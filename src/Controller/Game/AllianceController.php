<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyPoints;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceMemberCosts;
use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\Event\AllianceCreate;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Alliance\TownhallService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceApplication;
use EtoA\Entity\MessageData;
use EtoA\Entity\User;
use EtoA\Fleet\ForeignFleetLoader;
use EtoA\Form\Type\Core\AllianceApplicationType;
use EtoA\Form\Type\Core\AvatarUploadType;
use EtoA\Form\Type\Core\EditAllianceMemberType;
use EtoA\Form\Type\Core\MultiViewType;
use EtoA\Form\Type\Core\ProfileUploadType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRatingService;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
use function Symfony\Component\Translation\t;

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
        private readonly ConfigurationService $config,
        private readonly LogRepository $logRepository,
        private readonly UserService $userService,
        private readonly AllianceMemberCosts $allianceMemberCosts,
        private readonly PlanetRepository $planetRepository,
        private readonly ForeignFleetLoader       $foreignFleetLoader,
        private readonly EntityRepository $entityRepository,
        private readonly AllianceNewsRepository $allianceNewsRepository,
        private readonly UserRatingService $userRatingService,
        private readonly TownhallService $townhallService,
        private readonly MessageCategoryRepository $messageCategoryRepository,
        private readonly AllianceRankRepository $allianceRankRepository
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

        return $this->render('game/alliance/alliance_info.html.twig',[
            'allianceRepository' => $this->allianceRepository,
            'allianceDiplomacyRepository' => $this->allianceDiplomacyRepository,
            'infoAlliance' => $this->allianceRepository->getAlliance($infoAlliance->getId()),
            'userRepository' => $this->userRepository
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

    #[Route('/game/alliance/overview', name: 'game.alliance.overview')]
    public function overview(): Response {
        if(!$this->getUser()->getData()->getAlliance()) {
            return $this->redirectToRoute('game.alliance');
        }

        $alliance = $this->getUser()->getData()->getAlliance();
        $this->allianceRepository->addVisit($alliance->getId());

        return $this->render('game/alliance/alliance_overview.html.twig',[
            'overview' =>$this->service->renderOverview($alliance),
        ]);
    }

    #[Route('/game/alliance/members', name: 'game.alliance.members')]
    public function members(): Response {

        $members = [];
        foreach ($this->userRepository->findBy(['alliance'=>$this->getUser()->getData()->getAlliance()]) as $key => $member) {
            $planet = $this->planetRepository->findOneBy(['user'=>$member,'mainPlanet'=>true]);
            $entity = $this->entityRepository->find($planet->getId());

            $members[$key]['id'] = $member->getId();
            $members[$key]['nick'] = $member->getNick();
            $members[$key]['planet'] = $entity->coordinatesString().' '.$planet->getName();
            $members[$key]['points'] = $member->getPoints();
            $members[$key]['race'] = $member->getRace()->getName();
            $members[$key]['rank'] = $member->getAllianceRank()?->getName();
            $members[$key]['attacks'] = $this->foreignFleetLoader->getVisibleFleets($member->getId())->aggressiveCount;
            $members[$key]['online'] = !!$member->getActionTime();
            $members[$key]['lastLog'] = $member->getLastLogin()?date("d.m.Y H:i", $member->getLastLogin()):null;
        }

        return $this->render('game/alliance/alliance_members.html.twig',[
            'members' => $members
        ]);
    }

    #[Route('/game/alliance/applications', name: 'game.alliance.applications')]
    public function applications(Request $request): Response {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::APPLICATIONS, 'overview')) {
            $maxMemberCount = $this->config->getInt("alliance_max_member_count");
            $currentMemberCount = $this->userRepository->count(['alliance'=>$cu->getAlliance()]);
            $applications = $this->allianceApplicationRepository->findBy(['alliance'=>$cu->getAlliance()->getId()]);

            $form = $this->createFormBuilder(['applications' => $applications])
                ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
                ->add('applications', CollectionType::class, array(
                    'entry_type' => AllianceApplicationType::class,
                    'entry_options' => ['label' => false],
                ))
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $alliance = $cu->getAlliance();
                $newMemberCount = $currentMemberCount;

                foreach ($form->get('applications') as $application) {
                    $applicationUser = $application->getData()->getUser();
                    $nick = $applicationUser->getNick();

                    if ($application->get('action')->getData()) {
                        // Anfrage annehmen
                        if ($application->get('action')->getData() === 2) {
                            if ($maxMemberCount != 0 && $newMemberCount >= $maxMemberCount) {
                                $msg['error'][] = "Maximale Anzahl an Mitgliedern erreicht!";
                                break;
                            }

                            $newMemberCount++;
                            $msg['success'][] = $nick. " wurde angenommen.";

                            // Nachricht an den Bewerber schicken
                            $this->messageRepository->createSystemMessage($applicationUser, $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Bewerbung angenommen", "Deine Allianzbewerbung wurde angenommen!\n\n[b]Antwort:[/b]\n" . addslashes($application->get('answer')->getData()));

                            // Log schreiben
                            $this->allianceHistoryRepository->addEntry($alliance, "Die Bewerbung von [b]" . $nick . "[/b] wurde akzeptiert!");
                            $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Der Spieler [b]" . $nick . "[/b] tritt der Allianz [b]" . $alliance->toString() . "[/b] bei!");
                            $this->userService->addToUserLog($applicationUser, "alliance", "{nick} ist nun ein Mitglied der Allianz " . $alliance->getName() . ".");

                            // Speichern
                            $applicationUser->setAlliance($cu->getAlliance());
                            $this->allianceApplicationRepository->remove($application->getData());

                            $this->allianceApplicationRepository->save();
                        }
                        // Anfrage ablehnen
                        elseif ($application->get('action')->getData() === 1) {
                            $msg['success'][] = $nick . " wurde abgelehnt.";

                            // Nachricht an den Bewerber schicken
                            $this->messageRepository->createSystemMessage($applicationUser, $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Bewerbung abgelehnt", "Deine Allianzbewerbung wurde abgelehnt!\n\n[b]Antwort:[/b]\n" . addslashes($application->get('answer')->getData()));

                            // Log schreiben
                            $this->allianceHistoryRepository->addEntry($cu->getAlliance(), "Die Bewerbung von [b]" . $nick . "[/b] wurde abgelehnt!");

                            // Anfrage löschen
                            $this->allianceApplicationRepository->remove($application->getData());

                            $this->allianceApplicationRepository->save();
                        }
                        // Anfrage unbearbeitet lassen, jedoch Nachricht verschicken, wenn etwas geschrieben ist
                        else {
                            $text = str_replace(' ', '', $application->get('answer')->getData());
                            if ($text != '') {
                                // Nachricht an den Bewerber schicken
                                $this->messageRepository->createSystemMessage($applicationUser, $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Bewerbung: Nachricht", "Antwort auf die Bewerbung an die Allianz [b]" . $alliance->toString() . "[/b]:\n" . $application->get('answer')->getData());

                                $msg['success'][] = $nick . ": Nachricht gesendet";
                            }
                        }
                    }
                }

                if ($newMemberCount > $currentMemberCount) {
                    $this->allianceMemberCosts->increase($alliance, $currentMemberCount, $newMemberCount);
                }

                $msg['success'][] = "Änderungen übernommen";
            }

            return $this->render('game/alliance/alliance_applications.html.twig',[
                'applications' => $applications,
                'msg' => $msg??null,
                'form' => $form,
                'allowAccept' => $maxMemberCount == 0 || $currentMemberCount < $maxMemberCount
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/history', name: 'game.alliance.history')]
    public function history(): Response {
        $entries = $this->allianceHistoryRepository->findForAlliance($this->getUser()->getData()->getAlliance());

        return $this->render('game/alliance/alliance_history.html.twig',[
            'entries' =>$entries
        ]);
    }

    #[Route('/game/alliance/news', name: 'game.alliance.news')]
    public function news(Request $request): Response
    {
        $preview = false;

        $form = $this->createFormBuilder()
            ->add('title', TextType::class, [
                'attr' => [
                    'size'=>'62',
                    'maxlength => 255'
                ],
                'constraints' => new NotBlank(
                    ['message' => 'Nicht alle Felder ausgefüllt!']
                ),
            ])
            ->add('text', TextareaType::class, [
                'attr' => [
                    'rows'=>'18',
                    'cols' => '60'
                ],
                'constraints' => new NotBlank(
                    ['message' => 'Nicht alle Felder ausgefüllt!']
                ),
            ])
            ->add('target', ChoiceType::class, [
                'choices' => $this->allianceRepository->findAll(),
                'choice_label' => function (?Alliance $alliance): string {
                    return $alliance ? $alliance->toString() : '';
                },
                'choice_value' =>'id',
                'data' => $this->getUser()->getData()->getAlliance(),
                'placeholder' => 'Öffentliches Rathaus',
                'placeholder_attr' => [
                    'style' => 'font-weight:bold;color:#0f0;'
                ],
                'required' => false
            ])
            ->add('preview', SubmitType::class, ['label' => 'Vorschau'])
            ->add('send', SubmitType::class, ['label' => 'Senden'])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $preview = true;
            if($form->get('send')->isClicked()) {
                $cu = $this->getUser()->getData();
                $news = $this->allianceNewsRepository->add($cu, $cu->getAlliance(), $form->get('title')->getData(), $form->get('text')->getData(), $form->get('target')->getData());

                $msg['success'] = "News wurde gesendet!";

                // Gebe nur Punkte falls Nachricht öffentlich oder an andere Allianz
                if ($cu->getAlliance() !== $form->get('target')->getData()) {
                    $this->userRatingService->addDiplomacyRating(
                        $cu,
                        AllianceDiplomacyPoints::POINTS_PER_NEWS,
                        "Rathausnews verfasst (ID:" . $news->getId() . ", " . $form->get('text')->getData() . ")"
                    );
                }

                // Update rss file
                $this->townhallService->genRss();
            }
        }

        return $this->render('game/alliance/alliance_news.html.twig',[
            'form' =>$form,
            'preview' => $preview,
            'msg' => $msg??null
        ]);
    }

    #[Route('/game/alliance/massmail', name: 'game.alliance.massmail')]
    public function massmail(Request $request): Response
    {

        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::MASS_MAIL, 'alliance')) {

            $messageData = new MessageData();
            $form = $this->createFormBuilder($messageData)
                ->add('subject', TextType::class, [
                    'attr' => [
                        'size'=>'30',
                        'maxlength => 255'
                    ],
                    'constraints' => new NotBlank(
                        ['message' => 'Nicht alle Felder ausgefüllt!']
                    ),
                ])
                ->add('text', TextareaType::class, [
                    'attr' => [
                        'rows'=>'5',
                        'cols' => '50'
                    ],
                    'constraints' => new NotBlank(
                        ['message' => 'Nicht alle Felder ausgefüllt!']
                    ),
                ])
                ->add('send', SubmitType::class, ['label' => 'Senden'])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $allianceUsers = $this->userRepository->findBy(['alliance'=>$cu->getAlliance()]);
                if ($allianceUsers) {
                    foreach ($allianceUsers as $allianceUser) {
                        $this->messageRepository->sendFromUserToUser(
                            $cu,
                            $allianceUser,
                            $messageData,
                             $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE)
                        );
                    }

                    return $this->render('game/success.html.twig',[
                        'msg' => 'Nachricht wurde gesendet!',
                        'path' => $this->generateUrl('game.alliance.overview'),
                        'headline' => 'Allianz'
                    ]);
                } else {
                    return $this->render('game/error.html.twig',[
                        'msg' => 'Nachricht wurde nicht gesendet, keine Mitglieder vorhanden!',
                        'path' => $this->generateUrl('game.alliance.overview'),
                        'headline' => 'Allianz'
                    ]);
                }
            }

            return $this->render('game/alliance/alliance_massmail.html.twig',[
                'form' => $form
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/editmembers', name: 'game.alliance.editmembers')]
    public function editMembers(Request $request): Response
    {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);
        $members = $this->userRepository->findBy(['alliance'=>$cu->getAlliance()]);

        if ($userAlliancePermission->checkHasRights(AllianceRights::EDIT_MEMBERS, 'alliance')) {
            $form = $this->createFormBuilder(['members'=>$members])
                ->add('members', CollectionType::class, [
                    'entry_type'   => EditAllianceMemberType::class,
                    'entry_options' => ['label' => false],
                ])
                ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
                ->getForm()
                ->handleRequest($request);

            $wings = [];
            if ($this->config->getBoolean('allow_wings')) {
                $wings = $this->allianceRepository->findBy(['mother'=>$cu->getAlliance()]);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($form->get('members')->getData() as $member) {
                    if($this->userRepository->getChangeset($member)) {
                        $this->userRepository->save();
                        $this->allianceHistoryRepository->addEntry($cu->getAlliance(), "Der Spieler [b]" . $member->getNick() . "[/b] erhält den Rang [b]" . $member->getAllianceRank()->getName() . "[/b].");
                    }
                }
                return $this->render('game/success.html.twig',[
                    'msg' => "Änderungen wurden übernommen!",
                    'path' => $this->generateUrl('game.alliance.editmembers'),
                    'headline' => 'Allianz'
                ]);
            }

            return $this->render('game/alliance/alliance_edit_members.html.twig',[
                'form' => $form,
                'allianceMembers' => $members,
                'atWar' => $this->allianceDiplomacyRepository->isAtWar($cu->getAlliance()),
                'wings' => $wings
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    // Mitglied kicken
    #[Route('/game/alliance/editmembers/kick/{id}', name: 'game.alliance.editmembers.kick')]
    public function editMembersKick(?User $toBeKickedUser = null): Response
    {
        $currentAlliance = $this->getUser()->getData()->getAlliance();
        if (!$this->allianceDiplomacyRepository->isAtWar($currentAlliance)) {
            if ($toBeKickedUser && $toBeKickedUser->getAlliance() === $currentAlliance) {
                if ($this->service->kickMember($currentAlliance, $toBeKickedUser)) {
                    $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Der Spieler [b]" . $toBeKickedUser->getNick() . "[/b] wurde von [b]" . $this->getUser()->getData()->getNick() . "[/b] aus der Allianz [b]" . $currentAlliance->toString() . "[/b] ausgeschlossen!");
                    return $this->render('game/success.html.twig',[
                        'msg' => "Der Spieler [b]" . $toBeKickedUser->getNick() . "[/b] wurde aus der Allianz ausgeschlossen!",
                        'path' => $this->generateUrl('game.alliance.editmembers'),
                        'headline' => 'Allianz'
                    ]);
                } else {
                    return $this->render('game/error.html.twig',[
                        'msg' => 'Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da er in einem Allianzangriff unterwegs ist!',
                        'path' => $this->generateUrl('game.alliance.editmembers'),
                        'headline' => 'Allianz'
                    ]);
                }
            } else {
                return $this->render('game/error.html.twig',[
                    'msg' => 'Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da er kein Mitglieder dieser Allianz ist!',
                    'path' => $this->generateUrl('game.alliance.editmembers'),
                    'headline' => 'Allianz'
                ]);
            }
        }
    }

    // Mitglied kicken
    #[Route('/game/alliance/editmembers/leader/{id}', name: 'game.alliance.editmembers.leader')]
    public function editMembersLeader(?User $newFounder = null): Response
    {
        $currentAlliance = $this->getUser()->getData()->getAlliance();

        if(
            $newFounder &&
            $newFounder->getAlliance() === $currentAlliance &&
            $this->service->isFounder() &&
            $this->getUser()->getData() !== $newFounder
        ) {
            $this->service->changeFounder($currentAlliance, $newFounder);
            $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Der Spieler [b]" . $newFounder->getNick() . "[/b] wird vom Spieler [b]" . $this->getUser()->getData()->getNick() . "[/b] zum Gründer befördert.");

            return $this->render('game/success.html.twig',[
                'msg' => 'Gründer geändert!',
                'path' => $this->generateUrl('game.alliance.editmembers'),
                'headline' => 'Allianz'
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'User nicht gefunden!',
            'path' => $this->generateUrl('game.alliance.editmembers'),
            'headline' => 'Allianz'
        ]);
    }

    private function onCooldown():bool
    {
        return time() < ($this->getUser()->getData()->getAllianceLeave() + $this->config->getInt("alliance_leave_cooldown"));
    }
}