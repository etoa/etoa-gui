<?PHP

use EtoA\HostCache\NetworkNameService;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSessionSearch;

echo "<h1>User-Sessionlogs</h1>";

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var UserSessionRepository $userSessionRepository */
$userSessionRepository = $app[UserSessionRepository::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

if (isset($_POST['logshow'])) {
    $search = UserSessionSearch::create();
    if ($_POST['user_id'] != "") {
        $search->userId((int) $_POST['user_id']);
    }
    if ($_POST['user_nick'] != "") {
        $search->userNickLike($_POST['user_nick']);
    }
    if ($_POST['log_ip'] != "") {
        $search->ip($_POST['log_ip']);
    }
    if ($_POST['log_hostname'] != "") {
        $search->ip($networkNameService->getAddr($_POST['log_ip']));
    }
    if ($_POST['user_agent'] != "") {
        $search->userAgentLike($_POST['user_agent']);
    }
    if ($_POST['duration'] > 0) {
        $search->minDuration($_POST['duration'] * $_POST['duration_multiplier']);
    }

    $entries = $userSessionRepository->getSessionLogs($search);

    if (count($entries) > 20)
        echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /><br/><br/>";
    if (count($entries) > 0) {
        $userNicks = $userRepository->searchUserNicknames();
        echo count($entries) . " Sessions gefunden!<br/><br/>";
        $cnt = 0;
        echo "<table class=\"tb\"><tr>";
        $nid = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($nid == 0)
            echo "<th>Nick</th>";
        echo "<th>Login</th><th>Letzte Aktivit&auml;t</th><th>Logout</th><th>IP/Host</th><th>Client</th><th>Session-Dauer</th>";
        foreach ($entries as $entry) {
            echo "<tr>";
            if ($nid == 0)
                echo "<tr><td><a href=\"?page=$page&amp;sub=edit&amp;user_id=" . $entry->userId . "\">" . $userNicks[$entry->userId] . "</a></td>";
            echo "<td>" . date("d.m.Y H:i", $entry->timeLogin) . "</td>";
            echo "<td>";
            if ($entry->timeAction > 0)
                echo date("d.m.Y H:i", $entry->timeAction);
            else
                echo "-";
            echo "</td>";
            echo "<td>";
            if ($entry->timeLogout > 0)
                echo date("d.m.Y H:i", $entry->timeLogout);
            else
                echo "-";
            echo "</td>";
            echo "<td>" . $entry->ipAddr . "<br/>" . $networkNameService->getHost($entry->ipAddr) . "</td>";
            $browserParser = new \WhichBrowser\Parser($entry->userAgent);
            echo "<td>" . $browserParser->toString() . "</td>";
            echo "<td>";
            if (max($entry->timeLogout, $entry->timeAction) - $entry->timeLogin > 0)
                echo StringUtils::formatTimespan(max($entry->timeLogout, $entry->timeAction) - $entry->timeLogin);
            else
                echo "-";
            echo "</td></tr>";
            $cnt += max((max($entry->timeLogout, $entry->timeAction) - $entry->timeLogin), 0);
        }
        echo "<tr><td colspan=\"7\"></td></tr>";
        echo "<tr><td colspan=\"6\"></td>";
        echo "<td>" . StringUtils::formatTimespan($cnt) . "</td></tr>";
        echo "</table>";
    } else
        echo "<i>Keine Eintr&auml;ge vorhanden</i>";
    echo "<br/><br/><input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
} else {


    echo "<h2>Session-Log</h2>";
    $logCount = $userSessionRepository->countLogs();
    if ($logCount > 0) {
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
        echo "<table class=\"tb\">";
        echo "<tr><th>ID</td><td><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th>Nickname</td><td><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> ";
        echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
        echo "<tr><th>IP-Adresse</td><td><input type=\"text\" name=\"log_ip\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
        echo "<tr><th>Hostname</td><td><input type=\"text\" name=\"log_hostname\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
        echo "<tr><th>Client</td><td><input type=\"text\" name=\"user_agent\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
        echo "</td></tr>";
        echo "<tr><th>Mindestdauer</td><td><input type=\"text\" name=\"duration\" value=\"\" size=\"20\" maxlength=\"250\" /><select name=\"duration_multiplier\">";
        echo "<option value=\"1\">Sekunden</option>";
        echo "<option value=\"60\">Minuten</option>";
        echo "<option value=\"3600\">Stunden</option>";
        echo "</select></td></tr>";
        echo "</table>";
        echo "<br/><input type=\"submit\" name=\"logshow\" value=\"Suche starten\" /></form>";
        echo "<br/>Es sind " . nf($logCount) . " Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>";
    } else
        echo "<i>Keine Eintr&auml;ge vorhanden</i><br/><br/>";
}
