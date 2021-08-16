<?php

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionManager;
use EtoA\User\UserSessionRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var UserSessionManager $userSessionManager */
$userSessionManager = $app[UserSessionManager::class];

/** @var UserSessionRepository $userSessionRepository */
$userSessionRepository = $app[UserSessionRepository::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

echo "<h2>Aktive Sessions</h2>";
echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
echo "<p>Das User-Timeout beträgt " . tf($config->getInt('user_timeout')) . "</p>";

if (isset($_GET['kick'])) {
    $userSessionManager->kick($_GET['kick']);
    success_msg("Session " . $_GET['kick'] . " gelöscht!");
}
if (isset($_POST['kick_all'])) {
    $sessionIds = $userSessionRepository->getUserSessionIds();
    if (count($sessionIds) > 0) {
        foreach ($sessionIds as $sessionId) {
            $userSessionManager->kick($sessionId);
        }
        success_msg("Alle Sessions gelöscht!");
    }
}

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
$userNicks = $userRepository->searchUserNicknames();
$userSessions = $userSessionRepository->getSessions();
if (count($userSessions) > 0) {
    echo "<p>Es sind " . count($userSessions) . " Sessions aktiv. <input type=\"submit\" name=\"kick_all\" value=\"Alle User kicken\" onclick=\"return confirm('Sollen wirklich alle User aus dem Spiel geworfen werden?');\" /></p>";
    echo "<table><tr>
        <th class=\"tbltitle\">Nick</th>
        <th class=\"tbltitle\">Login</th>
        <th class=\"tbltitle\">Letzte Aktion</th>
        <th class=\"tbltitle\">Status</th>
        <th class=\"tbltitle\">IP / Hostname</th>
        <th class=\"tbltitle\">Client</th>
        <th class=\"tbltitle\">Dauer</th>
        <th class=\"tbltitle\">Info</th>
        </tr>";
    foreach ($userSessions as $session) {
        echo "<tr><td class=\"tbldata\"><a href=\"?page=user&sub=edit&user_id=" . $session->userId . "\">" . $userNicks[$session->userId] . "</a></td>
            <td class=\"tbldata\">" . date("d.m.Y H:i", $session->timeLogin) . "</td>
            <td class=\"tbldata\">" . date("d.m.Y  H:i", $session->timeAction) . "</td>";
        if (time() - $config->getInt('user_timeout') < $session->timeAction && $session->id !== '') {
            echo "<td class=\"tbldata\" style=\"color:#0f0\">Online [<a href=\"?page=$page&amp;sub=$sub&amp;kick=" . $session->id . "\">kick</a>]</td>";
        } else
            echo "<td class=\"tbldata\" style=\"color:#f72\">offline</td>";
        echo "<td class=\"tbldata\">" . $session->ipAddr . "<br/>" . $networkNameService->getHost($session->ipAddr) . "</td>";
        $browserParser = new \WhichBrowser\Parser($session->userAgent);
        echo "<td class=\"tbldata\">" . $browserParser->toString() . "</td>";
        echo "<td class=\"tbldata\">";
        if (max($session->timeLogin, $session->timeAction) - $session->timeLogin > 0)
            echo tf($session->timeAction - $session->timeLogin);
        else
            echo "-";
        echo "</td>";
        echo "<input type=\"hidden\" name=\"user_id\" value=\"" . $session->userId . "\">";
        echo "<td class=\"tbldata\"><input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub&id=" . $session->userId . "'\" value=\"Info\" /></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    echo "</form><br/>";
} else
    echo "<br/><br/><i>Keine Einträge vorhanden!</i><br/>";
