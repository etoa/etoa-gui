<?PHP

use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminSessionManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ranking\GameStatsGenerator;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\Universe\Cell\CellRepository;
use League\CommonMark\MarkdownConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var Request */
$request = Request::createFromGlobals();

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var GameStatsGenerator $gameStatsGenerator */
$gameStatsGenerator = $app[GameStatsGenerator::class];

if ($sub == "offline") {
    takeOffline($request, $config);
} elseif ($sub == "stats") {
    require("home/stats.inc.php");
} elseif ($sub === "gamestats") {
    gameStatsView($gameStatsGenerator, $twig);
} elseif ($sub === "changelog") {
    /** @var MarkdownConverterInterface */
    $markdown = $app[MarkdownConverterInterface::class];

    changelogView($markdown, $twig);
} elseif ($sub == "adminlog") {
    /** @var AdminSessionRepository $sessionRepository */
    $sessionRepository = $app[AdminSessionRepository::class];

    /** @var AdminUserRepository $adminUserRepo */
    $adminUserRepo = $app[AdminUserRepository::class];

    /** @var AdminSessionManager $sessionManager */
    $sessionManager = $app[AdminSessionManager::class];

    /** @var NetworkNameService $networkNameService */
    $networkNameService = $app[NetworkNameService::class];

    if ($request->request->has('logshow') && $request->request->get('logshow') != "") {
        adminSessionLogForUserView($request, $s, $sessionRepository, $adminUserRepo, $networkNameService);
    } else {
        adminSessionLogView($request, $config, $cu, $sessionRepository, $sessionManager, $networkNameService);
    }
} elseif ($sub == "adminusers") {
    require("home/adminusers.inc.php");
} elseif ($sub == "observed") {
    require("home/observed.inc.php");
} elseif ($sub == "sysinfo") {
    /** @var DatabaseManagerRepository $databaseManager */
    $databaseManager = $app[DatabaseManagerRepository::class];

    systemInfoView($databaseManager, $twig);
} else {
    /** @var CellRepository $universeCellRepo */
    $universeCellRepo = $app[CellRepository::class];

    /** @var TicketRepository $ticketRepo */
    $ticketRepo = $app[TicketRepository::class];

    /** @var TextRepository $textRepo */
    $textRepo = $app[TextRepository::class];

    /** @var AdminRoleManager $roleManager */
    $roleManager = $app[AdminRoleManager::class];

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];

    indexView($config, $cu, $universeCellRepo, $ticketRepo, $textRepo, $roleManager, $twig);
}

function takeOffline(Request $request, ConfigurationService $config)
{
    global $sub;
    global $page;

    echo "<h1>Spiel offline nehmen</h1>";

    if ($request->query->has('off') && $request->query->getBoolean('off')) {
        $config->set('offline', 1);
    }
    if ($request->query->has('on') && $request->query->getBoolean('on')) {
        $config->set('offline', 0);
    }

    if ($request->request->has('save')) {
        $config->set('offline_ips_allow', $request->request->get('offline_ips_allow'));
        $config->set('offline_message', $request->request->get('offline_message'));
    }

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    if ($config->getBoolean('offline')) {
        echo "<span style=\"color:#f90;\">Das Spiel ist offline!</span><br/><br/>
        Erlaubte IP Adressen  (deine ist " . $request->getClientIp() . "):<br/>
        <textarea name=\"offline_ips_allow\" rows=\"6\" cols=\"60\">" . $config->get('offline_ips_allow') . "</textarea><br/>
        Nachricht: <br/>
        <textarea name=\"offline_message\" rows=\"6\" cols=\"60\">" . $config->get('offline_message') . "</textarea><br/><br/>
        <input type=\"submit\" value=\"Änderungen speichern\" name=\"save\" /> &nbsp;
        <input type=\"button\" value=\"Spiel online stellen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;on=1'\" />";
    } else {
        echo "<span style=\"color:#0f0;\">Das Spiel ist online!</span><br/><br/>
        <input type=\"button\" value=\"Spiel offline nehmen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;off=1'\" />";
    }
    echo "</form>";
}

function gameStatsView(GameStatsGenerator $gameStatsGenerator, Environment $twig)
{
    echo $twig->render('admin/overview/gamestats.html.twig', [
        'userStats' => file_exists(USERSTATS_OUTFILE) ? USERSTATS_OUTFILE : null,
        'xmlInfo' => file_exists(XML_INFO_FILE) ? XML_INFO_FILE : null,
        'gameStats' => $gameStatsGenerator->readCached(),
    ]);
    exit();
}

function changelogView(MarkdownConverterInterface $markdown, Environment $twig)
{
    $changelogFile = "../../Changelog.md";
    $changelogPublicFile = "../../Changelog_public.md";
    echo $twig->render('admin/overview/changelog.html.twig', [
        'changelog' => is_file($changelogFile) ? $markdown->convertToHtml(file_get_contents($changelogFile)) : null,
        'changelogPublic' => is_file($changelogPublicFile) ? $markdown->convertToHtml(file_get_contents($changelogPublicFile)) : null,
    ]);
    exit();
}

function adminSessionLogForUserView(
    Request $request,
    AdminSession $s,
    AdminSessionRepository $sessionRepository,
    AdminUserRepository $adminUserRepo,
    NetworkNameService $networkNameService
) {
    global $page;
    global $sub;

    $adminUser = $adminUserRepo->find($request->request->getInt('user_id'));
    if ($adminUser != null) {
        echo "<h2>Session-Log für " . $adminUser->nick . "</h2>";

        $sessions = $sessionRepository->findSessionLogsByUser($request->request->getInt('user_id'));
        if (count($sessions) > 0) {
            echo "<table class=\"tb\">
                <tr>
                    <th>Login</th>
                    <th>Aktivität</th>
                    <th>Logout</th>
                    <th>Dauer</th>
                    <th>IP</th>
                    <th>Browser</th>
                    <th>OS</th>
                </tr>";
            foreach ($sessions as $arr) {
                echo "<tr>
                        <td>" . date("d.m.Y, H:i", $arr->timeLogin) . "</td>";
                echo "<td>";
                if ($arr['time_action'] > 0)
                    echo date("d.m.Y H:i", $arr->timeAction);
                else
                    echo "-";
                echo "</td>";
                echo "<td>";
                if ($arr->timeLogout > 0)
                    echo date("d.m.Y, H:i", $arr->timeLogout);
                else
                    echo "-";
                echo "</td>";
                echo "<td>";
                if (max($arr->timeLogout, $arr->timeAction) - $arr->timeLogin > 0) {
                    echo StringUtils::formatTimespan(max($arr->timeLogout, $arr->timeAction) - $arr->timeLogin);
                } else {
                    echo "-";
                }
                if ($arr->sessionId == $s->id) {
                    echo " <span style=\"color:#0f0\">aktiv</span>";
                }
                echo "</td>";
                echo "<td title=\"" . $networkNameService->getHost($arr->ipAddr) . "\">" . $arr->ipAddr . "</td>";
                $browserParser = new \WhichBrowser\Parser($arr->userAgent);
                echo "<td title=\"" . $arr->userAgent . "\">" . $browserParser->browser->toString() . "</td>";
                echo "<td title=\"" . $arr->userAgent . "\">" . $browserParser->os->toString() . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else
            echo "<i>Keine Einträge vorhanden</i>";
    } else {
        echo "<h2>Fehler</h2><i>User nicht vorhanden</i>";
    }
    echo "<br/><br/><input type=\"button\" value=\"Zur Übersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
}

function adminSessionLogView(
    Request $request,
    ConfigurationService $config,
    AdminUser $cu,
    AdminSessionRepository $sessionRepository,
    AdminSessionManager $sessionManager,
    NetworkNameService $networkNameService
) {
    global $page;
    global $sub;
    global $app;

    /** @var LogRepository $logRepository */
    $logRepository = $app[LogRepository::class];

    echo "<h1>Admin-Log</h1>";

    $logDelTimespan = [
        [1296000, "15 Tage"],
        [2592000, "30 Tage"],
        [3888000, "45 Tage"],
        [5184000, "60 Tage"],
    ];

    if ($request->query->has('kick') && $request->query->getInt('kick') > 0) {
        $idToKick = $request->query->getInt('kick');
        if ($idToKick != $cu->id) {
            $sessionManager->kick((string) $idToKick);
            $logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $cu->nick . " löscht die Session des Administrators mit der ID " . $idToKick);
        } else {
            echo error_msg("Du kannst nicht dich selbst kicken!");
        }
    }

    if ($request->request->has('delentrys') && $request->request->get('delentrys') != "") {
        if (isset($logDelTimespan[$request->request->getInt('log_timestamp')])) {
            $td = $logDelTimespan[$request->request->getInt('log_timestamp')][0];
            $nr = $sessionManager->cleanupLogs($td);
            echo "<p>" . $nr . " Einträge wurden gelöscht!</p>";
        }
    }

    echo "<h2>Aktive Sessions</h2>";
    echo "Das Timeout beträgt " . StringUtils::formatTimespan($config->getInt('admin_timeout')) . "<br/><br/>";

    $sessions = $sessionRepository->findAll();

    if (count($sessions) > 0) {
        echo "<table class=\"tb\">
            <tr>
                <th>Status</th>
                <th>Nick</th>
                <th>Login</th>
                <th>Aktivität</th>
                <th>Dauer</th>
                <th>IP</th>
                <th>User Agent</th>
                <th>Kicken</th>
            </tr>";
        $t = time();
        foreach ($sessions as $arr) {
            $browserParser = new \WhichBrowser\Parser($arr->userAgent);
            echo "<tr>
                    <td " . ($t - $config->getInt('admin_timeout') < $arr->timeAction ? 'style="color:#0f0;">Online' : 'style="color:red;">Timeout') . "</td>
                    <td>" . $arr->userNick . "</td>
                    <td>" . date("d.m.Y H:i", $arr->timeLogin) . "</td>
                    <td>" . date("d.m.Y H:i", $arr->timeAction) . "</td>
                    <td>" . StringUtils::formatTimespan($arr->timeAction - $arr->timeLogin) . "</td>
                    <td title=\"" . $networkNameService->getHost($arr->ipAddr) . "\">" . $arr->ipAddr . "</td>
                    <td title=\"" . $arr->userAgent . "\">" . $browserParser->toString() . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;kick=" . $arr->userId . "\">Kick</a></td>
                </tr>";
        }
        echo "</table>";
    } else
        echo "<i>Keine Einträge vorhanden!</i>";

    echo "<h2>Session-Log</h2>";
    $usersWithSessionLogs = $sessionRepository->findUsersWithSessionLogs();
    if (count($usersWithSessionLogs) > 0) {
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
        echo "Benutzer wählen: <select name=\"user_id\">";
        foreach ($usersWithSessionLogs as $arr) {
            echo "<option value=\"" . $arr->userId . "\">" . $arr->userNick . " (" . $arr->count . " Sessions)</option>";
        }
        echo "</select> &nbsp; <input type=\"submit\" name=\"logshow\" value=\"Anzeigen\" /></form>";

        echo "<h2>Logs löschen</h2>";
        echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
        echo "Es sind " . nf($sessionRepository->countSessionLog()) . " Einträge in der Datenbank vorhanden.<br/><br/>
                Einträge löschen die älter als <select name=\"log_timestamp\">";
        foreach ($logDelTimespan as $k => $lts) {
            echo "<option value=\"" . $k . "\">" . $lts[1] . "</option>";
        }
        echo "</select> sind: <input type=\"submit\" name=\"delentrys\" value=\"Ausführen\" /></form>";
    } else {
        echo "<i>Keine Einträge vorhanden</i>";
    }
}

function systemInfoView(DatabaseManagerRepository $databaseManager, Environment $twig)
{
    $unix = isUnixOS() ? posix_uname() : null;
    echo $twig->render('admin/overview/sysinfo.html.twig', [
        'phpVersion' => phpversion(),
        'dbVersion' => $databaseManager->getDatabasePlatform(),
        'webserverVersion' => $_SERVER['SERVER_SOFTWARE'],
        'unixName' => isUnixOS() ? $unix['sysname'] . ' ' . $unix['release'] . ' ' . $unix['version'] : null,
    ]);
    exit();
}

function indexView(
    ConfigurationService $config,
    AdminUser $cu,
    CellRepository $universeCellRepo,
    TicketRepository $ticketRepo,
    TextRepository $textRepo,
    AdminRoleManager $roleManager,
    Environment $twig
) {
    // Flottensperre aktiv
    $fleetBanTitle = null;
    $fleetBanText = null;
    if ($config->getBoolean('flightban')) {
        // Prüft, ob die Sperre schon abgelaufen ist
        if ($config->param1Int('flightban_time') <= time() && $config->param2Int('flightban_time') >= time()) {
            $flightban_time_status = "<span style=\"color:#0f0\">Aktiv</span> Es können keine Flüge gestartet werden!";
        } elseif ($config->param1Int('flightban_time') > time() && $config->param2Int('flightban_time') > time()) {
            $flightban_time_status = "Ausstehend";
        } else {
            $flightban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
        }

        $fleetBanTitle = "Flottensperre aktiviert";
        $fleetBanText = "Die Flottensperre wurde aktiviert.<br><br><b>Status:</b> " . $flightban_time_status . "<br><b>Zeit:</b> " . date("d.m.Y H:i", $config->param1Int('flightban_time')) . " - " . date("d.m.Y H:i", $config->param2Int('flightban_time')) . "<br><b>Grund:</b> " . $config->param1('flightban') . "<br><br>Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
    }

    // Kampfsperre aktiv
    if ($config->getBoolean('battleban')) {
        // Prüft, ob die Sperre schon abgelaufen ist
        if ($config->param1Int('battleban_time') <= time() && $config->param2Int('battleban_time') >= time()) {
            $battleban_time_status = "<span style=\"color:#0f0\">Aktiv</span> Es können keine Angriffe geflogen werden!";
        } elseif ($config->param1Int('battleban_time') > time() && $config->param2Int('battleban_time') > time()) {
            $battleban_time_status = "Ausstehend";
        } else {
            $battleban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
        }

        $fleetBanTitle = "Kampfsperre aktiviert";
        $fleetBanText = "Die Kampfsperre wurde aktiviert.<br><br><b>Status:</b> " . $battleban_time_status . "<br><b>Zeit:</b> " . date("d.m.Y H:i", $config->param1Int('battleban_time')) . " - " . date("d.m.Y H:i", $config->param2Int('battleban_time')) . "<br><b>Grund:</b> " . $config->param1('battleban') . "<br><br>Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
    }

    //
    // Schnellsuche
    //
    $_SESSION['planets']['query'] = Null;
    $_SESSION['admin']['user_query'] = "";
    $_SESSION['admin']['queries']['alliances'] = "";

    echo $twig->render('admin/overview/overview.html.twig', [
        'welcomeMessage' => 'Hallo <b>' . $cu->nick . '</b>, willkommen im Administrationsmodus! Deine Rolle(n): <b>' . $roleManager->getRolesStr($cu) . '.</b>',
        'hasTfa' => (bool) $cu->tfaSecret,
        'didBigBangHappen' => $universeCellRepo->count() != 0,
        'forcePasswordChange' => $cu->forcePasswordChange,
        'numNewTickets' => $ticketRepo->countNew(),
        'numOpenTickets' => $ticketRepo->countAssigned($cu->id),
        'fleetBanText' => $fleetBanText,
        'fleetBanTitle' => $fleetBanTitle,
        'adminInfo' => $textRepo->getEnabledTextOrDefault('admininfo'),
        'systemMessage' => $textRepo->getEnabledTextOrDefault('system_message'),
    ]);
    exit();
}
