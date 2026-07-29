<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceDiplomacyPoints;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceImage;
use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Alliance\TownhallService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceRank;
use EtoA\Entity\MessageData;
use EtoA\Entity\User;
use EtoA\Fleet\ForeignFleetService;
use EtoA\Form\Type\Core\AllianceRankType;
use EtoA\Form\Type\Core\AllianceUploadType;
use EtoA\Form\Type\Core\EditAllianceMemberType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Support\FileUtils;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRatingService;
use EtoA\User\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class AllianceInternalController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceRepository $allianceRepository,
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly UserRepository                $userRepository,
        private readonly MessageRepository             $messageRepository,
        private readonly AllianceHistoryRepository     $allianceHistoryRepository,
        private readonly AllianceService               $service,
        private readonly ConfigurationService          $config,
        private readonly LogRepository                 $logRepository,
        private readonly PlanetRepository              $planetRepository,
        private readonly ForeignFleetService           $foreignFleetLoader,
        private readonly EntityRepository              $entityRepository,
        private readonly AllianceNewsRepository        $allianceNewsRepository,
        private readonly UserRatingService             $userRatingService,
        private readonly TownhallService               $townhallService,
        private readonly MessageCategoryRepository     $messageCategoryRepository,
        private readonly AllianceRankRepository        $allianceRankRepository,
        private readonly FileUtils                     $fileUtils,
    )
    {
    }

    // show alliance infos
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
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::VIEW_MEMBERS, 'alliance')) {
            $members = [];
            foreach ($this->userRepository->findBy(['alliance' => $this->getUser()->getData()->getAlliance()]) as $key => $member) {
                $planet = $this->planetRepository->findOneBy(['user' => $member, 'mainPlanet' => true]);
                $entity = $this->entityRepository->find($planet->getId());

                $members[$key]['id'] = $member->getId();
                $members[$key]['nick'] = $member->getNick();
                $members[$key]['planet'] = $entity->coordinatesString() . ' ' . $planet->getName();
                $members[$key]['points'] = $member->getPoints();
                $members[$key]['race'] = $member->getRace()->getName();
                $members[$key]['rank'] = $member->getAllianceRank()?->getName();
                $members[$key]['attacks'] = $this->foreignFleetLoader->getVisibleFleets($member->getId())->aggressiveCount;
                $members[$key]['online'] = !!$member->getActionTime();
                $members[$key]['lastLog'] = $member->getLastLogin() ? date("d.m.Y H:i", $member->getLastLogin()) : null;
            }

            return $this->render('game/alliance/alliance_members.html.twig', [
                'members' => $members
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/applications', name: 'game.alliance.applications')]
    public function applications(): Response {
        $cu = $this->getUser()->getData();
        if (!$cu->getAlliance()) {
            return $this->redirectToRoute('game.alliance');
        }

        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::APPLICATIONS, 'alliance')) {
            return $this->render('game/alliance/alliance_applications.html.twig');
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/history', name: 'game.alliance.history')]
    public function history(): Response {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::HISTORY, 'alliance')) {
            $entries = $this->allianceHistoryRepository->findForAlliance($this->getUser()->getData()->getAlliance());

            return $this->render('game/alliance/alliance_history.html.twig',[
                'entries' =>$entries
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/news', name: 'game.alliance.news')]
    public function news(Request $request): Response
    {
        $preview = false;

        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::ALLIANCE_NEWS, 'alliance')) {
            $form = $this->createFormBuilder()
                ->add('title', TextType::class, [
                    'attr' => [
                        'size' => '62',
                        'maxlength => 255'
                    ],
                    'constraints' => new NotBlank(
                        ['message' => 'Nicht alle Felder ausgefüllt!']
                    ),
                ])
                ->add('text', TextareaType::class, [
                    'attr' => [
                        'rows' => '18',
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
                    'choice_value' => 'id',
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
                if ($form->get('send')->isClicked()) {
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

            return $this->render('game/alliance/alliance_news.html.twig', [
                'form' => $form,
                'preview' => $preview,
                'msg' => $msg ?? null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
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

        return $this->render('game/error.html.twig',[
            'msg' => 'Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da sie sich im Krieg befindet!',
            'path' => $this->generateUrl('game.alliance.editmembers'),
            'headline' => 'Allianz'
        ]);
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

    // Mitglied kicken
    #[Route('/game/alliance/ranks', name: 'game.alliance.ranks')]
    public function ranks(Request $request): Response
    {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::RANKS, 'alliance')) {
            $ranks = $this->allianceRankRepository->findBy(['alliance'=>$this->getUser()->getData()->getAlliance()],['level'=>'DESC']);
            $form = $this->createFormBuilder(['ranks'=>$ranks])
                ->add('ranks', CollectionType::class, [
                    'entry_type'   => AllianceRankType::class,
                    'entry_options' => ['label' => false],
                ])
                ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
                ->add('new', SubmitType::class, ['label' => 'Neuer Rang'])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if($form->get('new')->isClicked()) {
                    $newRank = new AllianceRank();
                    $newRank->setAlliance($this->getUser()->getData()->getAlliance());
                    $this->allianceRankRepository->persist($newRank);
                    $this->allianceRankRepository->save();

                    return $this->redirectToRoute('game.alliance.ranks');
                }

                if($form->get('save')->isClicked()) {
                    foreach ($form->get('ranks')->all() as $element) {
                        if($element->get('delete')->getData()) {
                            $this->allianceRankRepository->remove($element->getData());
                        }

                        $this->allianceRankRepository->save();

                        return $this->render('game/success.html.twig',[
                            'msg' => 'Gründer geändert!',
                            'path' => $this->generateUrl('game.alliance.ranks'),
                            'headline' => 'Allianz'
                        ]);
                    }
                }
            }

            return $this->render('game/alliance/alliance_ranks.html.twig',[
                'form' => $form
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/edit', name: 'game.alliance.edit')]
    public function editData(Request $request): Response
    {
        $cu = $this->getUser()->getData();
        $alliance = $cu->getAlliance();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($alliance, $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::EDIT_DATA, 'alliance')) {
            $msg['error'] = '';

            $form = $this->createFormBuilder($alliance)
                ->add('tag', TextType::class, [
                    'attr' => [
                        'size'=>6,
                        'maxlength' => 6
                    ],
                    'constraints' => new NotBlank(
                        ['message' => 'Nicht alle Felder ausgefüllt!']
                    ),
                ])
                ->add('name', TextType::class, [
                    'attr' => [
                        'size'=>25,
                        'maxlength' => 25
                    ],
                    'constraints' => new NotBlank(
                        ['message' => 'Nicht alle Felder ausgefüllt!']
                    ),
                ])
                ->add('text', TextareaType::class, [
                    'attr' => [
                        'rows' => 25,
                        'cols' => 70
                    ],
                    'required' => false
                ])
                ->add('url', TextType::class, [
                    'attr' => [
                        'size'=>40,
                        'maxlength' => 255
                    ],
                    'required' => false
                ])
                ->add('delete', CheckboxType::class, [
                    'label'    => false,
                    'mapped' => false
                ])
                ->add('image', AllianceUploadType::class)
                ->add('acceptApplications', ChoiceType::class, [
                    'expanded' => true,
                    'label'    => false,
                    'choices'  => [
                        'Ja' => true,
                        'Nein' => false,
                    ],
                ])
                ->add('acceptBnd', ChoiceType::class, [
                    'expanded' => true,
                    'label'    => false,
                    'choices'  => [
                        'Ja' => true,
                        'Nein' => false,
                    ],
                ])
                ->add('publicMemberlist', ChoiceType::class, [
                    'expanded' => true,
                    'label'    => false,
                    'choices'  => [
                        'Ja' => true,
                        'Nein' => false,
                    ],
                ])
                ->add('save', SubmitType::class, ['label' => 'Speichern'])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $changeset = $this->allianceRepository->getChangeset($alliance);

                if(array_key_exists('tag',$changeset) || array_key_exists('name',$changeset)) {
                    try {
                        // Prüft Korrektheit des Allianztags und Namen
                        $this->service->checkRename($alliance);
                    } catch (InvalidAllianceParametersException $ex) {
                        $msg['error'] = $ex->getMessage();
                    }
                }

                if(!array_key_exists('error',$msg)) {
                    if ($form->get('delete')->getData()) {
                        $alliance->setImage(null);
                    } elseif ($form->get('image')->getData()) {
                        if ($file = $this->fileUtils->uploadImage(
                            $form->get('image')->getData(),
                            $this->getParameter('kernel.project_dir') . AllianceImage::IMAGE_PATH,
                            [AllianceImage::IMAGE_WIDTH, AllianceImage::IMAGE_HEIGHT],
                            $msg['error']
                        )) {
                            $alliance->setImage($file->getFilename());
                        }
                    }

                    $this->allianceHistoryRepository->addEntry($alliance, "[b]" . $cu->getNick() . "[/b] ändert den Allianzname und/oder Tag von [b]" . $alliance->getName() . " (" . $alliance->getTag() . ")[/b] in [b]" . $alliance->getName() . " (" . $alliance->getTag() . ")[/b]!");
                    $msg['success'] = 'Die Änderungen wurden übernommen!';
                    $this->allianceRepository->save();
                }
            }

            return $this->render('game/alliance/alliance_editdata.html.twig',[
                'form' => $form,
                'msg' =>$msg
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/applicationtemplate', name: 'game.alliance.applicationtemplate')]
    public function editTemplate(Request $request): Response
    {
        $cu = $this->getUser()->getData();
        $alliance = $cu->getAlliance();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($alliance, $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::APPLICATION_TEMPLATE, 'alliance')) {
            $form = $this->createFormBuilder($alliance)
                ->add('applicationTemplate', TextareaType::class, [
                    'attr' => [
                        'rows' => 15,
                        'cols' => 60
                    ],
                    'required' => false
                ])
                ->add('save', SubmitType::class, ['label' => 'Speichern'])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->allianceRepository->save();
                $msg['success'] = 'Die Änderungen wurden übernommen!';
            }

            return $this->render('game/alliance/alliance_applicationtemplate.html.twig',[
                'form' => $form,
                'msg' =>$msg ?? null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/disband', name: 'game.alliance.disband')]
    public function disband(Request $request): Response
    {
        $cu = $this->getUser()->getData();
        $alliance = $cu->getAlliance();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($alliance, $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::LIQUIDATE, 'alliance')) {
            if($this->userRepository->count(['alliance'=>$alliance]) === 1) {
                $form = $this->createFormBuilder()
                    ->add('disband', SubmitType::class, ['label' => 'ja'])
                    ->getForm()
                    ->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    if(!$this->allianceDiplomacyRepository->isAtWar($alliance)) {
                        $this->service->delete($alliance, $cu);

                        return $this->render('game/success.html.twig',[
                            'msg' => 'Die Allianz wurde aufgelöst!',
                            'path' => $this->generateUrl('game.overview'),
                            'headline' => 'Allianz'
                        ]);
                    }

                    return $this->render('game/error.html.twig',[
                        'msg' => 'Allianz kann nicht aufgelöst werden, da sie sich im Krieg befindet!',
                        'path' => $this->generateUrl('game.alliance.overview'),
                        'headline' => 'Allianz'
                    ]);
                }

                return $this->render('game/alliance/alliance_disband.html.twig',[
                    'form' => $form,
                ]);
            }

            return $this->render('game/error.html.twig',[
                'msg' => 'Allianz kann nicht aufgelöst werden, da sie noch Mitglieder hat. Lösche zuerst die Mitglieder!',
                'path' => $this->generateUrl('game.alliance.overview'),
                'headline' => 'Allianz'
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }
}