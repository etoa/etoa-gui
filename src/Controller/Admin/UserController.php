<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use DateTime;
use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Design\DesignsService;
use EtoA\Entity\User;
use EtoA\Entity\UserComment;
use EtoA\Entity\UserMulti;
use EtoA\Entity\UserSitting;
use EtoA\Form\Request\Admin\UserCreateRequest;
use EtoA\Form\Request\Admin\UserLogEntryRequest;
use EtoA\Form\Type\Admin\ManualUserLogEntryType;
use EtoA\Form\Type\Admin\UserCreateType;
use EtoA\Form\Type\Core\UserPropertiesType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Help\TicketSystem\TicketStatus;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\GameLogSearch;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ranking\UserBannerService;
use EtoA\Ship\ShipDataRepository;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Support\ExternalUrl;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserCommentRepository;
use EtoA\User\UserHolidayService;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserLogRepository;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use EtoA\User\UserSessionLogRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSittingRepository;
use EtoA\User\UserWarningRepository;
use Exception;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;
use WhichBrowser\Parser as BrowserParser;
use EtoA\Support\ValidationUtils;

class UserController extends AbstractAdminController
{
    public function __construct(
        private readonly ConfigurationService       $config,
        private readonly UserService                $userService,
        private readonly LogRepository              $logRepository,
        private readonly UserRepository             $userRepository,
        private readonly UserMultiRepository        $userMultiRepository,
        private readonly UserSittingRepository      $userSittingRepository,
        private readonly UserWarningRepository      $userWarningRepository,
        private readonly AdminUserRepository        $adminUserRepo,
        private readonly NetworkNameService         $networkNameService,
        private readonly UserCommentRepository      $userCommentRepository,
        private readonly TicketRepository           $ticketRepo,
        private readonly RaceDataRepository         $raceRepository,
        private readonly SpecialistDataRepository   $specialistRepository,
        private readonly AllianceRepository         $allianceRepository,
        private readonly UserBannerService          $userBannerService,
        private readonly UserLoginFailureRepository $userLoginFailureRepository,
        private readonly GameLogRepository          $gameLogRepository,
        private readonly MessageRepository          $messageRepository,
        private readonly UserLogRepository          $userLogRepository,
        private readonly UserPointsRepository       $userPointsRepository,
        private readonly string                     $projectDir,
        private readonly MessageCategoryRepository  $messageCategoryRepository,
        private readonly UserSessionLogRepository   $userSessionLogRepository,
        private readonly UserSessionRepository      $userSessionRepository,
        private readonly UserHolidayService         $userHolidayService,
    )
    {
    }

    #[Route('/admin/users/new', name: 'admin.users.new')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function new(Request $request): Response
    {
        $createUserRequest = new UserCreateRequest();
        $form = $this->createForm(UserCreateType::class, $createUserRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $this->userService->register($createUserRequest->getName(), $createUserRequest->getEmail(), $createUserRequest->getNick(), $createUserRequest->getPassword(), $createUserRequest->getRace(), $createUserRequest->isGhost(), true);
                $this->logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->getNick() . " (" . $user->getName() . ", " . $user->getEmail() . ") wurde registriert!");
                $this->addFlash('success', 'Spieler erstellt');

                return $this->redirectToRoute('admin.users.view', ['id' => $user->getId()]);
            } catch (Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/{id}', name: 'admin.users.view')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function view(Request $request, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $newTickets = $this->ticketRepo->findBy([
            "user" => $user,
            "status" => TicketStatus::NEW->value,
        ]);
        $assignedTickets = $this->ticketRepo->findBy([
            "user" => $user,
            "status" => TicketStatus::ASSIGNED->value,
        ]);

        $bannerPath = $this->userBannerService->getUserBannerPath($user->getId());

        $userSession = $this->userSessionRepository->findOneBy(['user' => $user]);
        if (!$userSession) {
            $userSession = $this->userSessionLogRepository->findOneBy(['user' => $user], ['id' => 'DESC']);
        }

        $form = $this->createFormBuilder($user)
            ->add('cancelDelete', SubmitType::class, [
                'label' => 'Löschantrag aufheben',
                'attr' => [
                    'class' => 'userDeletedColor'
                ]
            ])
            ->add('verify', SubmitType::class, [
                'label' => 'Freischalten',
                'attr' => [
                    'class' => 'button'
                ]
            ])
            ->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('cancelDelete')->isClicked()) {
                $this->userRepository->markDeleted($user, 0);
                $this->addFlash('success', "Löschantrag aufgehoben!");
            }

            if ($form->get('verify')->isClicked()) {
                $this->userRepository->setVerified($user, true);
                $this->addFlash('success', "Account freigeschaltet!");
            }
        }

        return $this->render('admin/user/view.html.twig', [
            'user' => $user,
            'agent' => (new BrowserParser($userSession?->getUserAgent()))->toString(),
            'host' => $this->networkNameService->getHost($userSession?->getIpAddr()),
            'isBlocked' => $user->getBlockedFrom() > 0 && $user->getBlockedTo() > time(),
            'commentInfo' => $this->userCommentRepository->getCommentInformation($user),
            'activeMultisCount' => count($this->userMultiRepository->getUserEntries($user, true)),
            'activeSitting' => $this->userSittingRepository->getActiveUserEntry($user),
            'numberOfNewTickets' => count($newTickets),
            'numberOfAssignedTickets' => count($assignedTickets),
            'warning' => $this->userWarningRepository->getCountAndLatestWarning($user),
            'bannerPath' => file_exists($bannerPath) ? $bannerPath : null,
            'bannerTime' => file_exists($bannerPath) ? filemtime($bannerPath) : 0,
            'userBannerWebsiteLink' => ExternalUrl::USERBANNER_LINK,
            'userBannerLink' => $this->config->get('roundurl') . '/' . $bannerPath,
            'rating' => $user->getUserRating(),
            'session' => $userSession,
            'form' => $form
        ]);
    }

    #[Route('/admin/users/{id}/edit', name: 'admin.users.edit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function edit(Request $request, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $form = $this->createFormBuilder($user)
            ->add('nick', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'constraints' => new NotBlank(
                    ['message' => 'Du musst einen Nick eingeben!']
                ),
            ])
            ->add('email', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'constraints' => new NotBlank(
                    ['message' => 'Du musst eine Email eingeben!']
                ),
            ])
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'constraints' => new NotBlank(
                    ['message' => 'Du musst einen Namen eingeben!']
                ),
            ])
            ->add('emailFix', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'constraints' => new NotBlank(
                    ['message' => 'Du musst eine Email eingeben!']
                ),
            ])
            ->add('dualName', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'required' => false
            ])
            ->add('dualEmail', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'required' => false
            ])
            ->add('profileBoardUrl', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'required' => false
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'hash_property_path' => 'password',
                'required' => false,
                'label' => false
            ])
            ->add('ghost', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'expanded' => true,
                'label' => false
            ])
            ->add('chatAdmin', ChoiceType::class, [
                'choices' => [
                    'Ja' => 1,
                    'Nein' => 0,
                    'Leiter Team Community' => 2,
                    'Entwickler mit Adminrechten' => 3
                ],
                'expanded' => true,
                'label' => false
            ])
            ->add('admin', ChoiceType::class, [
                'choices' => [
                    'Ja' => 1,
                    'Nein' => 0,
                    'Entwickler ohne Adminrechte' => 2
                ],
                'expanded' => true,
                'label' => false,
            ])
            ->add('npc', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'expanded' => true,
                'label' => false,
            ])
            ->add('blocked', ChoiceType::class, [
                'choices' => [
                    'Ja' => 1,
                    'Nein' => 0,
                ],
                'expanded' => true,
                'mapped' => false,
                'choice_attr' => function () {
                    return ['onclick' => "this.value == 1 ? $('#ban_options').show() : $('#ban_options').hide()"];
                },
                'label' => false,
                'data' => !$user->getBlockedFrom() == 0
            ])
            ->add('blockedFrom', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'timestamp'
            ])
            ->add('blockedTo', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'timestamp'
            ])
            ->add('banReason', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'rows' => 2,
                    'cols' => 45,
                ],
                'required' => false
            ])
            ->add('banAdmin', ChoiceType::class, [
                'label' => false,
                'choices' => $this->adminUserRepo->searchNicknames(),
                'choice_value' => 'id',
                'choice_label' => 'nick',
                'placeholder' => '(Niemand)',
                'required' => false
            ])
            ->add('hmode', ChoiceType::class, [
                'choices' => [
                    'Ja' => 1,
                    'Nein' => 0,
                ],
                'expanded' => true,
                'mapped' => false,
                'choice_attr' => function () {
                    return ['onclick' => "this.value == 1 ? $('#umod_options').show() : $('#umod_options').hide()"];
                },
                'label' => false,
                'data' => !$user->getHmodFrom() == 0
            ])
            ->add('hmodFrom', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'timestamp',
                'required' => false
            ])
            ->add('hmodTo', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'timestamp',
                'required' => false
            ])
            ->add('race', ChoiceType::class, [
                'required' => false,
                'choices' => $this->raceRepository->getRaceNames(),
                'choice_value' => 'id',
                'choice_label' => 'name',
                'placeholder' => '(Keine)',
                'label' => false,
            ])
            ->add('avatarDel', CheckboxType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false
            ])
            ->add('imageDel', CheckboxType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('specialist', ChoiceType::class, [
                'required' => false,
                'choices' => $this->specialistRepository->getSpecialistNames(),
                'choice_value' => 'id',
                'choice_label' => 'name',
                'placeholder' => '(Keiner)',
                'label' => false,
                'attr' => [
                    'onchange' => "let spt=$('#spt'); this.value > 0 ? spt.show() : spt.hide()"
                ],
            ])
            ->add('specialistTime', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'timestamp'
            ])
            ->add('alliance', ChoiceType::class, [
                'required' => false,
                'choices' => $this->allianceRepository->getAllianceNames(),
                'choice_value' => 'id',
                'choice_label' => 'name',
                'placeholder' => '(Keine)',
                'label' => false,
            ])
            ->add('userProperties', UserPropertiesType::class, ['label' => false])
            ->add('allianceShipPoints', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'required' => false
            ])
            ->add('allianceShipPointsUsed', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'required' => false
            ])
            ->add('multiDelets', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'required' => false
            ])
            ->add('sittingDays', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 35,
                    'maxlength' => 250
                ],
                'required' => false
            ])
            ->add('userChangedMainPlanet', ChoiceType::class, [
                'choices' => [
                    'Ja' => 1,
                    'Nein' => 0,
                ],
                'expanded' => true,
                'label' => false,
            ])
            ->add('profileText', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'rows' => 2,
                    'cols' => 45,
                ],
                'required' => false
            ])
            ->add('signature', TextareaType::class, [
                'attr' => ['cols' => 50, 'rows' => 4],
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Speichern'])
            ->add('cancelDelete', SubmitType::class, [
                'label' => 'Löschantrag aufheben',
                'attr' => [
                    'class' => 'userDeletedColor'
                ]
            ])
            ->add('requestDelete', SubmitType::class, [
                'label' => 'Löschantrag erteilen',
                'attr' => [
                    'class' => 'userDeletedColor'
                ]
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'User löschen',
                'attr' => [
                    'onClick' => 'return confirm("Soll dieser User endgültig gelöscht werden?");',
                    'class' => 'remove'
                ]
            ])
            ->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('cancelDelete')->isClicked()) {
                $this->userRepository->markDeleted($user, 0);
                $this->addFlash('success', "Löschantrag aufgehoben!");

                return $this->redirectToRoute('admin.users.view', ['id' => $user->getId()]);
            }

            if ($form->get('delete')->isClicked()) {
                try {
                    $this->userService->delete($user, false, $this->getUser()->getUserIdentifier());
                    $this->addFlash('success', 'Löschung erfolgreich!');
                } catch (Exception $ex) {
                    $this->addFlash('error', $ex->getMessage());
                }
                return $this->redirectToRoute('admin.users');
            }

            if ($form->get('requestDelete')->isClicked()) {
                $t = time() + ($this->config->getInt('user_delete_days') * 3600 * 24);
                $this->userRepository->markDeleted($user, $t);
                $this->addFlash('success', "Löschantrag gespeichert!");

                return $this->redirectToRoute('admin.users.view', ['id' => $user->getId()]);
            }

            $changeset = $this->userRepository->getChangeset($user);
            if (array_key_exists('nick', $changeset)) {
                $this->userService->addToUserLog($user, "settings", $changeset['nick'][0] . "hat seinen Namen zu " . $changeset['nick'][0] . " geändert.");
            }

            if (!$form->get('hmode')->getData() && $user->getHmodTo() > 0) {
                $this->userHolidayService->deactivateHolidayMode($user,true);
            }

            // Handle profile image
            if ($form->get('imageDel')->getData()) {
                $existingProfileImage = $this->projectDir . $this->userService->buildProfileImageUrl($user->getProfileImage());
                if (file_exists($existingProfileImage)) {
                    unlink($existingProfileImage);
                }

                $user->setProfileImage('');
            }

            // Handle avatar
            if ($form->get('avatarDel')->getData()) {
                $existingAvatarPath = $this->projectDir . $this->userService->buildAvatarUrl($user->getAvatar());
                if (file_exists($existingAvatarPath)) {
                    unlink($existingAvatarPath);
                }
                $user->setAvatar('');
            }

            // Handle password
            if (array_key_exists('password', $changeset)) {
                $this->addFlash('success', "Das Passwort wurde geändert!");

                $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $this->getUser()->getUserIdentifier() . " ändert das Passwort von " . $user->getNick());
            }

            $this->userRepository->save();
            $this->addFlash('success', "Änderungen wurden übernommen!");
        }

        return $this->render('admin/user/edit.html.twig', [
            'isNoLongerBlocked' => $user->getBlockedFrom() > 0 && $user->getBlockedTo() < time(),
            'userBlockedDefaultTime' => time() + (3600 * 24 * $this->config->getInt('user_ban_min_length')),
            'holidayModeExpired' => $user->getHmodFrom() > 0 && $user->getHmodTo() < time(),
            'userHolidayModeDefaultTime' => time() + (3600 * 24 * $this->config->getInt('user_umod_min_length')),
            'form' => $form
        ]);
    }

    #[Route('/admin/users/{id}/economy', name: 'admin.users.economy')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function economy(?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $userPlanets = $user->getPlanets();

        // Rohstoffe/Bewohner und Speicher
        if (count($userPlanets) > 0) {
            $max_res = array(0, 0, 0, 0, 0, 0);
            $min_res = array(9999999999, 9999999999, 9999999999, 9999999999, 9999999999, 9999999999);
            $tot_res = array(0, 0, 0, 0, 0, 0);

            $max_prod = array(0, 0, 0, 0, 0, 0);
            $min_prod = array(9999999999, 9999999999, 9999999999, 9999999999, 9999999999, 9999999999);
            $tot_prod = array(0, 0, 0, 0, 0, 0);
            $val_res = [];
            $val_prod = [];
            $val_store = [];
            $val_time = [];
            foreach ($userPlanets as $planet) {
                //Speichert die aktuellen Rohstoffe in ein Array
                $val_res[$planet->getId()][0] = floor($planet->getResMetal());
                $val_res[$planet->getId()][1] = floor($planet->getResCrystal());
                $val_res[$planet->getId()][2] = floor($planet->getResPlastic());
                $val_res[$planet->getId()][3] = floor($planet->getResFuel());
                $val_res[$planet->getId()][4] = floor($planet->getResFood());
                $val_res[$planet->getId()][5] = floor($planet->getPeople());

                for ($x = 0; $x < 6; $x++) {
                    $max_res[$x] = max($max_res[$x], $val_res[$planet->getId()][$x]);
                    $min_res[$x] = min($min_res[$x], $val_res[$planet->getId()][$x]);
                    $tot_res[$x] += $val_res[$planet->getId()][$x];
                }

                //Speichert die aktuellen Rohstoffproduktionen in ein Array
                $val_prod[$planet->getId()][0] = floor($planet->getProdMetal());
                $val_prod[$planet->getId()][1] = floor($planet->getStoreCrystal());
                $val_prod[$planet->getId()][2] = floor($planet->getProdPlastic());
                $val_prod[$planet->getId()][3] = floor($planet->getProdFuel());
                $val_prod[$planet->getId()][4] = floor($planet->getProdFood());
                $val_prod[$planet->getId()][5] = floor($planet->getProdPeople());

                for ($x = 0; $x < 6; $x++) {
                    $max_prod[$x] = max($max_prod[$x], $val_prod[$planet->getId()][$x]);
                    $min_prod[$x] = min($min_prod[$x], $val_prod[$planet->getId()][$x]);
                    $tot_prod[$x] += $val_prod[$planet->getId()][$x];
                }

                //Speichert die aktuellen Speicher in ein Array
                $val_store[$planet->getId()][0] = floor($planet->getStoreMetal());
                $val_store[$planet->getId()][1] = floor($planet->getStoreCrystal());
                $val_store[$planet->getId()][2] = floor($planet->getStorePlastic());
                $val_store[$planet->getId()][3] = floor($planet->getStoreFuel());
                $val_store[$planet->getId()][4] = floor($planet->getStoreFood());
                $val_store[$planet->getId()][5] = floor($planet->getPeoplePlace());

                //Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)

                //Titan
                if ($planet->getProdMetal() > 0) {
                    if ($planet->getStoreMetal() - $planet->getResMetal() > 0) {
                        $val_time[$planet->getId()][0] = ceil(($planet->getStoreMetal() - $planet->getResMetal()) / $planet->getProdMetal() * 3600);
                    } else {
                        $val_time[$planet->getId()][0] = 0;
                    }
                } else {
                    $val_time[$planet->getId()][0] = 0;
                }

                //Silizium
                if ($planet->getProdCrystal() > 0) {
                    if ($planet->getStoreCrystal() - $planet->getResCrystal() > 0) {
                        $val_time[$planet->getId()][1] = ceil(($planet->getStoreCrystal() - $planet->getResCrystal()) / $planet->getProdCrystal() * 3600);
                    } else {
                        $val_time[$planet->getId()][1] = 0;
                    }
                } else {
                    $val_time[$planet->getId()][1] = 0;
                }

                //PVC
                if ($planet->getProdPlastic() > 0) {
                    if ($planet->getStorePlastic() - $planet->getResPlastic() > 0) {
                        $val_time[$planet->getId()][2] = ceil(($planet->getStorePlastic() - $planet->getResPlastic()) / $planet->getProdPlastic() * 3600);
                    } else {
                        $val_time[$planet->getId()][2] = 0;
                    }
                } else {
                    $val_time[$planet->getId()][2] = 0;
                }

                //Tritium
                if ($planet->getProdFuel() > 0) {
                    if ($planet->getStoreFuel() - $planet->getResFuel() > 0) {
                        $val_time[$planet->getId()][3] = ceil(($planet->getStoreFuel() - $planet->getResFuel()) / $planet->getProdFuel() * 3600);
                    } else {
                        $val_time[$planet->getId()][3] = 0;
                    }
                } else {
                    $val_time[$planet->getId()][3] = 0;
                }

                //Nahrung
                if ($planet->getProdFood() > 0) {
                    if ($planet->getStoreFood() - $planet->getResFood() > 0) {
                        $val_time[$planet->getId()][4] = ceil(($planet->getStoreFood() - $planet->getResFood()) / $planet->getProdFood() * 3600);
                    } else {
                        $val_time[$planet->getId()][4] = 0;
                    }
                } else {
                    $val_time[$planet->getId()][4] = 0;
                }

                //Bewohner
                if ($planet->getProdPeople() > 0) {
                    if ($planet->getPeoplePlace() - $planet->getPeople() > 0) {
                        $val_time[$planet->getId()][5] = ceil(($planet->getPeoplePlace() - $planet->getPeople()) / $planet->getProdPeople() * 3600);
                    } else {
                        $val_time[$planet->getId()][5] = 0;
                    }
                } else {
                    $val_time[$planet->getId()][5] = 0;
                }
            }
        }

        // Rohstoffproduktion inkl. Energie
        // Ersetzt Bewohnerwerte durch Energiewerte
        $max_prod[5] = 0;
        $min_prod[5] = 9999999999;
        $tot_prod[5] = 0;
        foreach ($userPlanets as $planet) {
            // TODO
            //Speichert die aktuellen Energieproduktionen in ein Array (Bewohnerproduktion [5] wird überschrieben)
            $val_prod[$planet->getId()][5] = floor($planet->getProdPower());

            // Gibt Min. / Max. aus
            $max_prod[5] = max($max_prod[5], $val_prod[$planet->getId()][5]);
            $min_prod[5] = min($min_prod[5], $val_prod[$planet->getId()][5]);
            $tot_prod[5] += $val_prod[$planet->getId()][5];
        }

        $buildLogs = $this->gameLogRepository->searchLogs(GameLogSearch::create()->user($user)->facility(GameLogFacility::BUILD), 5);
        $techLogs = $this->gameLogRepository->searchLogs(GameLogSearch::create()->user($user)->facility(GameLogFacility::TECH), 5);
        $shipLogs = $this->gameLogRepository->searchLogs(GameLogSearch::create()->user($user)->facility(GameLogFacility::SHIP), 5);
        $defLogs = $this->gameLogRepository->searchLogs(GameLogSearch::create()->user($user)->facility(GameLogFacility::DEF), 5);

        return $this->render('admin/user/economy.html.twig', [
            'user' => $user,
            'userPlanets' => $userPlanets,
            'val_res' => $val_res ?? [],
            'max_res' => $max_res ?? [],
            'min_res' => $min_res ?? [],
            'val_store' => $val_store ?? [],
            'val_time' => $val_time ?? [],
            'tot_res' => $tot_res ?? [],
            'val_prod' => $val_prod ?? [],
            'max_prod' => $max_prod ?? [],
            'min_prod' => $min_prod ?? [],
            'tot_prod' => $tot_prod ?? [],
            'buildLogs' => array_map(fn($log) => [
                'id' => $log->getId(),
                'timestamp' => $log->getTimestamp(),
                'message' => $log->getMessage(),
                'severity' => LogSeverity::SEVERITIES[$log->getSeverity()],
                'ip' => $log->getIp(),
                'te' => $log->getEntity() ? $log->getEntity()->toString() : "-",
                'ob' => $log->getBuilding()->getName() . " " . ($log->getLevel() > 0 ? $log->getLevel() : ''),
                'obStatus' => match ($log->getStatus()) {
                    1 => "Ausbau abgebrochen",
                    2 => "Abriss abgebrochen",
                    3 => "Ausbau",
                    4 => "Abriss",
                    default => '-',
                },
            ], $buildLogs),
            'techLogs' => array_map(fn($log) => [
                'id' => $log->getId(),
                'timestamp' => $log->getTimestamp(),
                'message' => $log->getMessage(),
                'severity' => LogSeverity::SEVERITIES[$log->getSeverity()],
                'ip' => $log->getIp(),
                'te' => $log->getEntity() ? $log->getEntity()->toString() : "-",
                'ob' => $log->getTechnology()->getName() . " " . ($log->getLevel() > 0 ? $log->getLevel() : ''),
                'obStatus' => match ($log->getStatus()) {
                    3 => "Erforschung",
                    0 => "Erforschung abgebrochen",
                    default => '-',
                },
            ], $techLogs),
            'shipLogs' => array_map(fn($log) => [
                'id' => $log->getId(),
                'timestamp' => $log->getTimestamp(),
                'message' => $log->getMessage(),
                'severity' => LogSeverity::SEVERITIES[$log->getSeverity()],
                'ip' => $log->getIp(),
                'te' => $log->getEntity() ? $log->getEntity()->toString() : "-",
                'ob' => $log->getShip()->getName() . " " . ($log->getLevel() > 0 ? $log->getLevel() : ''),
                'obStatus' => match ($log->getStatus()) {
                    1 => "Bau",
                    0 => "Bau abgebrochen",
                    default => '-',
                },
            ], $shipLogs),
            'defLogs' => array_map(fn($log) => [
                'id' => $log->getId(),
                'timestamp' => $log->getTimestamp(),
                'message' => $log->getMessage(),
                'severity' => LogSeverity::SEVERITIES[$log->getSeverity()],
                'ip' => $log->getIp(),
                'te' => $log->getEntity() ? $log->getEntity()->toString() : "-",
                'ob' => $log->getShip()->getName() . " " . ($log->getLevel() > 0 ? $log->getLevel() : ''),
                'obStatus' => match ($log->getStatus()) {
                    1 => "Bau",
                    0 => "Bau abgebrochen",
                    default => '-',
                },
            ], $defLogs),
        ]);
    }

    #[Route('/admin/users/{id}/messages', name: 'admin.users.messages', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function messages(Request $request, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $limit = $request->query->getInt('limit', 5);

        return $this->render('admin/user/messages.html.twig', [
            'user' => $user,
            'messages' => $this->messageRepository->findBy(['userTo' => $user], null, $limit),
            'limit' => $limit,
        ]);
    }

    #[Route('/admin/users/{id}/messages', name: 'admin.users.messages.send', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function sendMessage(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        if (ValidationUtils::blank($request->get('subject')) || ValidationUtils::blank($request->get('message'))) {
            $this->addFlash('error', 'Titel oder Text fehlt!');
            return $this->redirectToRoute('admin.users.messages', ['id' => $id]);
        }

        $this->messageRepository->createSystemMessage($user, $this->messageCategoryRepository->find(MessageCategoryId::USER), $request->get('subject'), $request->get('message'));

        $this->addFlash('success', 'Nachricht gesendet');
        return $this->redirectToRoute('admin.users.messages', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/comments', name: 'admin.users.comments')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function comments(Request $request, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $userComment = new UserComment();

        $form = $this->createFormBuilder($userComment)
            ->add('text', TextareaType::class, [
                'label' => false,
                'attr' => [
                    "rows" => 4,
                    "cols" => 70,
                    "placeholder" => "Neuer Kommentar"
                ],
                'constraints' => new NotBlank(
                    ['message' => 'Du musst einen Text eingeben!']
                ),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern'
            ])
            ->getForm()
            ->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $userComment->setUser($user);
            $userComment->setAdmin($this->getUser()->getData());
            $userComment->setTimestamp(time());

            $this->userCommentRepository->persist($userComment);
            $this->userCommentRepository->save();

            $this->addFlash('success', 'Kommentar hinzugefügt');
        }

        return $this->render('admin/user/comments.html.twig', [
            'user' => $user,
            'comments' => $this->userCommentRepository->getComments($user),
            'form' => $form
        ]);
    }

    #[Route('/admin/users/comments/{comment}/delete', name: 'admin.users.comments.delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deleteComment(UserComment $comment): Response
    {
        $this->userCommentRepository->remove($comment);
        $this->userCommentRepository->save();

        $this->addFlash('success', 'Kommentar gelöscht');
        return $this->redirectToRoute('admin.users.comments', ['id' => $comment->getUser()->getId()]);
    }

    #[Route('/admin/users/{id}/logs', name: 'admin.users.logs')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function logs(Request $request, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $userLogEntryRequest = new UserLogEntryRequest();
        $form = $this->createForm(ManualUserLogEntryType::class, $userLogEntryRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->addToUserLog($user, "settings", $userLogEntryRequest->message);
            $this->addFlash('success', 'Log hinzugefügt');
            return $this->redirectToRoute('admin.users.logs', ['id' => $user->getId()]);
        }

        $limit = $request->query->getInt('limit', 100);

        return $this->render('admin/user/logs.html.twig', [
            'user' => $user,
            'logs' => $this->userLogRepository->getUserLogs($user, $limit),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/{id}/tickets', name: 'admin.users.tickets', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function tickets(?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        return $this->render('admin/user/tickets.html.twig', [
            'user' => $user,
            'tickets' => $user->getTickets(),
        ]);
    }

    #[Route('/admin/users/{id}/points', name: 'admin.users.points', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function points(Request $request, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $limit = $request->query->getInt('limit', 100);
        $limitOptions = array(10, 20, 30, 50, 100, 200);

        $t = time();
        $startVal = $request->query->get('start');
        $start = $startVal !== null ? (is_numeric($startVal) ? intval($startVal) : strtotime($startVal)) : $t - 172800;
        $endVal = $request->query->get('end');
        $end = $endVal !== null ? (is_numeric($endVal) ? intval($endVal) : strtotime($endVal)) : $t;

        return $this->render('admin/user/points.html.twig', [
            'user' => $user,
            'userPoints' => $this->userPointsRepository->getPoints($user, $limit, $start, $end),
            'limit' => $limit,
            'start' => $start,
            'end' => $end,
            'limitOptions' => $limitOptions,
        ]);
    }

    #[Route('/admin/users/{id}/loginFailures', name: 'admin.users.user_login_failures', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function loginFailures(int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $userLoginFailures = $this->userLoginFailureRepository->getUserLoginFailures($user->getId());

        return $this->render('admin/user/user_login_failures.html.twig', [
            'user' => $user,
            'failures' => $userLoginFailures,
            'failureHosts' => array_map(fn($failure) => $this->networkNameService->getHost($failure->getIp()), $userLoginFailures),
        ]);
    }

    #[Route('/admin/users/{id}/multi', name: 'admin.users.user_multi')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function multi(Request $request, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $multi = new UserMulti();
        $multi->setUser($user);

        $form = $this->createFormBuilder($multi)
            ->add('multiUser', UserType::class, [
                'label' => false
            ])
            ->add('reason', TextType::class, [
                'label' => false,
                'attr' => [
                    'maxlength' => "20",
                    'size' => "20"
                ],
                'constraints' => [new NotBlank()],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
            ])
            ->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Prüfe ob der eigene Nick eingetragen ist
            if ($multi->getMultiUser() === $user) {
                $this->addFlash('error', "Man kann nicht den selben Nick als Multi eintragen!");
            } else {
                $this->userMultiRepository->addOrUpdateEntry($multi);
                $this->addFlash('success', "Neuer Multi User angelegt!");
            }
        }

        return $this->render('admin/user/user_multi.html.twig', [
            'user' => $user,
            'multiEntries' => $this->userMultiRepository->getUserEntries($user, true),
            'deletedMultiEntries' => $this->userMultiRepository->getUserEntries($user, false),
            'form' => $form
        ]);
    }

    #[Route('/admin/users/{id}/deleteMulti/{multi}', name: 'admin.users.deleteMulti', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deleteMulti(int $id, int $multi): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $this->userMultiRepository->deactivate($id, $multi);
        $this->userRepository->increaseMultiDeletes($user);
        $this->addFlash('success', "Eintrag gelöscht!");

        return $this->redirectToRoute('admin.users.user_multi', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/sitting', name: 'admin.users.user_sitting')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function sitting(Request $request, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $userSitting = new UserSitting();

        $form = $this->createFormBuilder($userSitting)
            ->add('sitter', TextType::class, [
                'label' => false,
                'invalid_message' => 'Kein gültiger User!'
            ])
            ->add('dateFrom', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'timestamp',
                'data' => time(),
                'attr' => [
                    'min' => time()
                ]
            ])
            ->add('dateTo', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'timestamp',
                'data' => time(),
                'attr' => [
                    'min' => time()
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'off',
                        'maxlength' => "20",
                        'size' => "20",
                    ]
                ],
                'required' => true,
                'first_options' => [
                    'hash_property_path' => 'password',
                ],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
            ]);

        $form = $form->add($form->get('sitter')->addModelTransformer(new CallbackTransformer(
            function (?User $user): string {
                return $user ? $user->getNick() : '';
            },
            function (?string $nick) {
                $user = $this->userRepository->findOneBy(['nick' => $nick]);

                if (!$user) {
                    throw new TransformationFailedException(sprintf('Ein Benutzer mit dem Nickname "%s" wurde nicht gefunden.', $nick));
                }

                return $user;

            }
        )))
            ->getForm()->handleRequest($request);

        $userSittings = $this->userSittingRepository->getWhereUser($user);
        $used_days = array_reduce($userSittings, fn($carry, UserSitting $entry) => $carry + (($entry->getDateTo() - $entry->getDateFrom()) / 86400), 0);
        $availableDays = floor($user->getSittingDays() - $used_days);

        if ($form->isSubmitted() && $form->isValid()) {
            $diff = ceil(($userSitting->getDateTo() - $userSitting->getDateFrom()) / 86400);

            if ($diff <= 0) {
                $this->addFlash('error', "Enddatum muss größer als Startdatum sein!");
                return $this->redirectToRoute('admin.users.user_sitting', ['id' => $user->getId()]);
            }

            if ($diff > $availableDays) {
                $this->addFlash('error', "So viele Tage sind nicht mehr vorhanden!");
                return $this->redirectToRoute('admin.users.user_sitting', ['id' => $user->getId()]);
            }

            if ($user === $userSitting->getSitter()) {
                $this->addFlash('error', "Man kann nicht den selben Nick im Sitting eintragen!");
                return $this->redirectToRoute('admin.users.user_sitting', ['id' => $user->getId()]);
            }

            $userSitting->setUser($user);
            $this->userSittingRepository->addEntry($userSitting);
            $this->userSittingRepository->save();
            $this->addFlash('success', "Sitting eingerichtet!");
        }

        return $this->render('admin/user/user_sitting.html.twig', [
            'user' => $user,
            'sittedEntries' => $userSittings,
            'sittingEntries' => $this->userSittingRepository->getWhereSitter($user),
            'availableDays' => $availableDays,
            'form' => $form
        ]);
    }

    #[Route('/admin/users/deleteSitting/{userSitting}', name: 'admin.users.deleteSitting', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deleteSitting(UserSitting $userSitting): Response
    {
        $this->userSittingRepository->cancelEntry($userSitting);
        $this->addFlash('success', "Eintrag gelöscht!");

        return $this->redirectToRoute('admin.users.user_sitting', ['id' => $userSitting->getUser()->getId()]);
    }

    /**
     * Parse value submitted by datepicker field
     */
    private function parseDatePicker(string $value): int
    {
        try {
            $dt = new DateTime($value);
            return $dt->getTimestamp();
        } catch (Exception) {
            return 0;
        }
    }
}
