<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Entity;
use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Design\DesignsService;
use EtoA\Form\Request\Admin\UserCreateRequest;
use EtoA\Form\Type\Admin\UserCreateType;
use EtoA\Help\TicketSystem\Ticket;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\GameLogSearch;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageCategoryId;
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
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRatingSearch;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use EtoA\User\UserSittingRepository;
use EtoA\User\UserWarningRepository;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WhichBrowser\Parser as BrowserParser;

class UserController extends AbstractAdminController
{
    public function __construct(
        private readonly ConfigurationService       $config,
        private readonly UserService                $userService,
        private readonly LogRepository              $logRepository,
        private readonly UserRepository             $userRepository,
        private readonly UserPropertiesRepository   $userPropertiesRepository,
        private readonly UserMultiRepository        $userMultiRepository,
        private readonly UserSittingRepository      $userSittingRepository,
        private readonly UserHolidayService         $userHolidayService,
        private readonly UserWarningRepository      $userWarningRepository,
        private readonly AdminUserRepository        $adminUserRepo,
        private readonly NetworkNameService         $networkNameService,
        private readonly UserCommentRepository      $userCommentRepository,
        private readonly TicketRepository           $ticketRepo,
        private readonly RaceDataRepository         $raceRepository,
        private readonly SpecialistDataRepository   $specialistRepository,
        private readonly AllianceRepository         $allianceRepository,
        private readonly ShipDataRepository         $shipDateRepository,
        private readonly UserBannerService          $userBannerService,
        private readonly DesignsService             $designsService,
        private readonly UserLoginFailureRepository $userLoginFailureRepository,
        private readonly UserRatingRepository       $userRatingRepository,
        private readonly PlanetRepository           $planetRepository,
        private readonly GameLogRepository          $gameLogRepository,
        private readonly BuildingDataRepository     $buildingRepository,
        private readonly TechnologyDataRepository   $techRepository,
        private readonly ShipDataRepository         $shipRepository,
        private readonly DefenseDataRepository      $defenseRepository,
        private readonly MessageRepository          $messageRepository,
        private readonly UserLogRepository          $userLogRepository,
        private readonly UserPointsRepository       $userPointsRepository,
        private readonly string                     $projectDir,
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
                $user = $this->userService->register($createUserRequest->name, $createUserRequest->email, $createUserRequest->nick, $createUserRequest->password, $createUserRequest->raceId, $createUserRequest->ghost, true);
                $this->logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->nick . " (" . $user->name . ", " . $user->email . ") wurde registriert!");
                $this->addFlash('success', 'Spieler erstellt');

                return $this->redirectToRoute('admin.users.view', ['id' => $user->id]);
            } catch (Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/users/{id}', name: 'admin.users.view')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function view(int $id): Response
    {
        $user = $this->userRepository->getUserAdminView($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $newTickets = $this->ticketRepo->findBy([
            "user_id" => $user->id,
            "status" => "new",
        ]);
        $assignedTickets = $this->ticketRepo->findBy([
            "user_id" => $user->id,
            "status" => "assigned",
        ]);

        $bannerPath = $this->userBannerService->getUserBannerPath($id);

        $ratingSearch = UserRatingSearch::create()->id($id);

        return $this->render('admin/user/view.html.twig', [
            'user' => $user,
            'agent' => (new BrowserParser($user->userAgent))->toString(),
            'host' => $this->networkNameService->getHost($user->ipAddr),
            'isBlocked' => $user->blockedFrom > 0 && $user->blockedTo > time(),
            'commentInfo' => $this->userCommentRepository->getCommentInformation($user->id),
            'activeMultisCount' => count($this->userMultiRepository->getUserEntries($user->id, true)),
            'activeSitting' => $this->userSittingRepository->getActiveUserEntry($user->id),
            'numberOfNewTickets' => count($newTickets),
            'numberOfAssignedTickets' => count($assignedTickets),
            'warning' => $this->userWarningRepository->getCountAndLatestWarning($user->id),
            'raceNames' => $this->raceRepository->getRaceNames(),
            'specialistNames' => $this->specialistRepository->getSpecialistNames(),
            'allianceNamesWithTags' => $this->allianceRepository->getAllianceNamesWithTags(),
            'bannerPath' => file_exists($bannerPath) ? $bannerPath : null,
            'bannerTime' => file_exists($bannerPath) ? filemtime($bannerPath) : 0,
            'userBannerWebsiteLink' => ExternalUrl::USERBANNER_LINK,
            'userBannerLink' => $this->config->get('roundurl') . '/' . $bannerPath,
            'battleRating' => $this->userRatingRepository->getBattleRating($ratingSearch)[0] ?? null,
            'tradeRating' => $this->userRatingRepository->getTradeRating($ratingSearch)[0] ?? null,
            'diplomacyRating' => $this->userRatingRepository->getDiplomacyRating($ratingSearch)[0] ?? null,
        ]);
    }

    #[Route('/admin/users/{id}/edit', name: 'admin.users.edit', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function edit(int $id): Response
    {
        $user = $this->userRepository->getUserAdminView($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $designs = $this->designsService->getDesigns();
        $planetCircleOptions = range(450, 700, 50);

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'properties' => $this->userPropertiesRepository->getOrCreateProperties($user->id),
            'isNoLongerBlocked' => $user->blockedFrom > 0 && $user->blockedTo < time(),
            'userBlockedDefaultTime' => time() + (3600 * 24 * $this->config->getInt('user_ban_min_length')),
            'adminUserNicks' => $this->adminUserRepo->findAllAsList(),
            'holidayModeExpired' => $user->hmodFrom > 0 && $user->hmodTo < time(),
            'userHolidayModeDefaultTime' => time() + (3600 * 24 * $this->config->getInt('user_umod_min_length')),
            'raceNames' => $this->raceRepository->getRaceNames(),
            'specialistNames' => $this->specialistRepository->getSpecialistNames(),
            'allianceNamesWithTags' => $this->allianceRepository->getAllianceNamesWithTags(),
            'spyShipNames' => $this->shipDateRepository->getShipNamesWithAction('spy'),
            'analyzeShipNames' => $this->shipDateRepository->getShipNamesWithAction('analyze'),
            'designNames' => array_map(fn($design) => $design['name'], $designs),
            'planetCircleOptions' => $planetCircleOptions,
        ]);
    }

    #[Route('/admin/users/{id}/edit', name: 'admin.users.update', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function update(Request $request, int $id): Response
    {
        $user = $this->userRepository->getUser($id);

        if ($user->nick !== $request->get('user_nick')) {
            $this->userService->addToUserLog($id, "settings", "{nick} hat seinen Namen zu " . $request->get('user_nick') . " geändert.");
        }

        $user->name = $request->request->get('user_name');
        $user->npc = $request->request->getInt('npc');
        $user->nick = $request->request->get('user_nick');
        $user->email = $request->request->get('user_email');
        $user->passwordTemp = $request->request->get('user_password_temp');
        $user->emailFix = $request->request->get('user_email_fix');
        $user->dualName = $request->request->get('dual_name');
        $user->dualEmail = $request->request->get('dual_email');
        $user->raceId = $request->request->getInt('user_race_id');
        $user->allianceId = $request->request->getInt('user_alliance_id');
        $user->profileText = $request->request->get('user_profile_text');
        $user->signature = $request->request->get('user_signature');
        $user->multiDelets = $request->request->getInt('user_multi_delets');
        $user->sittingDays = $request->request->getInt('user_sitting_days');
        $user->chatAdmin = $request->request->getInt('user_chatadmin');
        $user->admin = $request->request->getInt('admin');
        $user->ghost = $request->request->getBoolean('user_ghost');
        $user->userChangedMainPlanet = $request->request->getBoolean('user_changed_main_planet');
        $user->profileBoardUrl = $request->request->get('user_profile_board_url');
        $user->allianceShipPoints = $request->request->getInt('user_alliace_shippoints');
        $user->allianceShipPointsUsed = $request->request->getInt('user_alliace_shippoints_used');

        if ($request->request->has('user_alliance_rank_id')) {
            $user->allianceRankId = $request->request->getInt('user_alliance_rank_id');
        }
        if ($request->request->has('user_profile_img_check')) {
            $user->profileImageCheck = false;
        }

        // Handle specialist decision
        if ($request->request->getInt('user_specialist_id') > 0 && $request->request->get('user_specialist_time') > 0) {
            $user->specialistTime = strtotime($request->request->get('user_specialist_time'));
            $user->specialistId = $request->request->getInt('user_specialist_id');
        } else {
            $user->specialistTime = 0;
            $user->specialistId = 0;
        }

        // Handle profile image
        if ($request->request->has('profile_img_del')) {
            $existingProfileImage = $this->projectDir . $user->getProfileImageUrl();
            if (file_exists($existingProfileImage)) {
                unlink($existingProfileImage);
            }

            $user->profileImage = '';
        }

        // Handle avatar
        if ($request->request->has('avatar_img_del')) {
            $existingAvatarPath = $this->projectDir . $user->getAvatarUrl();
            if (file_exists($existingAvatarPath)) {
                unlink($existingAvatarPath);
            }
            $user->avatar = '';
        }

        // Handle password
        if ($request->request->has('user_password') && filled($request->request->get('user_password'))) {
            $user->password = saltPasswort($request->request->get('user_password'));
            $this->addFlash('success', "Das Passwort wurde geändert!");

            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $this->getUser()->getUserIdentifier() . " ändert das Passwort von " . $request->request->get('user_nick'));
        }

        // Handle ban
        if ($request->request->getInt('ban_enable') == 1) {
            $ban_from = parseDatePicker($request->request->get('user_blocked_from'));
            $ban_to = parseDatePicker($request->request->get('user_blocked_to'));

            $user->blockedFrom = $ban_from;
            $user->blockedTo = $ban_to;
            $user->banAdminId = $request->request->getInt('user_ban_admin_id');
            $user->banReason = $request->request->get('user_ban_reason');

            $adminUserNicks = $this->adminUserRepo->findAllAsList();
            $adminUserNick = $adminUserNicks[$request->request->getInt('user_ban_admin_id')] ?? '';
            $this->userService->addToUserLog($id, "account", "{nick} wird von [b]" . date("d.m.Y H:i", $ban_from) . "[/b] bis [b]" . date("d.m.Y H:i", $ban_to) . "[/b] gesperrt.\n[b]Grund:[/b] " . addslashes($request->request->get('user_ban_reason')) . "\n[b]Verantwortlich: [/b] " . $adminUserNick);
        } else {
            $user->blockedFrom = 0;
            $user->blockedTo = 0;
            $user->banAdminId = 0;
            $user->banReason = '';
        }

        // Handle holiday mode
        if ($request->request->getInt('umod_enable') == 1) {
            $this->userHolidayService->activateHolidayMode($id, true);
            $user->hmodFrom = parseDatePicker($request->request->get('user_hmode_from'));
            $user->hmodTo = parseDatePicker($request->request->get('user_hmode_to'));
        } else {
            $this->userHolidayService->deactivateHolidayMode($user, true);
            $user->hmodFrom = 0;
            $user->hmodTo = 0;
        }

        // Perform query
        $this->userRepository->save($user);

        //
        // Speichert User einstellungen
        //

        $properties = $this->userPropertiesRepository->getOrCreateProperties($id);
        $properties->cssStyle = filled($request->request->get('css_style')) ? $request->request->get('css_style') : null;
        $properties->planetCircleWidth = $request->request->getInt('planet_circle_width');
        $properties->itemShow = $request->request->get('item_show');
        $properties->imageFilter = $request->request->getInt('image_filter') == 1;
        $properties->msgSignature = filled($request->request->get('msgsignature')) ? $request->request->get('msgsignature') : null;
        $properties->msgCreationPreview = $request->request->getInt('msgcreation_preview') == 1;
        $properties->msgPreview = $request->request->getInt('msg_preview') == 1;
        $properties->msgCopy = $request->request->getInt('msg_copy') == 1;
        $properties->msgBlink = $request->request->getInt('msg_blink') == 1;
        $properties->spyShipId = $request->request->getInt('spyship_id');
        $properties->spyShipCount = $request->request->getInt('spyship_count');
        $properties->analyzeShipId = $request->request->getInt('analyzeship_id');
        $properties->analyzeShipCount = $request->request->getInt('analyzeship_count');
        $properties->havenShipsButtons = $request->request->getInt('havenships_buttons') == 1;
        $properties->showAdds = $request->request->getInt('show_adds') == 1;
        $properties->fleetRtnMsg = $request->request->getInt('fleet_rtn_msg') == 1;

        $this->userPropertiesRepository->storeProperties($id, $properties);

        $this->addFlash('success', "Änderungen wurden übernommen!");

        return $this->redirectToRoute('admin.users.view', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/delete', name: 'admin.users.delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deleteUser(int $id): Response
    {
        try {
            $this->userService->delete($id, false, $this->getUser()->getUserIdentifier());
            $this->addFlash('success', 'Löschung erfolgreich!');
        } catch (Exception $ex) {
            $this->addFlash('error', $ex->getMessage());
        }

        return $this->redirectToRoute('admin.users');
    }

    #[Route('/admin/users/{id}/requestDelete', name: 'admin.users.request_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function requestDelete(int $id): Response
    {
        $t = time() + ($this->config->getInt('user_delete_days') * 3600 * 24);
        $this->userRepository->markDeleted($id, $t);
        $this->addFlash('success', "Löschantrag gespeichert!");

        return $this->redirectToRoute('admin.users.view', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/cancelDelete', name: 'admin.users.cancel_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function cancelDelete(int $id): Response
    {
        $this->userRepository->markDeleted($id, 0);
        $this->addFlash('success', "Löschantrag aufgehoben!");

        return $this->redirectToRoute('admin.users.view', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/setVerified', name: 'admin.users.set_verified', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function setVerified(int $id): Response
    {
        $this->userRepository->setVerified($id, true);
        $this->addFlash('success', "Account freigeschaltet!");

        return $this->redirectToRoute('admin.users.view', ['id' => $id]);
    }


    #[Route('/admin/users/{id}/economy', name: 'admin.users.economy')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function economy(int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        // Sucht alle Planet IDs des Users
        $userPlanets = $this->planetRepository->getUserPlanetsWithCoordinates($id);

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
                $val_res[$planet->id][0] = floor($planet->resMetal);
                $val_res[$planet->id][1] = floor($planet->resCrystal);
                $val_res[$planet->id][2] = floor($planet->resPlastic);
                $val_res[$planet->id][3] = floor($planet->resFuel);
                $val_res[$planet->id][4] = floor($planet->resFood);
                $val_res[$planet->id][5] = floor($planet->people);

                for ($x = 0; $x < 6; $x++) {
                    $max_res[$x] = max($max_res[$x], $val_res[$planet->id][$x]);
                    $min_res[$x] = min($min_res[$x], $val_res[$planet->id][$x]);
                    $tot_res[$x] += $val_res[$planet->id][$x];
                }

                //Speichert die aktuellen Rohstoffproduktionen in ein Array
                $val_prod[$planet->id][0] = floor($planet->prodMetal);
                $val_prod[$planet->id][1] = floor($planet->prodCrystal);
                $val_prod[$planet->id][2] = floor($planet->prodPlastic);
                $val_prod[$planet->id][3] = floor($planet->prodFuel);
                $val_prod[$planet->id][4] = floor($planet->prodFood);
                $val_prod[$planet->id][5] = floor($planet->prodPeople);

                for ($x = 0; $x < 6; $x++) {
                    $max_prod[$x] = max($max_prod[$x], $val_prod[$planet->id][$x]);
                    $min_prod[$x] = min($min_prod[$x], $val_prod[$planet->id][$x]);
                    $tot_prod[$x] += $val_prod[$planet->id][$x];
                }

                //Speichert die aktuellen Speicher in ein Array
                $val_store[$planet->id][0] = floor($planet->storeMetal);
                $val_store[$planet->id][1] = floor($planet->storeCrystal);
                $val_store[$planet->id][2] = floor($planet->storePlastic);
                $val_store[$planet->id][3] = floor($planet->storeFuel);
                $val_store[$planet->id][4] = floor($planet->storeFood);
                $val_store[$planet->id][5] = floor($planet->peoplePlace);

                //Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)

                //Titan
                if ($planet->prodMetal > 0) {
                    if ($planet->storeMetal - $planet->resMetal > 0) {
                        $val_time[$planet->id][0] = ceil(($planet->storeMetal - $planet->resMetal) / $planet->prodMetal * 3600);
                    } else {
                        $val_time[$planet->id][0] = 0;
                    }
                } else {
                    $val_time[$planet->id][0] = 0;
                }

                //Silizium
                if ($planet->prodCrystal > 0) {
                    if ($planet->storeCrystal - $planet->resCrystal > 0) {
                        $val_time[$planet->id][1] = ceil(($planet->storeCrystal - $planet->resCrystal) / $planet->prodCrystal * 3600);
                    } else {
                        $val_time[$planet->id][1] = 0;
                    }
                } else {
                    $val_time[$planet->id][1] = 0;
                }

                //PVC
                if ($planet->prodPlastic > 0) {
                    if ($planet->storePlastic - $planet->resPlastic > 0) {
                        $val_time[$planet->id][2] = ceil(($planet->storePlastic - $planet->resPlastic) / $planet->prodPlastic * 3600);
                    } else {
                        $val_time[$planet->id][2] = 0;
                    }
                } else {
                    $val_time[$planet->id][2] = 0;
                }

                //Tritium
                if ($planet->prodFuel > 0) {
                    if ($planet->storeFuel - $planet->resFuel > 0) {
                        $val_time[$planet->id][3] = ceil(($planet->storeFuel - $planet->resFuel) / $planet->prodFuel * 3600);
                    } else {
                        $val_time[$planet->id][3] = 0;
                    }
                } else {
                    $val_time[$planet->id][3] = 0;
                }

                //Nahrung
                if ($planet->prodFood > 0) {
                    if ($planet->storeFood - $planet->resFood > 0) {
                        $val_time[$planet->id][4] = ceil(($planet->storeFood - $planet->resFood) / $planet->prodFood * 3600);
                    } else {
                        $val_time[$planet->id][4] = 0;
                    }
                } else {
                    $val_time[$planet->id][4] = 0;
                }

                //Bewohner
                if ($planet->prodPeople > 0) {
                    if ($planet->peoplePlace - $planet->people > 0) {
                        $val_time[$planet->id][5] = ceil(($planet->peoplePlace - $planet->people) / $planet->prodPeople * 3600);
                    } else {
                        $val_time[$planet->id][5] = 0;
                    }
                } else {
                    $val_time[$planet->id][5] = 0;
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
            $val_prod[$planet->id][5] = floor($planet->prodPower);

            // Gibt Min. / Max. aus
            $max_prod[5] = max($max_prod[5], $val_prod[$planet->id][5]);
            $min_prod[5] = min($min_prod[5], $val_prod[$planet->id][5]);
            $tot_prod[5] += $val_prod[$planet->id][5];
        }

        $buildLogs = $this->gameLogRepository->searchLogs(GameLogSearch::create()->userId($id)->facility(GameLogFacility::BUILD), 5);
        $buildingNames = $this->buildingRepository->getBuildingNames(true);
        $techLogs = $this->gameLogRepository->searchLogs(GameLogSearch::create()->userId($id)->facility(GameLogFacility::TECH), 5);
        $technologyNames = $this->techRepository->getTechnologyNames(true);
        $shipLogs = $this->gameLogRepository->searchLogs(GameLogSearch::create()->userId($id)->facility(GameLogFacility::SHIP), 5);
        $shipNames = $this->shipRepository->getShipNames(true);
        $defLogs = $this->gameLogRepository->searchLogs(GameLogSearch::create()->userId($id)->facility(GameLogFacility::DEF), 5);
        $defenseNames = $this->defenseRepository->getDefenseNames(true);

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
                'id' => $log->id,
                'timestamp' => $log->timestamp,
                'message' => $log->message,
                'severity' => LogSeverity::SEVERITIES[$log->severity],
                'ip' => $log->ip,
                'te' => ($log->entityId > 0) ? Entity::createFactoryById($log->entityId) : "-",
                'ob' => $buildingNames[$log->objectId] . " " . ($log->level > 0 ? $log->level : ''),
                'obStatus' => match ($log->status) {
                    1 => "Ausbau abgebrochen",
                    2 => "Abriss abgebrochen",
                    3 => "Ausbau",
                    4 => "Abriss",
                    default => '-',
                },
            ], $buildLogs),
            'techLogs' => array_map(fn($log) => [
                'id' => $log->id,
                'timestamp' => $log->timestamp,
                'message' => $log->message,
                'severity' => LogSeverity::SEVERITIES[$log->severity],
                'ip' => $log->ip,
                'te' => ($log->entityId > 0) ? Entity::createFactoryById($log->entityId) : "-",
                'ob' => $technologyNames[$log->objectId] . " " . ($log->level > 0 ? $log->level : ''),
                'obStatus' => match ($log->status) {
                    3 => "Erforschung",
                    0 => "Erforschung abgebrochen",
                    default => '-',
                },
            ], $techLogs),
            'shipLogs' => array_map(fn($log) => [
                'id' => $log->id,
                'timestamp' => $log->timestamp,
                'message' => $log->message,
                'severity' => LogSeverity::SEVERITIES[$log->severity],
                'ip' => $log->ip,
                'te' => ($log->entityId > 0) ? Entity::createFactoryById($log->entityId) : "-",
                'ob' => $shipNames[$log->objectId] . " " . ($log->level > 0 ? $log->level : ''),
                'obStatus' => match ($log->status) {
                    1 => "Bau",
                    0 => "Bau abgebrochen",
                    default => '-',
                },
            ], $shipLogs),
            'defLogs' => array_map(fn($log) => [
                'id' => $log->id,
                'timestamp' => $log->timestamp,
                'message' => $log->message,
                'severity' => LogSeverity::SEVERITIES[$log->severity],
                'ip' => $log->ip,
                'te' => ($log->entityId > 0) ? Entity::createFactoryById($log->entityId) : "-",
                'ob' => $defenseNames[$log->objectId] . " " . ($log->level > 0 ? $log->level : ''),
                'obStatus' => match ($log->status) {
                    1 => "Bau",
                    0 => "Bau abgebrochen",
                    default => '-',
                },
            ], $defLogs),
        ]);
    }

    #[Route('/admin/users/{id}/messages', name: 'admin.users.messages', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function messages(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $limit = $request->query->getInt('limit', 5);

        return $this->render('admin/user/messages.html.twig', [
            'user' => $user,
            'messages' => $this->messageRepository->findBy(['user_to_id' => $id,], $limit),
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

        if (blank($request->get('subject')) || blank($request->get('message'))) {
            $this->addFlash('error', 'Titel oder Text fehlt!');
            return $this->redirectToRoute('admin.users.messages', ['id' => $id]);
        }

        $this->messageRepository->createSystemMessage($id, MessageCategoryId::USER, $request->get('subject'), $request->get('message'));

        $this->addFlash('success', 'Nachricht gesendet');
        return $this->redirectToRoute('admin.users.messages', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/comments', name: 'admin.users.comments', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function comments(int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        return $this->render('admin/user/comments.html.twig', [
            'user' => $user,
            'comments' => $this->userCommentRepository->getComments($id),
        ]);
    }

    #[Route('/admin/users/{id}/comments', name: 'admin.users.comments.add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function addComment(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        if (blank($request->get('text'))) {
            $this->addFlash('error', 'Text fehlt!');
            return $this->redirectToRoute('admin.users.comments', ['id' => $id]);
        }

        $this->userCommentRepository->addComment($id, $this->getUser()->getId(), $request->get('text'));

        $this->addFlash('success', 'Kommentar hinzugefügt');
        return $this->redirectToRoute('admin.users.comments', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/comments/{comment}/delete', name: 'admin.users.comments.delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deleteComment(int $id, int $comment): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $this->userCommentRepository->deleteComment($comment);

        $this->addFlash('success', 'Kommentar gelöscht');
        return $this->redirectToRoute('admin.users.comments', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/logs', name: 'admin.users.logs', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function logs(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $limit = $request->query->getInt('limit', 100);

        return $this->render('admin/user/logs.html.twig', [
            'user' => $user,
            'logs' => $this->userLogRepository->getUserLogs($id, $limit),
        ]);
    }

    #[Route('/admin/users/{id}/logs', name: 'admin.users.logs.add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function addLog(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        if (blank($request->get('text'))) {
            $this->addFlash('error', 'Text fehlt!');
            return $this->redirectToRoute('admin.users.logs', ['id' => $id]);
        }

        $this->userService->addToUserLog($id, "settings", $request->get('text'));

        $this->addFlash('success', 'Log hinzugefügt');
        return $this->redirectToRoute('admin.users.logs', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/tickets', name: 'admin.users.tickets', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function tickets(int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $tickets = $this->ticketRepo->findBy(['user_id' => $id]);

        return $this->render('admin/user/tickets.html.twig', [
            'user' => $user,
            'tickets' => array_map(fn(Ticket $ticket) => [
                'id' => $ticket->id,
                'idString' => $ticket->getIdString(),
                'statusName' => $ticket->getStatusName(),
                'categoryName' => $this->ticketRepo->getCategoryName($ticket->catId),
                'adminName' => ($ticket->adminId > 0 ? $this->adminUserRepo->getNick($ticket->adminId) : null),
                'timestamp' => $ticket->timestamp,
            ], $tickets),
        ]);
    }

    #[Route('/admin/users/{id}/points', name: 'admin.users.pointProgression', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function pointProgression(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
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

        return $this->render('admin/user/pointProgression.html.twig', [
            'user' => $user,
            'userPoints' => $this->userPointsRepository->getPoints($id, $limit, $start, $end),
            'limit' => $limit,
            'start' => $start,
            'end' => $end,
            'limitOptions' => $limitOptions,
        ]);
    }

    #[Route('/admin/users/{id}/loginFailures', name: 'admin.users.user_login_failures', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function loginFailures(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $userLoginFailures = $this->userLoginFailureRepository->getUserLoginFailures($user->id);

        return $this->render('admin/user/user_login_failures.html.twig', [
            'user' => $user,
            'failures' => $userLoginFailures,
            'failureHosts' => array_map(fn($failure) => $this->networkNameService->getHost($failure->ip), $userLoginFailures),
        ]);
    }

    #[Route('/admin/users/{id}/multi', name: 'admin.users.user_multi')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function multi(int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        return $this->render('admin/user/user_multi.html.twig', [
            'user' => $user,
            'multiEntries' => $this->userMultiRepository->getUserEntries($user->id, true),
            'deletedMultiEntries' => $this->userMultiRepository->getUserEntries($user->id, false),
        ]);
    }

    #[Route('/admin/users/{id}/addMulti', name: 'admin.users.addMulti', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function addMulti(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        if (!filled($request->request->get('new_multi')) || !filled($request->request->get('multi_reason'))) {
            $this->addFlash('error', 'Multi Name oder Beziehung fehlt!');
            return $this->redirectToRoute('admin.users.user_multi', ['id' => $id]);
        }

        $newMultiUserId = $this->userRepository->getUserIdByNick($request->request->get('new_multi'));
        if ($newMultiUserId === null) {
            $this->addFlash('error', "Dieser User existiert nicht!");
            return $this->redirectToRoute('admin.users.user_multi', ['id' => $id]);
        }

        // Prüfe ob der eigene Nick eingetragen ist
        if ($newMultiUserId == $id) {
            $this->addFlash('error', "Man kann nicht den selben Nick als Multi eintragen!");
            return $this->redirectToRoute('admin.users.user_multi', ['id' => $id]);
        }

        $this->userMultiRepository->addOrUpdateEntry($id, $newMultiUserId, $request->request->get('multi_reason'));
        $this->addFlash('success', "Neuer Multi User angelegt!");

        return $this->redirectToRoute('admin.users.user_multi', ['id' => $id]);
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
        $this->userRepository->increaseMultiDeletes($id);
        $this->addFlash('success', "Eintrag gelöscht!");

        return $this->redirectToRoute('admin.users.user_multi', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/sitting', name: 'admin.users.user_sitting')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function sitting(int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        return $this->render('admin/user/user_sitting.html.twig', [
            'user' => $user,
            'sittedEntries' => $this->userSittingRepository->getWhereUser($user->id),
            'sittingEntries' => $this->userSittingRepository->getWhereSitter($user->id),
        ]);
    }

    #[Route('/admin/users/{id}/addSitting', name: 'admin.users.addSitting', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function addSitting(int $id, Request $request): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        if (!filled($request->request->get('sitter_nick')) || !filled($request->request->get('sitter_password1'))) {
            $this->addFlash('error', 'Sitter Name oder Passwort fehlt!');
            return $this->redirectToRoute('admin.users.user_sitting', ['id' => $id]);
        }

        if ($request->request->get('sitter_password1') != $request->request->get('sitter_password2')) {
            $this->addFlash('error', 'Sitter Passwörter stimmen nicht überein!');
            return $this->redirectToRoute('admin.users.user_sitting', ['id' => $id]);
        }

        $sitting_from = parseDatePicker($request->request->get('sitting_time_from'));
        $sitting_to = parseDatePicker($request->request->get('sitting_time_to'));
        $diff = ceil(($sitting_to - $sitting_from) / 86400);
        $pw = saltPasswort($request->request->get('sitter_password1'));
        $sitterId = $this->userRepository->getUserIdByNick($request->request->get('sitter_nick'));

        if ($sitterId == $id) {
            $this->addFlash('error', "Man kann nicht den selben Nick im Sitting eintragen!");
            return $this->redirectToRoute('admin.users.user_sitting', ['id' => $id]);
        }

        if ($diff <= 0) {
            $this->addFlash('error', "Enddatum muss größer als Startdatum sein!");
            return $this->redirectToRoute('admin.users.user_sitting', ['id' => $id]);
        }
        if ($sitterId === null) {
            $this->addFlash('error', "Dieser Sitternick existiert nicht!");
            return $this->redirectToRoute('admin.users.user_sitting', ['id' => $id]);
        }

        if ($diff > $user->sittingDays) {
            $this->addFlash('error', "So viele Tage sind nicht mehr vorhanden!!");
            return $this->redirectToRoute('admin.users.user_sitting', ['id' => $id]);
        }

        $this->userSittingRepository->addEntry($id, $sitterId, $pw, $sitting_from, $sitting_to);
        $this->addFlash('success', "Sitting eingerichtet!");
        return $this->redirectToRoute('admin.users.user_sitting', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/deleteSitting/{sitter}', name: 'admin.users.deleteSitting', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deleteSitting(int $id, int $sitter): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Benutzer nicht vorhanden!');
            return $this->redirectToRoute('admin.users');
        }

        $this->userSittingRepository->cancelEntry($sitter);
        $this->addFlash('success', "Eintrag gelöscht!");

        return $this->redirectToRoute('admin.users.user_sitting', ['id' => $id]);
    }
}
