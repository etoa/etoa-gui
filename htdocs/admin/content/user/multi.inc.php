<?PHP

use EtoA\Alliance\AllianceRepository;
use EtoA\HostCache\NetworkNameService;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserSittingRepository;

/** @var UserMultiRepository $userMultiRepository */
$userMultiRepository = $app[UserMultiRepository::class];

/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

if (isset($_GET['ip'])) {
    $allianceTags = $allianceRepository->getAllianceTags();

    $ip = $_GET['ip'];
    echo "<h1>Multi-Erkennung - Details</h1>";

    echo "<b>IP:</b> <a href=\"?page=$page&amp;sub=ipsearch&amp;ip=" . $ip . "\">$ip</a><br/>
            <b>Host:</b> <a href=\"?page=$page&amp;sub=ipsearch&amp;host=" . $networkNameService->getHost($ip) . "\">" . $networkNameService->getHost($ip) . "</a><br/><br/>";
    $ipres = dbquery("
            SELECT
                users.user_blocked_from,
                users.user_blocked_to,
                users.user_hmode_from,
                users.admin,
                users.user_ghost,
                user_deleted,
                users.user_alliance_id,
                users.user_id,
                users.user_points,
                users.user_nick,
                user_sessionlog.time_action AS time_log,
                user_sessions.time_action,
                users.user_name,
                users.user_email,
                users.user_email_fix,
                users.user_multi_delets
            FROM
                users
                LEFT JOIN
                    user_sessions
                ON
                users.user_id=user_sessions.user_id
            INNER JOIN
                user_sessionlog
            ON
                users.user_id=user_sessionlog.user_id
                INNER JOIN (
                    SELECT
                        user_id,
                        MAX( time_action ) AS last_action
                    FROM
                        user_sessionlog
                    GROUP BY
                        user_id
                ) AS log
                ON
                    user_sessionlog.user_id = log.user_id
                    AND user_sessionlog.time_action = log.last_action
                    AND (user_sessions.ip_addr='$ip' OR user_sessionlog.ip_addr='$ip')
            ORDER BY
                time_log DESC;");

    echo "<table class=\"tbl\" width=\"100%\">";
    echo "<tr><td class=\"tbltitle\">Nick</td><td class=\"tbltitle\">Name</td><td class=\"tbltitle\">E-Mail</td><td class=\"tbltitle\">Online</td><td class=\"tbltitle\">Punkte</td><td class=\"tbltitle\">Eingetragene Multis</td><td class=\"tbltitle\">Gel&ouml;schte Multis</td></tr>";
    while ($iparr = mysql_fetch_array($ipres)) {
        if ($iparr['admin'])
            $uCol = ' class="adminColor"';
        elseif ($iparr['user_ghost'])
            $uCol = ' class="userGhostColor"';
        elseif ($iparr['user_blocked_from'] < time() && $iparr['user_blocked_to'] > time())
            $uCol = ' class="userLockedColor"';
        elseif ($iparr['user_hmode_from'] > 0)
            $uCol = ' class="userHolidayColor"';
        elseif ($iparr['user_deleted'] > 0)
            $uCol = ' class="userDeletedColor"';
        else
            $uCol = ' class="tbldata"';

        echo "<tr>";
        echo "<td $uCol>
                <a href=\"?page=$page&amp;sub=ipsearch&amp;user=" . $iparr['user_id'] . "\">" . $iparr['user_nick'] . "</a>
                ";
        if ($iparr['user_alliance_id'] > 0) {
            echo "<br/><b>" . $allianceTags[$iparr['user_alliance_id']] . "</b>";
        }
        echo "</td>";
        echo "<td $uCol>" . $iparr['user_name'] . "</td>";
        echo "<td $uCol>" . $iparr['user_email_fix'] . "<br/>" . $iparr['user_email'] . "</td>";
        echo "<td $uCol ";
        if ($iparr['time_action'])
            echo " style=\"color:#0f0;\">online";
        elseif ($iparr['time_log'])
            echo ">" . date("d.m.Y H:i", $iparr['time_log']) . "";
        else
            echo ">Noch nicht eingeloggt!";
        echo "</td><td $uCol>" . nf($iparr['user_points']) . "</td>";

        $multiEntries = $userMultiRepository->getUserEntries((int) $iparr['user_id']);
        if (count($multiEntries) > 0) {
            $multi = 1;

            echo "<td $uCol>";
            foreach ($multiEntries as $entry) {
                echo "<span title=\"" . $entry->reason . "\">" . $entry->multiUserNick . "</span>";

                if ($multi < count($multiEntries)) {
                    echo ", ";
                }

                $multi++;
            }
            echo "</td>";
        } else {
            echo "<td $uCol>-</td>";
        }
        echo "<td $uCol>" . nf($iparr['user_multi_delets']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br/><a href=\"?page=$page&amp;sub=$sub\">Multi-&Uuml;bersicht</a>";
} else {
    echo "<h1>Multi-Erkennung - Zusammenfassung</h1>";
    echo "Multi-Merkmale:</br><ul><li>Gleiche IP (durch dieses Tool pr&uuml;fen)</li><li>&Auml;hnliche Onlinezeit (mit Session-Log pr&uuml;fen)</li><li>evtl. dieselbe Allianz</li><li>&Auml;hnliche Mailadresse</li><li>&Auml;hnliche Fantasienamen</li></ul></br>";
    $res = dbquery("
                           SELECT
                                   user_sessionlog.ip_addr AS log_ip,
                                user_sessions.ip_addr
                            FROM
                                users
                            INNER JOIN
                                user_sessionlog
                            ON
                                users.user_id=user_sessionlog.user_id
                            INNER JOIN (
                                SELECT
                                    user_id,
                                    MAX( time_action ) AS last_action
                                FROM
                                    user_sessionlog
                                GROUP BY
                                    user_id
                            ) AS log
                            ON
                                user_sessionlog.user_id = log.user_id
                                AND user_sessionlog.time_action = log.last_action
                            LEFT JOIN
                                user_sessions
                            ON
                                user_sessionlog.user_id = user_sessions.user_id
                            ;");
    $ips = array();
    while ($arr = mysql_fetch_array($res)) {
        $ip = $arr['ip_addr'] == null ? $arr['log_ip'] : $arr['ip_addr'];
        if (isset($ips[$ip]))
            ++$ips[$ip];
        else
            $ips[$ip] = 1;
    }
    $multi_ips = array();
    foreach ($ips as $ip => $cnt) {
        if ($cnt > 1)
            array_push($multi_ips, $ip);
    }
    if (count($multi_ips) > 0) {
        echo "<table class=\"tbl\" width=\"100%\">";
        echo "<tr><th class=\"tbltitle\">IP-Adresse</th><th class=\"tbltitle\">Nick</th><th class=\"tbltitle\">Realer Name</th><th class=\"tbltitle\">Zuletzt online</th><th class=\"tbltitle\">Eingetragene Multis</th><th class=\"tbltitle\">Wird gesittet von:</th></tr>";
        $multi_ip_cnt = 0;
        $multi_total_cnt = 0;
        foreach ($multi_ips as $ip) {
            $ipres = dbquery("
                    SELECT
                        users.user_blocked_from,
                        users.user_blocked_to,
                        users.user_hmode_from,
                        users.user_deleted,
                        users.admin,
                        users.user_ghost,
                        users.user_alliance_id,
                        users.user_id,
                        users.user_points,
                        users.user_nick,
                        user_sessionlog.time_action AS time_log,
                        user_sessions.time_action,
                        users.user_name,
                        users.user_email
                    FROM
                        users
                        LEFT JOIN
                            user_sessions
                        ON
                        users.user_id=user_sessions.user_id
                    INNER JOIN
                        user_sessionlog
                    ON
                        users.user_id=user_sessionlog.user_id
                        INNER JOIN (
                            SELECT
                                user_id,
                                MAX( time_action ) AS last_action
                            FROM
                                user_sessionlog
                            GROUP BY
                                user_id
                        ) AS log
                        ON
                            user_sessionlog.user_id = log.user_id
                            AND user_sessionlog.time_action = log.last_action
                            AND (user_sessions.ip_addr='$ip' OR user_sessionlog.ip_addr='$ip')
                    ORDER BY
                        time_log DESC;");

            if ($iparr['admin'])
                $uCol = ' class="adminColor"';
            elseif ($iparr['user_ghost'])
                $uCol = ' class="userGhostColor"';
            elseif ($iparr['user_blocked_from'] < time() && $iparr['user_blocked_to'] > time())
                $uCol = ' class="userLockedColor"';
            elseif ($iparr['user_hmode_from'] > 0)
                $uCol = ' class="userHolidayColor"';
            elseif ($iparr['user_deleted'] > 0)
                $uCol = ' class="userDeletedColor"';
            else
                $uCol = ' class="tbldata"';


            echo "<tr>
                    <td rowspan=\"" . mysql_num_rows($ipres) . "\" valign=\"top\" class=\"tbldata\">
                        <a href=\"?page=$page&amp;sub=$sub&amp;ip=$ip\">
                            $ip
                        </a>
                    </td>";
            $cnt = 0;
            while ($iparr = mysql_fetch_array($ipres)) {
                if ($cnt != 0) echo "<tr>";
                else $cnt = 1;

                echo "<td $uCol><a href=\"?page=user&sub=edit&id=" . $iparr['user_id'] . "\">" . $iparr['user_nick'] . "</a></td>";
                echo "<td $uCol title=\"" . $iparr['user_email'] . "\">" . $iparr['user_name'] . "</td>";
                echo "<td $uCol";
                if ($iparr['time_action'])
                    echo " style=\"color:#0f0;\">online";
                elseif ($iparr['time_log'])
                    echo ">" . date("d.m.Y H:i", $iparr['time_log']) . "";
                else
                    echo ">Noch nicht eingeloggt!";
                echo "</td>";

                $multiEntries = $userMultiRepository->getUserEntries((int) $iparr['user_id']);
                if (count($multiEntries) > 0) {
                    $multi = 1;

                    echo "<td $uCol>";
                    foreach ($multiEntries as $entry) {
                        echo "<span title=\"" . $entry->reason . "\"><a href=\"?page=user&sub=edit&id=" . $entry->multiUserId . "\">" . $entry->multiUserNick . "</a></span>";

                        if ($multi < count($multiEntries)) {
                            echo ", ";
                        }

                        $multi++;
                    }
                    echo "</td>";
                } else {
                    echo "<td $uCol>-</td>";
                }

                /** @var UserSittingRepository $userSittingRepository */
                $userSittingRepository = $app[UserSittingRepository::class];
                $entry = $userSittingRepository->getActiveUserEntry((int) $iparr['user_id']);
                if ($entry !== null)
                    echo "<td>" . $entry->sitterNick . "</td></tr>";
                else
                    echo "<td>-</td></tr>";

                $multi_total_cnt++;
            }
            $multi_ip_cnt++;
        }
        echo "</table>";
        echo "<p>Total $multi_ip_cnt IP-Adressen mit $multi_total_cnt Spielern entdeckt.</p>";
    } else
        echo "<br/><i>Nichts gefunden!</i>";
}
