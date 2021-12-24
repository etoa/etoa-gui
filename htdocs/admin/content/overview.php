<?PHP

use EtoA\Admin\AdminSessionManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ranking\GameStatsGenerator;
use EtoA\Support\StringUtils;
use Symfony\Component\HttpFoundation\Request;

/** @var Request $request */

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var GameStatsGenerator $gameStatsGenerator */
$gameStatsGenerator = $app[GameStatsGenerator::class];

if ($sub == "stats") {
    require("home/stats.inc.php");
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
        adminSessionLogForUserView($request, $sessionRepository, $adminUserRepo, $networkNameService);
    } else {
        adminSessionLogView($request, $config, $cu, $sessionRepository, $sessionManager, $networkNameService);
    }
} else {
    indexView();
}

function adminSessionLogForUserView(
    Request $request,
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
                if ($arr->timeAction > 0)
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
                if ($arr->sessionId == $request->getSession()->getId()) {
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
        echo "Es sind " . StringUtils::formatNumber($sessionRepository->countSessionLog()) . " Einträge in der Datenbank vorhanden.<br/><br/>
                Einträge löschen die älter als <select name=\"log_timestamp\">";
        foreach ($logDelTimespan as $k => $lts) {
            echo "<option value=\"" . $k . "\">" . $lts[1] . "</option>";
        }
        echo "</select> sind: <input type=\"submit\" name=\"delentrys\" value=\"Ausführen\" /></form>";
    } else {
        echo "<i>Keine Einträge vorhanden</i>";
    }
}

function indexView() {
    forward('/admin/overview');
}
