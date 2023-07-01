<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Design\DesignsService;
use EtoA\Form\Request\Admin\UserCreateRequest;
use EtoA\Form\Type\Admin\UserCreateType;
use EtoA\Help\TicketSystem\Ticket;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Race\RaceDataRepository;
use EtoA\Ranking\UserBannerService;
use EtoA\Ship\ShipDataRepository;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Support\ExternalUrl;
use EtoA\User\UserCommentRepository;
use EtoA\User\UserHolidayService;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRatingSearch;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use EtoA\User\UserSittingRepository;
use EtoA\User\UserWarningRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WhichBrowser\Parser as BrowserParser;

class UserController extends AbstractController
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

                return $this->redirectToRoute('admin.users.edit', ['id' => $user->id]);
            } catch (Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/{id}/edit', name: 'admin.users.edit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function edit(int $id): Response
    {
        $user = $this->userRepository->getUserAdminView($id);

        if ($user === null) {
            $this->addFlash('error', 'Datensatz nicht vorhanden!');

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

        $designs = $this->designsService->getDesigns();
        $planetCircleOptions = range(450, 700, 50);

        $userLoginFailures = $this->userLoginFailureRepository->getUserLoginFailures($user->id);

        $ratingSearch = UserRatingSearch::create()->id($id);

        $tickets = $this->ticketRepo->findBy(['user_id' => $id]);

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'properties' => $this->userPropertiesRepository->getOrCreateProperties($user->id),
            'specialistTime' => $user->specialistTime > 0 ? $user->specialistTime : time(),
            'agent' => (new BrowserParser($user->userAgent))->toString(),
            'host' => $this->networkNameService->getHost($user->ipAddr),
            'isBlocked' => $user->blockedFrom > 0 && $user->blockedTo > time(),
            'isNoLongerBlocked' => $user->blockedFrom > 0 && $user->blockedTo < time(),
            'userBlockedDefaultTime' => time() + (3600 * 24 * $this->config->getInt('user_ban_min_length')),
            'commentInfo' => $this->userCommentRepository->getCommentInformation($user->id),
            'comments' => $this->userCommentRepository->getComments($id),
            'numberOfNewTickets' => count($newTickets),
            'numberOfAssignedTickets' => count($assignedTickets),
            'warning' => $this->userWarningRepository->getCountAndLatestWarning($user->id),
            'adminUserNicks' => $this->adminUserRepo->findAllAsList(),
            'holidayModeExpired' => $user->hmodFrom > 0 && $user->hmodTo < time(),
            'userHolidayModeDefaultTime' => time() + (3600 * 24 * $this->config->getInt('user_umod_min_length')),
            'raceNames' => $this->raceRepository->getRaceNames(),
            'specialistNames' => $this->specialistRepository->getSpecialistNames(),
            'allianceNamesWithTags' => $this->allianceRepository->getAllianceNamesWithTags(),
            'spyShipNames' => $this->shipDateRepository->getShipNamesWithAction('spy'),
            'analyzeShipNames' => $this->shipDateRepository->getShipNamesWithAction('analyze'),
            'multiEntries' => $this->userMultiRepository->getUserEntries($user->id, true),
            'deletedMultiEntries' => $this->userMultiRepository->getUserEntries($user->id, false),
            'sittedEntries' => $this->userSittingRepository->getWhereUser($user->id),
            'sittingEntries' => $this->userSittingRepository->getWhereSitter($user->id),
            'bannerPath' => file_exists($bannerPath) ? $bannerPath : null,
            'bannerTime' => file_exists($bannerPath) ? filemtime($bannerPath) : 0,
            'userBannerWebsiteLink' => ExternalUrl::USERBANNER_LINK,
            'userBannerLink' => $this->config->get('roundurl') . '/' . $bannerPath,
            'designNames' => array_map(fn($design) => $design['name'], $designs),
            'planetCircleOptions' => array_combine($planetCircleOptions, $planetCircleOptions),
            'failures' => $userLoginFailures,
            'failureHosts' => array_map(fn($failure) => $this->networkNameService->getHost($failure->ip), $userLoginFailures),
            'battleRating' => $this->userRatingRepository->getBattleRating($ratingSearch)[0] ?? null,
            'tradeRating' => $this->userRatingRepository->getTradeRating($ratingSearch)[0] ?? null,
            'diplomacyRating' => $this->userRatingRepository->getDiplomacyRating($ratingSearch)[0] ?? null,
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

        // new Multi
        if (filled($request->request->get('new_multi')) && filled($request->request->get('multi_reason'))) {
            $newMultiUserId = $this->userRepository->getUserIdByNick($request->request->get('new_multi'));
            if ($newMultiUserId === null) {
                $this->addFlash('error', "Dieser User existiert nicht!");
            } // Prüfe ob der eigene Nick eingetragen ist
            elseif ($newMultiUserId == $id) {
                $this->addFlash('error', "Man kann nicht den selben Nick im Sitting eintragen!");
            } else {
                $this->userMultiRepository->addOrUpdateEntry($id, $newMultiUserId, $request->request->get('multi_reason'));
                $this->addFlash('success', "Neuer User angelegt!");
            }
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

        if ($request->request->has('del_multi')) {
            // Multi löschen
            foreach ($request->request->all('del_multi') as $m_id => $data) {
                $m_id = intval($m_id);
                if ($request->request->all('del_multi')[$m_id] == 1) {
                    $this->userMultiRepository->deactivate($id, (int)$$request->request->all('multi_nick')[$m_id]);
                    $this->userRepository->increaseMultiDeletes($id);
                    $this->addFlash('success', "Eintrag gelöscht!");
                }
            }
        }

        // Sitting löschen
        if ($request->request->has('del_sitting')) {
            foreach ($request->request->all('del_sitting') as $s_id => $data) {
                $s_id = intval($s_id);
                if ($request->request->all('del_sitting')[$s_id] == 1) {
                    $this->userSittingRepository->cancelEntry($s_id);
                    $this->addFlash('success', "Eintrag gelöscht!");
                }
            }
        }

        //new sitting
        if (filled($request->request->get('sitter_nick'))) {
            if ($request->request->get('sitter_password1') == $request->request->get('sitter_password2') && filled($request->request->get('sitter_password1'))) {
                $sitting_from = parseDatePicker($request->request->get('sitting_time_from'));
                $sitting_to = parseDatePicker($request->request->get('sitting_time_to'));
                $diff = ceil(($sitting_to - $sitting_from) / 86400);
                $pw = saltPasswort($request->request->get('sitter_password1'));
                $sitterId = $this->userRepository->getUserIdByNick($request->request->get('sitter_nick'));

                if ($diff > 0) {
                    if ($sitterId !== null) {
                        if ($diff <= $request->request->getInt('user_sitting_days')) {
                            $this->userSittingRepository->addEntry($id, $sitterId, $pw, $sitting_from, $sitting_to);
                        } else {
                            $this->addFlash('error', "So viele Tage sind nicht mehr vorhanden!!");
                        }
                    } else {
                        $this->addFlash('error', "Dieser Sitternick existiert nicht!");
                    }
                } else {
                    $this->addFlash('error', "Enddatum muss größer als Startdatum sein!");
                }
            }
        }

        $this->addFlash('success', "Änderungen wurden übernommen!");

        return $this->redirectToRoute('admin.users.edit', ['id' => $id]);
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

        return $this->redirectToRoute('admin.users.edit', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/cancelDelete', name: 'admin.users.cancel_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function cancelDelete(int $id): Response
    {
        $this->userRepository->markDeleted($id, 0);
        $this->addFlash('success', "Löschantrag aufgehoben!");

        return $this->redirectToRoute('admin.users.edit', ['id' => $id]);
    }

    #[Route('/admin/users/{id}/setVerified', name: 'admin.users.set_verified', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function setVerified(int $id): Response
    {
        $this->userRepository->setVerified($id, true);
        $this->addFlash('success', "Account freigeschaltet!");

        return $this->redirectToRoute('admin.users.edit', ['id' => $id]);
    }
}
