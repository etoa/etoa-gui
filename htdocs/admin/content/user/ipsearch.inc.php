<?PHP

use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var UserLoginFailureRepository $userLoginFailureRepository */
$userLoginFailureRepository = $app[UserLoginFailureRepository::class];

echo "<h1>Ip- und Hostsuche</h1>";

if (isset($_POST['search'])) {
    $_GET['ip'] = $_POST['ip'];
    $_GET['host'] = $_POST['host'];
}

if (isset($_GET['ip'])  && $_GET['ip'] != "")
    $ip = $_GET['ip'];
elseif (isset($_GET['host'])  && $_GET['host'] != "")
    $ip = Net::getAddr($_GET['host']);
else
    $ip = "";

if (isset($_GET['host']) && $_GET['host'] != "")
    $host = $_GET['host'];
elseif ($ip != "")
    $host = Net::getHost($ip);
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
            $res = dbquery("
                SELECT
                    COUNT(ip_addr) as cnt,
                    ip_addr
                FROM
                    user_sessionlog
                WHERE
                    user_id=" . $user . "
                GROUP BY
                    ip_addr
                ORDER BY
                    cnt DESC
                ;");
            if (mysql_num_rows($res) > 0) {
                echo "<table class=\"tb\">
                    <tr>
                    <th style=\"width:150px;\">Anzahl</th>
                    <th style=\"width:130px;\">IP</th>
                    <th style=\"width:130px;\">Host</th>
                    </tr>";
                while ($arr = mysql_fetch_array($res)) {
                    echo "<tr>
                        <td>" . nf($arr['cnt']) . "</td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $arr['ip_addr'] . "\">" . $arr['ip_addr'] . "</a></td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;host=" . Net::getHost($arr['ip_addr']) . "\">" . Net::getHost($arr['ip_addr']) . "</a></td>
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
            $sessionLogs = $userSessionRepository->getUserSessionLogs($user);
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
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $sessionLog->ip . "\">" . $sessionLog->ip . "</a></td>
                        <td><a href=\"?page=$page&amp;sub=$sub&amp;host=" . Net::getHost($sessionLog->ip) . "\">" . Net::getHost($sessionLog->ip) . "</a></td>
                        <td>" . df($sessionLog->timeAction) . "</td>
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
                        <td>" . df($failure->time) . "</td>
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
        echo "<h3>User welche momentan unter dieser Adresse online sind</h3>";
        $res = dbquery("
            SELECT
                users.user_id,
                users.user_nick,
                COUNT(user_sessions.user_id) AS cnt
            FROM
                user_sessions
            INNER JOIN
                users
            ON
                users.user_id = user_sessions.user_id
                AND user_sessions.ip_addr='" . $ip . "'
            GROUP BY
                user_sessions.user_id
            ORDER BY
                cnt DESC
            ;");
        if (mysql_num_rows($res) > 0) {
            echo "<table class=\"tb\">
                <tr>
                <th style=\"width:50px;\">Anzahl</th>
                <th>Nick</th>
                </tr>";
            while ($arr = mysql_fetch_array($res)) {
                echo "<tr>
                    <td>" . nf($arr['cnt']) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;user=" . $arr['user_id'] . "\">" . $arr['user_nick'] . "</a></td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Nichts gefunden!</i>";
        }

        echo "<h3>User welche schon mal unter dieser Adresse online waren</h3>";
        $res = dbquery("
            SELECT
                users.user_id,
                users.user_nick,
                COUNT(user_sessionlog.user_id) AS cnt
            FROM
                user_sessionlog
            INNER JOIN
                users
            ON
                users.user_id = user_sessionlog.user_id
                AND user_sessionlog.ip_addr='" . $ip . "'
            GROUP BY
                user_sessionlog.user_id
            ORDER BY
                cnt DESC
            ;");
        if (mysql_num_rows($res) > 0) {
            echo "<table class=\"tb\">
                <tr>
                <th style=\"width:50px;\">Anzahl</th>
                <th>Nick</th>
                </tr>";
            while ($arr = mysql_fetch_array($res)) {
                echo "<tr>
                    <td>" . nf($arr['cnt']) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;user=" . $arr['user_id'] . "\">" . $arr['user_nick'] . "</a></td>
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
                    <td>" . nf($failure['count']) . "</td>
                    <td><a href=\"?page=user&amp;sub=$sub&amp;user=" . $failure['userId'] . "\">" . $failure['userNick'] . "</a></td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Keine fehlgeschlagenen Logins</i>";
        }
    } else {
        echo "<h3>User welche momentan unter dieser Adresse online sind</h3>";
        $res = dbquery("
            SELECT
                users.user_id,
                users.user_nick,
                user_sessions.time_action,
                user_sessions.user_agent,
                user_sessions.ip_addr
            FROM
                user_sessions
            INNER JOIN
                users
            ON
                users.user_id = user_sessions.user_id
                AND user_sessions.ip_addr='" . $ip . "'
            ORDER BY
                time_action DESC
            ;");
        if (mysql_num_rows($res) > 0) {
            echo "<table class=\"tb\">
                <tr>
                <th style=\"width:130px;\">Nick</th>
                <th style=\"width:150px;\">Datum/Zeit</th>
                <th style=\"width:60px;\">Match</th>
                <th>Client</th></tr>";
            while ($arr = mysql_fetch_array($res)) {
                $browserParser = new \WhichBrowser\Parser($arr['user_agent']);
                echo "<div id=\"tt" . $arr['user_id'] . "\" style=\"display:none;\">
                    <a href=\"?page=user&amp;sub=ipsearch&amp;user=" . $arr['user_id'] . "\">IP-Adressen suchen</a><br/>
                    <a href=\"?page=$page&amp;sub=edit&amp;id=" . $arr['user_id'] . "\">Daten bearbeiten</a><br/>
                    </div>";

                echo "<tr>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;user=" . $arr['user_id'] . "\" " . cTT($arr['user_nick'], "tt" . $arr['user_id']) . ">" . $arr['user_nick'] . "</a></td>
                    <td>" . df($arr['time_action']) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $arr['ip_addr'] . "\" " . mTT('IP', $arr['ip_addr']) . ">" . ($ip == $arr['ip_addr'] ? 'IP' : '-') . "</a> /
                    <a href=\"?page=$page&amp;sub=$sub&amp;host=" . Net::getHost($arr['ip_addr']) . "\" " . mTT('Host', Net::getHost($arr['ip_addr'])) . ">" . ($host == Net::getHost($arr['ip_addr']) ? 'Host' : '-') . "</a></td>
                    <td>" . $browserParser->toString() . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Nichts gefunden!</i>";
        }

        echo "<h3>User welche schon mal unter dieser Adresse online waren</h3>";
        $res = dbquery("
            SELECT
                users.user_id,
                users.user_nick,
                user_sessionlog.time_action,
                user_sessionlog.user_agent,
                user_sessionlog.ip_addr
            FROM
                user_sessionlog
            INNER JOIN
                users
            ON
                users.user_id = user_sessionlog.user_id
                AND user_sessionlog.ip_addr='" . $ip . "'
            ORDER BY
                time_action DESC
            ;");
        if (mysql_num_rows($res) > 0) {
            echo "<table class=\"tb\">
                <tr>
                <th style=\"width:130px;\">Nick</th>
                <th style=\"width:150px;\">Datum/Zeit</th>
                <th style=\"width:60px;\">Match</th>
                <th>Client</th></tr>";
            while ($arr = mysql_fetch_array($res)) {
                $browserParser = new \WhichBrowser\Parser($arr['user_agent']);
                echo "<div id=\"tt" . $arr['user_id'] . "\" style=\"display:none;\">
                    <a href=\"?page=user&amp;sub=ipsearch&amp;user=" . $arr['user_id'] . "\">IP-Adressen suchen</a><br/>
                    <a href=\"?page=$page&amp;sub=edit&amp;id=" . $arr['user_id'] . "\">Daten bearbeiten</a><br/>
                    </div>";

                echo "<tr>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;user=" . $arr['user_id'] . "\" " . cTT($arr['user_nick'], "tt" . $arr['user_id']) . ">" . $arr['user_nick'] . "</a></td>
                    <td>" . df($arr['time_action']) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $arr['ip_addr'] . "\" " . mTT('IP', $arr['ip_addr']) . ">" . ($ip == $arr['ip_addr'] ? 'IP' : '-') . "</a> /
                    <a href=\"?page=$page&amp;sub=$sub&amp;host=" . Net::getHost($arr['ip_addr']) . "\" " . mTT('Host', Net::getHost($arr['ip_addr'])) . ">" . ($host == Net::getHost($arr['ip_addr']) ? 'Host' : '-') . "</a></td>
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
                    <td>" . df($failure->time) . "</td>
                    <td><a href=\"?page=$page&amp;sub=$sub&amp;ip=" . $failure->ip . "\" " . mTT('IP', $failure->ip) . ">" . ($ip == $failure->ip ? 'IP' : '-') . "</a> /
                    <a href=\"?page=$page&amp;sub=$sub&amp;host=" . Net::getHost($failure->ip) . "\" " . mTT('Host', Net::getHost($failure->ip)) . ">" . ($host == Net::getHost($failure->ip) ? 'Host' : '-') . "</a></td>
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
