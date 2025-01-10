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
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceApplication;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AllianceExternalController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceRepository $allianceRepository,
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly AllianceApplicationRepository $allianceApplicationRepository,
        private readonly MessageRepository $messageRepository,
        private readonly AllianceHistoryRepository $allianceHistoryRepository,
        private readonly AllianceService $service,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ConfigurationService $config,
        private readonly MessageCategoryRepository $messageCategoryRepository,
    )
    {
    }

    // show alliance infos
    #[Route('/game/alliance/info/{id}', name: 'game.alliance.info')]
    public function info(Alliance $infoAlliance): Response {
        $cu = $this->getUser()->getData();
        if ($cu->getAlliance() !== $infoAlliance) {
            $this->allianceRepository->addVisit($infoAlliance->getId(), true);
        }

        // Bündnisse
        $bnds = $this->allianceDiplomacyRepository->findBy(['level'=>AllianceDiplomacyLevel::BND_CONFIRMED],['date'=>'DESC'],15);

        // Kriege
        $wars = $this->allianceDiplomacyRepository->findBy(['level'=>AllianceDiplomacyLevel::WAR],['date'=>'DESC']);

        // Friedensabkommen
        $peace = $this->allianceDiplomacyRepository->findBy(['level'=>AllianceDiplomacyLevel::PEACE],['date'=>'DESC']);

        return $this->render('game/alliance/alliance_info.html.twig',[
            'allianceRepository' => $this->allianceRepository,
            'infoAlliance' => $this->allianceRepository->getAlliance($infoAlliance->getId()),
            'bnds' => $bnds,
            'wars' => $wars,
            'peace' => $peace
        ]);
    }

    // main alliance action
    #[Route('/game/alliance', name: 'game.alliance')]
    public function alliance(Request $request): Response {
        $cu = $this->getUser()->getData();
        if (!$cu->getAlliance()) {
            if($this->onCooldown()) {
                return $this->redirectToRoute('game.alliance.cooldown');
            }

            $application = $this->allianceApplicationRepository->findOneBy(['user'=>$cu->getId()]);
            $form = $this->createFormBuilder()
                ->add('cancel', SubmitType::class, ['label' => 'Bewerbung zurückziehen'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if($form->get('cancel')->isClicked()) {
                    $this->messageRepository->createSystemMessage($application->getAlliance()->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Bewerbung zurückgezogen", "Der Spieler " . $cu->getNick() . " hat die Bewerbung bei deiner Allianz zurückgezogen!");
                    $this->allianceHistoryRepository->addEntry($application->getAlliance(), "Der Spieler [b]" . $cu->getNick() . "[/b] zieht seine Bewerbung zurück.");
                    $this->allianceApplicationRepository->remove($application);
                    $this->allianceApplicationRepository->save();

                    //show cancel message
                    return $this->render('game/alliance/alliance_application_cancel.html.twig');
                }
            }

            //no alliance - show info
            return $this->render('game/alliance/alliance_no_alliance.html.twig',[
                'form' => $form,
                'application' => $application,
                'alliance' => $application?$this->allianceRepository->find($application->getAlliance()->getId()):null
            ]);
        }

        return $this->redirectToRoute('game.alliance.overview');
    }

    //action for creating new alliance
    #[Route('/game/alliance/create', name: 'game.alliance.create')]
    public function create(Request $request): Response {
        if($this->getUser()->getData()->getAlliance()) {
            return $this->redirectToRoute('game.alliance');
        }

        $form = $this->createFormBuilder()
            ->add('tag', TextType::class,[
                'attr'=> ['size'=>"6", 'maxlength'=>"6"]
            ])
            ->add('name', TextType::class,[
                'attr'=> ['size'=>"25", 'maxlength'=>"25"]
            ])
            ->add('create_submit', SubmitType::class, ['label' => 'Speichern'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $alliance = $this->service->create(
                    $form->getData()['tag'],
                    $form->getData()['name'],
                    $this->getUser()->getData()
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
        if($this->getUser()->getData()->getAlliance()) {
            return $this->redirectToRoute('game.alliance');
        }

        $alliances = $this->allianceRepository->getAlliancesAcceptingApplications();
        return $this->render('game/alliance/alliance_join.html.twig',[
            'alliances' => $alliances
        ]);
    }

    // application form action
    #[Route('/game/alliance/join/{id}', name: 'game.alliance.apply')]
    public function apply(Alliance $alliance, Request $request): Response {
        if($this->getUser()->getData()->getAlliance()) {
            return $this->redirectToRoute('game.alliance');
        }

        if($this->onCooldown()) {
            return $this->redirectToRoute('game.alliance.cooldown');
        }

        if ($alliance->isAcceptApplications()) {

            $application = new AllianceApplication();
            $form = $this->createFormBuilder($application)
                ->add('text', TextareaType::class, [
                    'attr' => ['rows' => "15", 'cols' => "80"],
                    'constraints'=> new NotBlank([
                        'message' => 'Du musst einen Bewerbungstext eingeben!',
                    ]),
                    'data'=>$alliance->getApplicationTemplate()
                ])
                ->add('submitApplication', SubmitType::class, ['label' => 'Senden'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->messageRepository->createSystemMessage($alliance->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Bewerbung", "Der Spieler " . $this->getUser()->getUserIdentifier() . " hat sich bei deiner Allianz beworben. Gehe auf die [".$this->generateUrl('game.alliance.applications')."]Allianzseite[/page] für Details!");
                $this->allianceHistoryRepository->addEntry($alliance, "Der Spieler [b]" . $this->getUser()->getUserIdentifier() . "[/b] bewirbt sich sich bei der Allianz.");

                $application->setAlliance($alliance);
                $application->setUser($this->getUser()->getData());
                $application->setTimestamp(time());

                $this->allianceApplicationRepository->persist($application);
                $this->allianceApplicationRepository->save();

                return $this->render('game/alliance/alliance_apply_finished.html.twig',['allianceName'=>$alliance->toString()]);
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

    private function onCooldown():bool
    {
        return time() < ($this->getUser()->getData()->getAllianceLeave() + $this->config->getInt("alliance_leave_cooldown"));
    }
}