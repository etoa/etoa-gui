<?PHP

use EtoA\User\UserSessionRepository;

iBoxStart("Logins");
echo "Hier findest du deine aktiven Sessions, eine Liste der letzten 10 Logins in deinen Account, ebenfalls kannst du weiter unten
            sehen wann dass fehlerhafte Loginversuche stattgefunden haben. Solltest du feststellen, dass jemand unbefugten
            Zugriff auf deinen Account hatte, solltest du umgehend dein Passwort &auml;ndern und ein " . ticketLink("Ticket", 16) . " schreiben.";
iBoxEnd();

tableStart("Aktive Sessions");
/** @var UserSessionRepository $userSessionRepository */
$userSessionRepository = $app[UserSessionRepository::class];
$activeSessions = $userSessionRepository->getActiveUserSessions($cu->getId());

echo "<tr>
            <th>Login</th>
            <th>Letzte Aktion</th>
            <th>IP-Adresse</th>
            <th>Hostname</th>
            <th>Client</th></tr>";
foreach ($activeSessions as $session) {
    $browserParser = new \WhichBrowser\Parser($session->userAgent);
    echo "<tr><td>" . df($session->timeLogin) . "</td>";
    echo "<td>" . df($session->timeAction) . "</td>";
    echo "<td>" . $session->ipAddr . "</td>";
    echo "<td>" . Net::getHost($session->ipAddr) . "</td>";
    echo "<td>" . $browserParser->toString() . "</td></tr>";
}
tableEnd();

tableStart("Letzte 10 Logins");
$res = dbquery("
            SELECT
                time_login,
                ip_addr ,
                user_agent
            FROM
                user_sessionlog
            WHERE
                user_id=" . $cu->id . "
            ORDER BY
                time_login DESC
            LIMIT
                10;");
echo "<tr><th>Zeit</th>
            <th>IP-Adresse</th>
            <th>Hostname</th>
            <th>Client</th></tr>";
while ($arr = mysql_fetch_array($res)) {
    $browserParser = new \WhichBrowser\Parser($arr['user_agent']);
    echo "<tr><td>" . df($arr['time_login']) . "</td>";
    echo "<td>" . $arr['ip_addr'] . "</td>";
    echo "<td>" . Net::getHost($arr['ip_addr']) . "</td>";
    echo "<td>" . $browserParser->toString() . "</td></tr>";
}
tableEnd();

tableStart("Letzte 10 fehlgeschlagene Logins");
$res = dbquery("
            SELECT
                *
            FROM
                login_failures
            WHERE
                failure_user_id=" . $cu->id . "
            ORDER BY
                failure_time DESC
            LIMIT
                10;");
if (mysql_num_rows($res) > 0) {
    echo "<tr><th>Zeit</th>";
    echo "<th>IP-Adresse</th>
                <th>Hostname</th>
                <th>Client</th></tr>";
    while ($arr = mysql_fetch_array($res)) {
        echo "<tr><td>" . df($arr['failure_time']) . "</td>";
        echo "<td>" . $arr['failure_ip'] . "</td>";
        echo "<td>" . Net::getHost($arr['failure_ip']) . "</td>";
        echo "<td>" . $arr['failure_client'] . "</td></tr>";
    }
} else {
    echo "<tr><td>Keine fehlgeschlagenen Logins</td></tr>";
}
tableEnd();
