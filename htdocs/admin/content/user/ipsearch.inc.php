<?PHP

use EtoA\HostCache\NetworkNameService;
use EtoA\Support\StringUtils;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSessionSearch;

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var UserSessionRepository $userSessionRepository */
$userSessionRepository = $app[UserSessionRepository::class];
/** @var UserLoginFailureRepository $userLoginFailureRepository */
$userLoginFailureRepository = $app[UserLoginFailureRepository::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

echo "<h1>Ip- und Hostsuche</h1>";

if (isset($_POST['search'])) {
    $_GET['ip'] = $_POST['ip'];
    $_GET['host'] = $_POST['host'];
}

if (isset($_GET['ip'])  && $_GET['ip'] != "")
    $ip = $_GET['ip'];
elseif (isset($_GET['host'])  && $_GET['host'] != "")
    $ip = $networkNameService->getAddr($_GET['host']);
else
    $ip = "";

if (isset($_GET['host']) && $_GET['host'] != "")
    $host = $_GET['host'];
elseif ($ip != "")
    $host = $networkNameService->getHost($ip);
else
    $host = "";

if (isset($_GET['user']))
    $user = $_GET['user'];
else
    $user = 0;

if ($user > 0) {
    echo "<h2>Suchergebnisse</h2>";

    $userNick = $userRepository->getNick($user);
    if ($userNick !== null) {

        echo "<b>Nick:</b> <a href=\"?page=$page&amp;sub=edit&amp;id=" . $user . "\">" . $userNick . "</a><br/>";

        if (!isset($_SESSION['admin_ipsearch_concat']))
            $_SESSION['admin_ipsearch_concat'] = false;
        if (isset($_GET['cc']) && $_GET['cc'] == 1)
            $_SESSION['admin_ipsearch_concat'] = true;
        if (isset($_GET['cc']) && $_GET['cc'] == 0)
            $_SESSION['admin_ipsearch_concat'] = false;

        echo "<br/>[ ";
        if (!$_SESSION['admin_ipsearch_concat'])
            echo "<a href=\"?page=$page&amp;sub=$sub&amp;user=" . $user . "&amp;cc=1\">Zusammenfassung</a>";
        else
            echo "Zusammenfassung";
        echo " | ";
        if ($_SESSION['admin_ipsearch_concat'])
            echo "<a href=\"?page=$page&amp;sub=$sub&amp;user=" . $user . "&amp;cc=0\">Details</a>";
        else
            echo "Details";
        echo " ]<br/>";


        echo "<h3>Adressen mit denen dieser User bereits online war</h3>";
        if ($_SESSION['admin_ipsearch_concat']) {
            $ipCounts = $userSessionRepository->logCountPerIp(UserSessionSearch::create()->userId($user));
            if (count($ipCounts) > 0) {
                echo "<table class=\"tb\">
                    <tr>
                    <th style=\"width:150px;\">Anzahl</th>
                    <th style=\"width:130px;\">IP</th>
                    <th style=\"width:130px;\">Host</th>
                    </tr>";
                foreach ($ipCounts as $ipAddr => $count) {
                    echo "<tr>
                        <td>" . StringUtils::formatNumber($count) . "</td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $ipAddr . "\">" . $ipAddr . "</a></td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;host=" . $networkNameService->getHost($ipAddr) . "\">" . $networkNameService->getHost($ipAddr) . "</a></td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "<i>Nichts gefunden!</i>";
            }

            echo "<h3>Fehlgeschlagene Logins dieses Users</h3>";
            $userFailureCounts = $userLoginFailureRepository->getLoginFailureCountsByUser($user);
            if (count($userFailureCounts) > 0) {
                echo "<table class=\"tb\">";
                echo "<tr>
                    <th>Anzahl</a></th>
                    <th>IP</a></th>
                    <th>Host</th>
                    </tr>";
                foreach ($userFailureCounts as $failure) {
                    echo "<tr>
                        <td>" . $failure['count'] . "</td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $failure['ip'] . "\">" . $failure['ip'] . "</a></td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;host=" . $failure['host'] . "\">" . $failure['host'] . "</a></td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "<i>Keine fehlgeschlagenen Logins</i>";
            }
        } else {
            /** @var UserSessionRepository $userSessionRepository */
            $userSessionRepository = $app[UserSessionRepository::class];
            $sessionLogs = $userSessionRepository->getSessionLogs(UserSessionSearch::create()->userId($user));
            if (count($sessionLogs) > 0) {
                echo "<table class=\"tb\">
                    <tr>
                    <th style=\"width:130px;\">IP</th>
                    <th style=\"width:130px;\">Host</th>
                    <th style=\"width:150px;\">Datum/Zeit</th>
                    <th>Client</th></tr>";
                foreach ($sessionLogs as $sessionLog) {
                    $browserParser = new \WhichBrowser\Parser($sessionLog->userAgent);
                    echo "<tr>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $sessionLog->ipAddr . "\">" . $sessionLog->ipAddr . "</a></td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;host=" . $networkNameService->getHost($sessionLog->ipAddr) . "\">" . $networkNameService->getHost($sessionLog->ipAddr) . "</a></td>
                        <td>" . StringUtils::formatDate($sessionLog->timeAction) . "</td>
                        <td>" . $browserParser->toString() . "</td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "<i>Nichts gefunden!</i>";
            }

            echo "<h3>Fehlgeschlagene Logins dieses Users</h3>";
            $failures = $userLoginFailureRepository->getUserLoginFailures($user);
            if (count($failures) > 0) {
                echo "<table class=\"tb\">";
                echo "<tr>
                    <th>IP</a></th>
                    <th>Host</th>
                    <th>Datum/Zeit</th>
                    <th>Client</th>
                    </tr>";
                foreach ($failures as $failure) {
                    echo "<tr>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $failure->ip . "\">" . $failure->ip . "</a></td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;host=" . $failure->host . "\">" . $failure->host . "</a></td>
                        <td>" . StringUtils::formatDate($failure->time) . "</td>
                        <td>" . $failure->client . "</td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "<i>Keine fehlgeschlagenen Logins</i>";
            }
        }
    } else {
        echo "<i>Benutzer nicht gefunden!</i>";
    }

    echo "<br/><br/><a href=\"?page=$page&amp;sub=$sub\">Zurück zur Suche</a>";
} elseif ($ip != "" || $host != "") {

    echo "<h2>Suchergebnisse</h2>";

    echo "<b>IP:</b> <a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $ip . "\">" . $ip . "</a><br/>
        <b>Host:</b> <a href=\"?page=$page&amp;sub=$sub&amp;host=" . $host . "\">" . $host . "</a><br/>";

    if (!isset($_SESSION['admin_ipsearch_concat']))
        $_SESSION['admin_ipsearch_concat'] = false;
    if (isset($_GET['cc']) && $_GET['cc'] == 1)
        $_SESSION['admin_ipsearch_concat'] = true;
    if (isset($_GET['cc']) && $_GET['cc'] == 0)
        $_SESSION['admin_ipsearch_concat'] = false;

    echo "<br/>[ ";
    if (!$_SESSION['admin_ipsearch_concat'])
        echo "<a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $ip . "&amp;host=" . $host . "&amp;cc=1\">Zusammenfassung</a>";
    else
        echo "Zusammenfassung";
    echo " | ";
    if ($_SESSION['admin_ipsearch_concat'])
        echo "<a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $ip . "&amp;host=" . $host . "&amp;cc=0\">Details</a>";
    else
        echo "Details";
    echo " ]<br/>";

    if ($_SESSION['admin_ipsearch_concat']) {
        $userNicks = $userRepository->searchUserNicknames();

        echo "<h3>User welche momentan unter dieser Adresse online sind</h3>";
        $sessionCounts = $userSessionRepository->countPerUserId(UserSessionSearch::create()->ip($ip));
        if (count($sessionCounts) > 0) {
            echo "<table class=\"tb\">
                <tr>
                <th style=\"width:50px;\">Anzahl</th>
                <th>Nick</th>
                </tr>";
            foreach ($sessionCounts as $userId => $count) {
                echo "<tr>
                    <td>" . StringUtils::formatNumber($count) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;user=" . $userId . "\">" . $userNicks[$userId] . "</a></td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Nichts gefunden!</i>";
        }

        echo "<h3>User welche schon mal unter dieser Adresse online waren</h3>";
        $sessionLogCounts = $userSessionRepository->logCountPerUserId(UserSessionSearch::create()->ip($ip));
        if (count($sessionLogCounts) > 0) {
            echo "<table class=\"tb\">
                <tr>
                <th style=\"width:50px;\">Anzahl</th>
                <th>Nick</th>
                </tr>";
            foreach ($sessionLogCounts as $userId => $count) {
                echo "<tr>
                    <td>" . StringUtils::formatNumber($count) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;user=" . $userId . "\">" . $userNicks[$userId] . "</a></td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Nichts gefunden!</i>";
        }

        echo "<h3>Fehlgeschlagene Logins unter dieser Adresse</h3>";
        $failures = $userLoginFailureRepository->getLoginFailureCountsByIp($ip);
        if (count($failures) > 0) {
            echo "<table class=\"tb\">";
            echo "<tr>
                <th>Anzahl</a></th>
                <th>Nick</a></th>
                </tr>";
            foreach ($failures as $failure) {
                echo "<tr>
                    <td>" . StringUtils::formatNumber($failure['count']) . "</td>
                    <td><a href=\"?page=user&amp;sub=$sub&amp;user=" . $failure['userId'] . "\">" . $failure['userNick'] . "</a></td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Keine fehlgeschlagenen Logins</i>";
        }
    } else {
        echo "<h3>User welche momentan unter dieser Adresse online sind</h3>";
        $userNicks = $userRepository->searchUserNicknames();
        $sessions = $userSessionRepository->getSessions(UserSessionSearch::create()->ip($ip));
        if (count($sessions) > 0) {
            echo "<table class=\"tb\">
                <tr>
                <th style=\"width:130px;\">Nick</th>
                <th style=\"width:150px;\">Datum/Zeit</th>
                <th style=\"width:60px;\">Match</th>
                <th>Client</th></tr>";
            foreach ($sessions as $session) {
                $browserParser = new \WhichBrowser\Parser($session->userAgent);
                echo "<div id=\"tt" . $session->userId . "\" style=\"display:none;\">
                    <a href=\"?page=user&amp;sub=ipsearch&amp;user=" . $session->userId . "\">IP-Adressen suchen</a><br/>
                    <a href=\"?page=$page&amp;sub=edit&amp;id=" . $session->userId . "\">Daten bearbeiten</a><br/>
                    </div>";

                echo "<tr>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;user=" . $session->userId . "\" " . cTT($userNicks[$session->userId], "tt" . $session->userId) . ">" . $userNicks[$session->userId] . "</a></td>
                    <td>" . StringUtils::formatDate($session->timeAction) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $session->ipAddr . "\" " . mTT('IP', $session->ipAddr) . ">" . ($ip == $session->ipAddr ? 'IP' : '-') . "</a> /
                    <a href=\"?page=$page&amp;sub=$sub&amp;host=" . $networkNameService->getHost($session->ipAddr) . "\" " . mTT('Host', $networkNameService->getHost($session->ipAddr)) . ">" . ($host == $networkNameService->getHost($session->ipAddr) ? 'Host' : '-') . "</a></td>
                    <td>" . $browserParser->toString() . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Nichts gefunden!</i>";
        }

        echo "<h3>User welche schon mal unter dieser Adresse online waren</h3>";
        $sessionLogs = $userSessionRepository->getSessionLogs(UserSessionSearch::create()->ip($ip));
        if (count($sessionLogs) > 0) {
            echo "<table class=\"tb\">
                <tr>
                <th style=\"width:130px;\">Nick</th>
                <th style=\"width:150px;\">Datum/Zeit</th>
                <th style=\"width:60px;\">Match</th>
                <th>Client</th></tr>";
            foreach ($sessionLogs as $log) {
                $browserParser = new \WhichBrowser\Parser($log->userAgent);
                echo "<div id=\"tt" . $log->userId . "\" style=\"display:none;\">
                    <a href=\"?page=user&amp;sub=ipsearch&amp;user=" . $log->userId . "\">IP-Adressen suchen</a><br/>
                    <a href=\"?page=$page&amp;sub=edit&amp;id=" . $log->userId . "\">Daten bearbeiten</a><br/>
                    </div>";

                echo "<tr>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;user=" . $log->userId . "\" " . cTT($userNicks[$log->userId], "tt" . $log->userId) . ">" . $userNicks[$log->userId] . "</a></td>
                    <td>" . StringUtils::formatDate($log->timeAction) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $log->ipAddr . "\" " . mTT('IP', $log->ipAddr) . ">" . ($ip == $log->ipAddr ? 'IP' : '-') . "</a> /
                    <a href=\"?page=$page&amp;sub=$sub&amp;host=" . $networkNameService->getHost($log->ipAddr) . "\" " . mTT('Host', $networkNameService->getHost($log->ipAddr)) . ">" . ($host == $networkNameService->getHost($log->ipAddr) ? 'Host' : '-') . "</a></td>
                    <td>" . $browserParser->toString() . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Nichts gefunden!</i>";
        }

        echo "<h3>Fehlgeschlagene Logins unter dieser Adresse</h3>";
        $ipFailures = $userLoginFailureRepository->getIpLoginFailures($ip);
        if (count($ipFailures) > 0) {
            echo "<table class=\"tb\">";
            echo "<tr>
                <th>Nick</a></th>
                <th>Datum/Zeit</th>
                <th>Match</th>
                <th>Client</th>
                </tr>";
            foreach ($ipFailures as $failure) {
                echo "<div id=\"tt" . $failure->userId . "\" style=\"display:none;\">
                    <a href=\"?page=user&amp;sub=ipsearch&amp;user=" . $failure->userId . "\">IP-Adressen suchen</a><br/>
                    <a href=\"?page=$page&amp;sub=edit&amp;id=" . $failure->userId . "\">Daten bearbeiten</a><br/>
                    </div>";
                echo "<tr>
                    <td><a href=\"?page=user&amp;sub=$sub&amp;user=" . $failure->userId . "\" " . cTT($failure->userNick, "tt" . $failure->userId) . ">" . $failure->userNick . "</a></td>
                    <td>" . StringUtils::formatDate($failure->time) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $failure->ip . "\" " . mTT('IP', $failure->ip) . ">" . ($ip == $failure->ip ? 'IP' : '-') . "</a> /
                    <a href=\"?page=$page&amp;sub=$sub&amp;host=" . $networkNameService->getHost($failure->ip) . "\" " . mTT('Host', $networkNameService->getHost($failure->ip)) . ">" . ($host == $networkNameService->getHost($failure->ip) ? 'Host' : '-') . "</a></td>
                    <td>" . $failure->client . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Keine fehlgeschlagenen Logins</i>";
        }
    }

    echo "<br/><br/><a href=\"?page=$page&amp;sub=$sub\">Zurück zur Suche</a>";
} else {
    echo "<h2>Suchmaske</h2>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
        <table class=\"tb\">
        <tr>
            <th>IP:</th><td><input type=\"text\" name=\"ip\" value=\"\" /></td>
        </tr>
        <tr>
            <th>Host:</th><td><input type=\"text\" name=\"host\" value=\"\" /></td>
        </tr>
        </table><br/>
        <input type=\"submit\" name=\"search\" value=\"Suchen\" />
        </form>";
}
