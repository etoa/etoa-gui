<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\Event\AllianceCreate;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Core\AvatarUploadType;
use EtoA\Form\Type\Core\ProfileUploadType;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AllianceController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceRepository $allianceRepository,
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly UserRepository $userRepository,
        private readonly AllianceApplicationRepository $allianceApplicationRepository,
        private readonly MessageRepository $messageRepository,
        private readonly AllianceHistoryRepository $allianceHistoryRepository,
        private readonly AllianceService $service,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ConfigurationService $config
    )
    {
    }

    // show alliance infos
    #[Route('/game/alliance/info/{id}', name: 'game.alliance.info')]
    public function info(int $id): Response {
        $infoAlliance = $this->allianceRepository->getAlliance($id);
        if ($infoAlliance !== null) {
            $cu = $this->getUser()->getData();
            if ($cu->getAllianceId() !== $infoAlliance->id) {
                $this->allianceRepository->addVisit($infoAlliance->id, true);
            }
        }

        return $this->render('game/alliance/alliance_info.html.twig',[
            'allianceRepository' => $this->allianceRepository,
            'allianceDiplomacyRepository' => $this->allianceDiplomacyRepository,
            'id' => $id,
            'userRepository' => $this->userRepository
        ]);
    }

    // main alliance action
    #[Route('/game/alliance', name: 'game.alliance')]
    public function alliance(Request $request): Response {
        $cu = $this->getUser()->getData();
        if ($cu->getAllianceId() === 0) {
            if($this->onCooldown()) {
                return $this->redirectToRoute('game.alliance.cooldown');
            }

            $application = $this->allianceApplicationRepository->getUserApplication($cu->getId());
            $form = $this->createFormBuilder()
                ->add('cancel', SubmitType::class, ['label' => 'Bewerbung zurückziehen'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if($form->get('cancel')->isClicked()) {
                    $alliance = $this->allianceRepository->getAlliance($application->allianceId);
                    $this->messageRepository->createSystemMessage($alliance->founderId, MessageCategoryId::ALLIANCE, "Bewerbung zurückgezogen", "Der Spieler " . $cu->getNick() . " hat die Bewerbung bei deiner Allianz zurückgezogen!");
                    $this->allianceHistoryRepository->addEntry($application->allianceId, "Der Spieler [b]" . $cu->getNick() . "[/b] zieht seine Bewerbung zurück.");
                    $this->allianceApplicationRepository->deleteApplication($cu->getId(), $application->allianceId);

                    //show cancel message
                    return $this->render('game/alliance/alliance_application_cancel.html.twig');
                }
            }

            //no alliance - show info
            return $this->render('game/alliance/alliance_no_alliance.html.twig',[
                'form' => $form,
                'application' => $application,
                'alliance' => $application?$this->allianceRepository->getAlliance($application->allianceId):null
            ]);
        }

        return $this->redirectToRoute('game.alliance.overview');
    }

    //action for creating new alliance
    #[Route('/game/alliance/create', name: 'game.alliance.create')]
    public function create(Request $request): Response {
        if($this->getUser()->getData()->getAllianceId()) {
            return $this->redirectToRoute('game.alliance');
        }

        $form = $this->createFormBuilder()
            ->add('alliance_tag', TextType::class,[
                'attr'=> ['size'=>"6", 'maxlength'=>"6"]
            ])
            ->add('alliance_name', TextType::class,[
                'attr'=> ['size'=>"25", 'maxlength'=>"25"]
            ])
            ->add('create_submit', SubmitType::class, ['label' => 'Speichern'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $alliance = $this->service->create(
                    $form->getData()['alliance_tag'],
                    $form->getData()['alliance_name'],
                    $this->getUser()->getId()
                );
                $msg['success'] = "Allianz [b]" . $alliance->toString() . "[/b] gegründet!";
                $finish = true;

                $this->dispatcher->dispatch(new AllianceCreate(), AllianceCreate::CREATE_SUCCESS);
            } catch (InvalidAllianceParametersException $ex) {
                $msg['error'] = $ex->getMessage();
            }
        }

        return $this->render('game/alliance/alliance_create.html.twig',[
            'form' => $form,
            'msg' => $msg??null,
            'finish' =>$finish??false
        ]);
    }

    //overview for all join able alliances
    #[Route('/game/alliance/join', name: 'game.alliance.join')]
    public function join(): Response {
        if($this->getUser()->getData()->getAllianceId()) {
            return $this->redirectToRoute('game.alliance');
        }

        $alliances = $this->allianceRepository->getAlliancesAcceptingApplications();

        return $this->render('game/alliance/alliance_join.html.twig',[
            'alliances' => $alliances
        ]);
    }

    // application form action
    #[Route('/game/alliance/join/{id}', name: 'game.alliance.apply')]
    public function apply(int $id, Request $request): Response {
        if($this->getUser()->getData()->getAllianceId()) {
            return $this->redirectToRoute('game.alliance');
        }

        if($this->onCooldown()) {
            return $this->redirectToRoute('game.alliance.cooldown');
        }

        $alliance = $this->allianceRepository->getAlliance($id);
        if ($alliance) {
            if ($alliance->acceptApplications) {
                $form = $this->createFormBuilder()
                    ->add('userAllianceApplication', TextareaType::class, [
                        'attr' => ['rows' => "15", 'cols' => "80"],
                        'constraints'=> new NotBlank([
                            'message' => 'Du musst einen Bewerbungstext eingeben!',
                        ]),
                        'data'=>$alliance->applicationTemplate
                    ])
                    ->add('submitApplication', SubmitType::class, ['label' => 'Senden'])
                    ->getForm();

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $this->messageRepository->createSystemMessage($alliance->founderId, MessageCategoryId::ALLIANCE, "Bewerbung", "Der Spieler " . $this->getUser()->getUserIdentifier() . " hat sich bei deiner Allianz beworben. Gehe auf die [".$this->generateUrl('game.alliance.applications')."]Allianzseite[/page] für Details!");
                    $this->allianceHistoryRepository->addEntry($id, "Der Spieler [b]" . $this->getUser()->getUserIdentifier() . "[/b] bewirbt sich sich bei der Allianz.");
                    $this->allianceApplicationRepository->addApplication($this->getUser()->getId(), $id, $form->getData()['userAllianceApplication']);

                    return $this->render('game/alliance/alliance_apply_finished.html.twig',['allianceName'=>$alliance->nameWithTag]);
                }
            }
        }

        return $this->render('game/alliance/alliance_apply.html.twig',[
            'msg' => $msg??null,
            'alliance'=>$alliance,
            'form' =>$form??null
        ]);
    }

    //redirect to this action when user can't join because of cooldown
    #[Route('/game/alliance/cooldown', name: 'game.alliance.cooldown')]
    public function cooldown(): Response {
        if($this->onCooldown())
            return $this->render('game/alliance/alliance_cooldown.html.twig');
        return $this->render('game/alliance/alliance_no_alliance.html.twig');
    }

    #[Route('/game/alliance/overview', name: 'game.alliance.overview')]
    public function overview(Request $request): Response {
        if(!$this->getUser()->getData()->getAllianceId()) {
            return $this->redirectToRoute('game.alliance');
        }

        $alliance = $this->allianceRepository->getAlliance($this->getUser()->getData()->getAllianceId());
        $this->allianceRepository->addVisit($alliance->id);

        return $this->render('game/alliance/alliance_overview.html.twig',[
            'overview' =>$this->service->renderOverview($alliance),
        ]);
    }

    #[Route('/game/alliance/applications', name: 'game.alliance.applications')]
    public function applications(int $id, Request $request): Response {

    }

    private function onCooldown():bool
    {
        return time() < ($this->getUser()->getData()->getAllianceLeave() + $this->config->getInt("alliance_leave_cooldown"));
    }
}