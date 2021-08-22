<?PHP

use EtoA\Admin\AdminSessionManager;
use EtoA\Alliance\AlliancePointsRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Help\TicketSystem\TicketService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageRepository;
use EtoA\Message\MessageService;
use EtoA\Message\ReportRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Notepad\NotepadRepository;
use EtoA\Ranking\PointsService;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyRepository;
use EtoA\User\UserCommentRepository;
use EtoA\User\UserLogRepository;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use EtoA\User\UserSessionManager;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSurveillanceRepository;

/** @var TicketRepository $ticketRepo */
$ticketRepo = $app[TicketRepository::class];

/** @var UserSessionManager $userSessionManager */
$userSessionManager = $app[UserSessionManager::class];

/** @var AdminSessionManager $sessionManager */
$sessionManager = $app[AdminSessionManager::class];

/** @var PointsService $pointsService */
$pointsService = $app[PointsService::class];

/** @var MessageService $messageService */
$messageService = $app[MessageService::class];

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var MessageRepository $messageRepository */
$messageRepository = $app[MessageRepository::class];

/** @var ReportRepository $reportRepository */
$reportRepository = $app[ReportRepository::class];

/** @var TicketService $ticketService */
$ticketService = $app[TicketService::class];

/** @var UserService $userService */
$userService = $app[UserService::class];

echo '<h2>Clean-Up</h2>';

if (isset($_POST['submit_cleanup_selected']) || isset($_POST['submit_cleanup_all'])) {
    runCleanup(
        $ticketService,
        $userSessionManager,
        $sessionManager,
        $ticketRepo,
        $pointsService,
        $messageService,
        $userService
    );
}
cleanupOverView(
    $ticketRepo,
    $config,
    $messageRepository,
    $reportRepository,
    $userService
);

function runCleanup(
    TicketService $ticketService,
    UserSessionManager $userSessionManager,
    AdminSessionManager $sessionManager,
    TicketRepository $ticketRepo,
    PointsService $pointsService,
    MessageService $messageService,
    UserService $userService
) {
    global $app;

    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];

    /** @var DefenseRepository $defenseRepository */
    $defenseRepository = $app[DefenseRepository::class];
    /** @var LogRepository $logRepository */
    $logRepository = $app[LogRepository::class];

    echo "Clean-Up wird durchgeführt...<br/>";
    $all = isset($_POST['submit_cleanup_all']) ? true : false;

    // Log cleanup
    if (isset($_POST['cl_log']) || $all) {
        $nr = BaseLog::removeOld($_POST['log_timestamp']);
        echo $nr . " Logs wurden gelöscht!<br/>";
    }

    // Session-Log cleanup
    if ((isset($_POST['cl_sesslog']) && $_POST['cl_sesslog'] == 1) || $all) {
        $nr = $userSessionManager->cleanupLogs($_POST['sess_log_timestamp']);
        $nr += $sessionManager->cleanupLogs($_POST['sess_log_timestamp']);
        echo $nr . " Session-Logs wurden gelöscht!<br/>";
    }

    /* Message cleanup */
    if ((isset($_POST['cl_msg']) && $_POST['cl_msg'] == 1) || $all) {
        if ($_POST['only_deleted'] == 1) {
            $nr = $messageService->removeOld((int) $_POST['message_timestamp_deleted'], true);
        } else {
            $nr = $messageService->removeOld((int) $_POST['message_timestamp']);
        }
        echo $nr . " Nachrichten wurden gelöscht!<br/>";
    }

    /* Reports cleanup */
    if ((isset($_POST['cl_report']) && $_POST['cl_report'] == 1) || $all) {
        if ($_POST['only_deleted_reports'] == 1)
            $nr = Report::removeOld($_POST['report_timestamp_deleted'], 1);
        else
            $nr = Report::removeOld($_POST['report_timestamp']);
        echo $nr . " Berichte wurden gelöscht!<br/>";
    }

    // User-Point-History
    if ((isset($_POST['cl_points']) && $_POST['cl_points'] == 1) || $all) {
        $nr = $pointsService->cleanupUserPoints((int) $_POST['del_user_points']);
        echo $nr . " Benutzerpunkte-Logs und ";
        $nr = $pointsService->cleanupAlliancePoints((int) $_POST['del_user_points']);
        echo $nr . " Allianzpunkte-Logs wurden gelöscht!<br/>";
    }

    // Inactive and delete jobs
    if ((isset($_POST['cl_inactive']) && $_POST['cl_inactive'] == 1) || $all) {
        $num = $userService->removeInactive();
        $userService->informLongInactive();
        echo $num . " inaktive User wurden gelöscht!<br/>";
        $num = $userService->removeDeleted(true);
        echo $num . " gelöschte User wurden endgültig gelöscht!<br/>";
    }

    //Observer
    if ((isset($_POST['cl_surveillance']) && $_POST['cl_surveillance'] == 1) || $all) {
        /** @var UserSurveillanceRepository $userSurveillanceRepository */
        $userSurveillanceRepository = $app[UserSurveillanceRepository::class];
        $num = $userSurveillanceRepository->deletedOrphanedEntries();
        echo $num . " verwaiste Beobachtereinträge gelöscht<br/>";
    }

    // Userdata
    if ((isset($_POST['cl_userdata']) && $_POST['cl_userdata'] == 1) || $all) {
        /** @var UserRepository $userRepository */
        $userRepository = $app[UserRepository::class];
        $userIds = array_keys($userRepository->searchUserNicknames());
        if (count($userIds) > 0) {
            $ustring = implode(',', $userIds);
        } else {
            $ustring = 0;
            $userIds = [];
        }

        if (isset($_POST['del_user_log'])) {
            /** @var UserLogRepository $userLogRepository */
            $userLogRepository = $app[UserLogRepository::class];
            echo $userLogRepository->deleteOrphaned($userIds) . " verwaiste Userlogs wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_user_ratings'])) {
            /** @var UserRatingRepository $userRatingRepository */
            $userRatingRepository = $app[UserRatingRepository::class];
            echo $userRatingRepository->deleteOrphaned($userIds) . " verwaiste Ratings wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_user_properties'])) {
            /** @var UserPropertiesRepository $userPropertiesRepository */
            $userPropertiesRepository = $app[UserPropertiesRepository::class];
            echo $userPropertiesRepository->deleteOrphaned($userIds) . " verwaiste Properties wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_user_multi'])) {
            /** @var UserMultiRepository $userMultiRepository */
            $userMultiRepository = $app[UserMultiRepository::class];
            echo $userMultiRepository->deleteOrphaned($userIds) . " verwaiste Multieinträge wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_user_comments'])) {
            /** @var UserCommentRepository $userCommentRepository */
            $userCommentRepository = $app[UserCommentRepository::class];
            echo $userCommentRepository->deleteOrphaned($userIds) . " verwaiste Adminkommentare wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_tickets'])) {
            $ticketIds = $ticketRepo->findOrphanedIds();
            $deletedTickets = $ticketService->removeByIds($ticketIds);
            echo $deletedTickets . " verwaiste Tickets wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_reports'])) {
            /** @var ReportRepository $reportRepository */
            $reportRepository = $app[ReportRepository::class];
            echo $reportRepository->deleteOrphaned($userIds) . " verwaiste Berichte wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_notepad'])) {
            /** @var NotepadRepository $notepadRepository */
            $notepadRepository = $app[NotepadRepository::class];
            echo $notepadRepository->deleteOrphaned($userIds) . " verwaiste Notizen wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_shiplist'])) {
            echo $shipRepository->deleteOrphaned($userIds) . " verwaiste Schiffe wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_deflist'])) {
            echo $defenseRepository->deleteOrphaned($userIds) . " verwaiste Verteidigungen wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_missilelist'])) {
            /** @var MissileRepository $missileRepository */
            $missileRepository = $app[MissileRepository::class];
            echo $missileRepository->deleteOrphaned($userIds) . " verwaiste Raketen wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_buildlist'])) {
            /** @var BuildingRepository $buildingRepository */
            $buildingRepository = $app[BuildingRepository::class];
            echo $buildingRepository->deleteOrphaned($userIds) . " verwaiste Gebäude wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_techlist'])) {
            /** @var TechnologyRepository $technologyRepository */
            $technologyRepository = $app[TechnologyRepository::class];
            echo $technologyRepository->deleteOrphaned($userIds) . " verwaiste Technologien wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_def_queue'])) {
            /** @var DefenseQueueRepository $defQueueRepository */
            $defQueueRepository = $app[DefenseQueueRepository::class];
            echo $defQueueRepository->deleteOrphaned($userIds) . " verwaiste Bauaufträge (Def) wurden gelöscht!<br/>";
        }
        if (isset($_POST['del_ship_queue'])) {
            /** @var ShipQueueRepository $shipQueueRepository */
            $shipQueueRepository = $app[ShipQueueRepository::class];
            echo $shipQueueRepository->deleteOrphaned($userIds) . " verwaiste Bauaufträge (Schiff) wurden gelöscht!<br/>";
        }
    }

    /* object lists */
    if ((isset($_POST['cl_objlist']) && $_POST['cl_objlist'] == 1) || $all) {
        $nr = $shipRepository->cleanUp();
        echo $nr . " leere Schiffdaten wurden gelöscht!<br/>";
        $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Schiffsdatensätze wurden manuell gelöscht!");

        $nr = $defenseRepository->cleanUp();
        echo $nr . " leere Verteidigungsdaten wurden gelöscht!<br/>";
        $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Verteidigungsdatensätze wurden manuell gelöscht!");

        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        echo $buildingRepository->deleteEmpty() . " leere Gebäudedaten wurden gelöscht!<br/>";

        /** @var TechnologyRepository $technologyRepository */
        $technologyRepository = $app[TechnologyRepository::class];
        echo $technologyRepository->deleteEmpty() . " leere Forschungsdaten wurden gelöscht!<br/>";
    }

    echo "Clean-Up fertig!<br/><br/>";
}

function cleanupOverView(
    TicketRepository $ticketRepo,
    ConfigurationService $config,
    MessageRepository $messageRepository,
    ReportRepository $reportRepository,
    UserService $userService
): void {
    global $page;
    global $sub;
    global $app;

    echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";

    /* Messages */
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_msg" /> Nachrichten</legend>';
    echo '<input type="radio" name="only_deleted" value="0" /><b>Nachrichten löschen:</b> ';
    echo "Älter als <select name=\"message_timestamp\">";
    $days = array(1, 7, 14, 21, 28);
    if (!in_array($config->getInt('messages_threshold_days'), $days, true))
        $days[] = $config->getInt('messages_threshold_days');
    sort($days);
    foreach ($days as $ds) {
        echo "<option value=\"" . (24 * 3600 * $ds) . "\" " . ($ds == $config->getInt('messages_threshold_days')  ? " selected=\"selected\"" : "") . ">" . $ds . " Tage</option>";
    }
    echo "</select> (" . StringUtils::formatNumber($messageRepository->countNotArchived()) . " total).<br/>";

    echo '<input type="radio" name="only_deleted_reports" value="1" checked="checked" /> <b>Nur \'gelöschte\' Nachrichten löschen:</b> ';
    echo 'Älter als <select name="message_timestamp_deleted">';
    $days = array(7, 14, 21, 28);
    if (!in_array($config->param1Int('messages_threshold_days'), $days, true)) {
        $days[] = $config->param1Int('messages_threshold_days');
    }
    sort($days);
    foreach ($days as $ds) {
        echo "<option value=\"" . (24 * 3600 * $ds) . "\" " . ($ds == $config->param1Int('messages_threshold_days')  ? " selected=\"selected\"" : "") . ">" . $ds . " Tage</option>";
    }
    echo "</select> (" . StringUtils::formatNumber($messageRepository->countDeleted()) . " total).";
    echo '</fieldset><br/>';

    /* Reports */
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_report" /> Berichte</legend>';
    echo '<input type="radio" name="only_deleted" value="0" /><b>Berichte löschen:</b> ';
    echo "Älter als <select name=\"report_timestamp\">";
    $days = array(1, 7, 14, 21, 28);
    if (!in_array($config->param1Int('messages_threshold_days'), $days, true))
        $days[] = $config->param1Int('messages_threshold_days');
    sort($days);
    foreach ($days as $ds) {
        echo "<option value=\"" . (24 * 3600 * $ds) . "\" " . ($ds == $config->param1Int('messages_threshold_days')  ? " selected=\"selected\"" : "") . " >" . $ds . " Tage</option>";
    }
    echo "</select> (" . StringUtils::formatNumber($reportRepository->countNotArchived()) . " total).<br/>";

    echo '<input type="radio" name="only_deleted" value="1" checked="checked" /> <b>Nur \'gelöschte\' Berichte löschen:</b> ';
    echo 'Älter als <select name="report_timestamp_deleted">';
    $days = array(7, 14, 21, 28);
    if (!in_array($config->param1Int('reports_threshold_days'), $days, true))
        $days[] = $config->param1Int('reports_threshold_days');
    sort($days);
    foreach ($days as $ds) {
        echo "<option value=\"" . (24 * 3600 * $ds) . "\" " . ($ds == $config->param1Int('reports_threshold_days')  ? " selected=\"selected\"" : "") . ">" . $ds . " Tage</option>";
    }
    echo "</select> (" . StringUtils::formatNumber($reportRepository->countDeleted()) . " total).";
    echo '</fieldset><br/>';

    // Logs
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_log" /> Logs</legend>';

    /** @var LogRepository $logRepository */
    $logRepository = $app[LogRepository::class];
    $tblcnt = $logRepository->count();
    echo "<b>Logs löschen:</b> Einträge löschen welche älter als <select name=\"log_timestamp\">";
    $days = array(7, 14, 21, 28);
    if (!in_array($config->getInt('log_threshold_days'), $days, true))
        $days[] = $config->getInt('log_threshold_days');
    sort($days);
    foreach ($days as $ds) {
        echo "<option value=\"" . (24 * 3600 * $ds) . "\" " . ($ds == $config->getInt('log_threshold_days')  ? " selected=\"selected\"" : "") . ">" . $ds . " Tage</option>";
    }
    echo "</select> sind (" . StringUtils::formatNumber($tblcnt) . " total).";
    echo '</fieldset><br/>';

    // User-Sessions
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_sesslog" /> Session-Logs</legend>';
    /** @var UserSessionRepository $userSessionRepository */
    $userSessionRepository = $app[UserSessionRepository::class];
    $tblcnt = $userSessionRepository->count();
    echo "<b>Session-Logs löschen:</b> ";
    echo "Einträge löschen die älter als <select name=\"sess_log_timestamp\">";
    $days = array(7, 14, 21, 28);
    if (!in_array($config->getInt('log_threshold_days'), $days, true))
        $days[] = $config->getInt('log_threshold_days');
    sort($days);
    foreach ($days as $ds) {
        echo "<option value=\"" . (24 * 3600 * $ds) . "\" " . ($ds == $config->getInt('log_threshold_days')  ? " selected=\"selected\"" : "") . ">" . $ds . " Tage</option>";
    }
    echo "</select> sind (" . StringUtils::formatNumber($tblcnt) . " total).";
    echo '</fieldset><br/>';

    // User-Points
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_points" /> Punkteverlauf</legend>';
    /** @var \EtoA\User\UserPointsRepository $userPointRepository */
    $userPointRepository = $app[\EtoA\User\UserPointsRepository::class];
    $tblcnt = $userPointRepository->count();
    echo "<b>Punkteverläufe löschen:</b> Einträge löschen die älter als <select name=\"del_user_points\">";
    $days = array(2, 5, 7, 14, 21, 28);
    if (!in_array($config->getInt('log_threshold_days'), $days, true))
        $days[] = $config->getInt('log_threshold_days');
    sort($days);
    foreach ($days as $ds) {
        echo "<option value=\"" . (24 * 3600 * $ds) . "\" " . ($ds == $config->getInt('log_threshold_days')  ? " selected=\"selected\"" : "") . ">" . $ds . " Tage</option>";
    }
    echo "</select> sind (Total: " . StringUtils::formatNumber($tblcnt) . " User,";

    /** @var AlliancePointsRepository $alliancePointsRepository */
    $alliancePointsRepository = $app[AlliancePointsRepository::class];
    $tblcnt = $alliancePointsRepository->count();
    echo " " . StringUtils::formatNumber($tblcnt) . " Allianz).";
    echo '</fieldset><br/>';

    // Inactive
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_inactive" /> User</legend>';
    echo StringUtils::formatNumber($userService->getNumInactive()) . " inaktive Benutzer löschen (" . $config->param2Int('user_inactive_days') . " Tage seit der Registration ohne Login oder " . $config->param1Int('user_inactive_days') . " Tage nicht mehr eingeloggt)<br/>";
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $tblcnt = count($userRepository->findDeleted());
    echo StringUtils::formatNumber($tblcnt) . " als gelöscht markierte Benutzer endgültig löschen";

    echo '</fieldset><br/>';

    // Beobachter
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_surveillance" /> Beobachter</legend>';
    /** @var UserSurveillanceRepository $userSurveillanceRepository */
    $userSurveillanceRepository = $app[UserSurveillanceRepository::class];
    $tblcnt = $userSurveillanceRepository->getOrphanedUserIds();
    echo count($tblcnt) . " verwaiste Beobachtereinträge gefunden";
    echo '</fieldset><br/>';

    // Userdata
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_userdata" /> Userdata von gelöschten Spielern</legend>';
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $userIds = array_keys($userRepository->searchUserNicknames());
    if (count($userIds) === 0) {
        $userIds = [0];
    }

    /** @var UserLogRepository $userLogRepository */
    $userLogRepository = $app[UserLogRepository::class];
    $lcount = $userLogRepository->getOrphanedCount($userIds);
    /** @var UserRatingRepository $userRatingRepository */
    $userRatingRepository = $app[UserRatingRepository::class];
    $rcount = $userRatingRepository->getOrphanedCount($userIds);
    /** @var UserPropertiesRepository $userPropertiesRepository */
    $userPropertiesRepository = $app[UserPropertiesRepository::class];
    $pcount = $userPropertiesRepository->getOrphanedCount($userIds);
    /** @var UserMultiRepository $userMultiRepository */
    $userMultiRepository = $app[UserMultiRepository::class];
    $mcount = $userMultiRepository->getOrphanedCount($userIds);
    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];
    $ccount = $userCommentRepository->getOrphanedCount($userIds);
    $recount = $reportRepository->getOrphanedCount($userIds);
    /** @var NotepadRepository $notepadRepository */
    $notepadRepository = $app[NotepadRepository::class];
    $ncount = $notepadRepository->getOrphanedCount($userIds);
    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];
    $slcount = $shipRepository->getOrphanedCount($userIds);
    /** @var DefenseRepository $defenseRepository */
    $defenseRepository = $app[DefenseRepository::class];
    $dlcount = $defenseRepository->getOrphanedCount($userIds);
    /** @var BuildingRepository $buildingRepository */
    $buildingRepository = $app[BuildingRepository::class];
    $blcount = $buildingRepository->getOrphanedCount($userIds);
    /** @var TechnologyRepository $technologyRepository */
    $technologyRepository = $app[TechnologyRepository::class];
    $tlcount = $technologyRepository->getOrphanedCount($userIds);
    /** @var MissileRepository $missileRepository */
    $missileRepository = $app[MissileRepository::class];
    $mlcount = $missileRepository->getOrphanedCount($userIds);
    /** @var DefenseQueueRepository $defQueueRepository */
    $defQueueRepository = $app[DefenseQueueRepository::class];
    $dqcount = $defQueueRepository->getOrphanedCount($userIds);
    /** @var ShipQueueRepository $shipQueueRepository */
    $shipQueueRepository = $app[ShipQueueRepository::class];
    $sqcount = $shipQueueRepository->getOrphanedCount($userIds);

    echo '<input type="checkbox" value="1" name="del_user_log" /> ' . StringUtils::formatNumber($lcount) . " verwaiste <strong>Userlogs</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_user_ratings" /> ' . StringUtils::formatNumber($rcount) . " verwaiste <strong>Ratings</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_user_properties" /> ' . StringUtils::formatNumber($pcount) . " verwaiste <strong>Properties</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_user_multi" /> ' . StringUtils::formatNumber($mcount) . " verwaiste <strong>Multieinträge</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_user_comments" /> ' . StringUtils::formatNumber($ccount) . " verwaiste <strong>Adminkommentare</strong> gefunden<br/>";
    $numOrphanedTickets = count($ticketRepo->findOrphanedIds());
    echo '<input type="checkbox" value="1" name="del_tickets" /> ' . StringUtils::formatNumber($numOrphanedTickets) . " verwaiste <strong>Tickets</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_reports" /> ' . StringUtils::formatNumber($recount) . " verwaiste <strong>Berichte</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_notepad" /> ' . StringUtils::formatNumber($ncount) . " verwaiste <strong>Notizen</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_shiplist" /> ' . StringUtils::formatNumber($slcount) . " verwaiste <strong>Schiffdatensätze</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_ship_queue" /> ' . StringUtils::formatNumber($sqcount) . " verwaiste <strong>Schiffbauaufträge</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_deflist" /> ' . StringUtils::formatNumber($dlcount) . " verwaiste <strong>Defdatensätze</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_def_queue" /> ' . StringUtils::formatNumber($dqcount) . " verwaiste <strong>Defbauaufträge</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_buildlist" /> ' . StringUtils::formatNumber($blcount) . " verwaiste <strong>Gebäude</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_techlist" /> ' . StringUtils::formatNumber($tlcount) . " verwaiste <strong>Technologien</strong> gefunden<br/>";
    echo '<input type="checkbox" value="1" name="del_missilelist" /> ' . StringUtils::formatNumber($mlcount) . " verwaiste <strong>Raketen</strong> gefunden<br/>";
    echo '</fieldset><br/>';

    /* Object lists */
    echo '<fieldset><legend><input type="checkbox" value="1" name="cl_objlist" /> Objektlisten</legend>';

    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];
    $scnt = $shipRepository->countEmpty();
    $stcnt = $shipRepository->count();

    /** @var DefenseRepository $defenseRepository */
    $defenseRepository = $app[DefenseRepository::class];
    $dcnt = $defenseRepository->countEmpty();
    $dtcnt = $defenseRepository->count();

    /** @var MissileRepository $missileRepository */
    $missileRepository = $app[MissileRepository::class];
    $mcnt = $missileRepository->countEmpty();
    $mtcnt = $missileRepository->count();

    /** @var BuildingRepository $buildingRepository */
    $buildingRepository = $app[BuildingRepository::class];
    $bcnt = $buildingRepository->countEmpty();
    $btcnt = $buildingRepository->numBuildingListEntries();

    /** @var TechnologyRepository $technologyRepository */
    $technologyRepository = $app[TechnologyRepository::class];
    $tcnt = $technologyRepository->countEmpty();
    $ttcnt = $technologyRepository->count();

    echo "<b>Leere Schiffdatensätze:</b> " . StringUtils::formatNumber($scnt) . " vorhanden (" . StringUtils::formatNumber($stcnt) . " total).<br/>";
    echo "<b>Leere Verteidigungsdatensätze:</b> " . StringUtils::formatNumber($dcnt) . " vorhanden (" . StringUtils::formatNumber($dtcnt) . " total).<br/>";
    echo "<b>Leere Raketendatensäte:</b> " . StringUtils::formatNumber($mcnt) . " vorhanden (" . StringUtils::formatNumber($mtcnt) . " total).<br/>";
    echo "<b>Leere Gebäudedatensätze:</b> " . StringUtils::formatNumber($bcnt) . " vorhanden (" . StringUtils::formatNumber($btcnt) . " total).<br/>";
    echo "<b>Leere Forschungsdatensätze:</b> " . StringUtils::formatNumber($tcnt) . " vorhanden (" . StringUtils::formatNumber($ttcnt) . " total).<br/>";
    echo '</fieldset><br/>';

    echo '<input type="submit" name="submit_cleanup_selected" value="Selektiere ausführen" /> &nbsp; ';
    echo '<input type="submit" name="submit_cleanup_all" value="Alle ausführen" />';

    echo '</form>';
}
