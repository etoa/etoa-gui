<?PHP

use EtoA\HostCache\NetworkNameService;
use EtoA\Support\StringUtils;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSessionSearch;

/** @var UserLoginFailureRepository $userLoginFailureRepository */
$userLoginFailureRepository = $app[UserLoginFailureRepository::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

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
    echo "<tr><td>" . StringUtils::formatDate($session->timeLogin) . "</td>";
    echo "<td>" . StringUtils::formatDate($session->timeAction) . "</td>";
    echo "<td>" . $session->ipAddr . "</td>";
    echo "<td>" . $networkNameService->getHost($session->ipAddr) . "</td>";
    echo "<td>" . $browserParser->toString() . "</td></tr>";
}
tableEnd();

tableStart("Letzte 10 Logins");
$sessionLogs = $userSessionRepository->getSessionLogs(UserSessionSearch::create()->userId($cu->getId()), 10);
echo "<tr><th>Zeit</th>
            <th>IP-Adresse</th>
            <th>Hostname</th>
            <th>Client</th></tr>";
foreach ($sessionLogs as $sessionLog) {
    $browserParser = new \WhichBrowser\Parser($sessionLog->userAgent);
    echo "<tr><td>" . StringUtils::formatDate($sessionLog->timeLogin) . "</td>";
    echo "<td>" . $sessionLog->ipAddr . "</td>";
    echo "<td>" . $networkNameService->getHost($sessionLog->ipAddr) . "</td>";
    echo "<td>" . $browserParser->toString() . "</td></tr>";
}
tableEnd();

tableStart("Letzte 10 fehlgeschlagene Logins");
$failures = $userLoginFailureRepository->getUserLoginFailures($cu->getId(), 10);
if (count($failures) > 0) {
    echo "<tr><th>Zeit</th>";
    echo "<th>IP-Adresse</th>
                <th>Hostname</th>
                <th>Client</th></tr>";
    foreach ($failures as $failure) {
        echo "<tr><td>" . StringUtils::formatDate($failure->time) . "</td>";
        echo "<td>" . $failure->ip . "</td>";
        echo "<td>" . $networkNameService->getHost($failure->ip) . "</td>";
        echo "<td>" . $failure->client . "</td></tr>";
    }
} else {
    echo "<tr><td>Keine fehlgeschlagenen Logins</td></tr>";
}
tableEnd();
