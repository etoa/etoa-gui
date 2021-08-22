<?PHP

use EtoA\HostCache\NetworkNameService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSurveillanceRepository;
use EtoA\User\UserSurveillanceSearch;

echo "<h1>Beobachtungsliste</h1>";

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];

if (isset($_GET['text'])) {
    $user = $userRepository->getUser((int) $_GET['text']);
    echo "<h2>Beobachtungsgrund für <a href=\"?page=$page&amp;sub=edit&amp;id=" . $user->id . "\">" . $user->nick . "</a></h2>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
        <textarea name=\"user_observe\" cols=\"80\" rows=\"10\">" . stripslashes($user->observe) . "</textarea>
        <input type=\"hidden\" name=\"user_id\" value=\"" . $user->id . "\" />
        <br/><br/>
        <input type=\"submit\" name=\"save_text\" value=\"Speichern\" /> &nbsp;
        <input type=\"submit\" name=\"del_text\" value=\"Löschen\" /> &nbsp;
        <input type=\"submit\" name=\"cancel\" value=\"Abbrechen\" />";
}

//
// Extended observation
//
elseif (isset($_GET['surveillance']) && $_GET['surveillance'] > 0) {
    $tu = new User($_GET['surveillance']);

    echo "<h2>Erweiterte Beobachtung von " . $tu . "</h2>";

    if (isset($_GET['session'])) {
        $sessionId = $_GET['session'];

        /** @var UserSessionRepository $userSessionRepository */
        $userSessionRepository = $app[UserSessionRepository::class];
        $userSession = $userSessionRepository->findLog($sessionId);
        if ($userSession === null) {
            $userSession = $userSessionRepository->find($sessionId);

            if ($userSession === null) {
                throw new \RuntimeException('User session not found');
            }
        }

        echo "<h3>Session";
        if ($userSession->timeLogin > 0) {
            echo " von " . date("d.m.Y H:i", $userSession->timeLogin);
            if ($userSession->timeAction > 0) {
                echo " bis " . date("d.m.Y H:i", $userSession->timeAction);
            }
        } else {
            echo " $userSession->id";
        }
        echo "</h3>";

        $browserParser = new \WhichBrowser\Parser($userSession->userAgent);
        echo "<p><b>IP:</b> " . $userSession->ipAddr . "<br/>
            <b>Host:</b> " . $networkNameService->getHost($userSession->ipAddr) . "<br/>
            <b>Client:</b> " . $browserParser->toString() . "</p>";

        echo "<p>" . button("Neu laden", "?page=$page&amp;sub=$sub&amp;surveillance=" . $_GET['surveillance'] . "&amp;session=" . $userSession->id) . " &nbsp; " .
            button("Zurück", "?page=$page&amp;sub=$sub&amp;surveillance=" . $_GET['surveillance']) . "</p>";

        /** @var UserSurveillanceRepository $userSuveillanceRepository */
        $userSuveillanceRepository = $app[UserSurveillanceRepository::class];
        $entries = $userSuveillanceRepository->search(UserSurveillanceSearch::create()->session($userSession->id));
        if (count($entries) > 0) {
            tableStart("", "100%");
            echo "<tr><th>Zeit</th><th>Seite</th><th>Request (GET)</th><th>Query String</th><th>Formular (POST)</th></tr>";
            foreach ($entries as $entry) {
                $req = wordwrap($entry->request, 60, "\n", true);
                $reqRaw = wordwrap($entry->requestRaw, 60, "\n", true);
                $post = wordwrap($entry->post, 60, "\n", true);
                echo "<tr>
                        <td>" . StringUtils::formatDate($entry->timestamp) . "</td>
                        <td>" . $entry->page . "</td>
                        <td>" . BBCodeUtils::toHTML($req) . "</td>
                        <td>" . BBCodeUtils::toHTML($reqRaw) . "</td>
                        <td>" . BBCodeUtils::toHTML($post) . "</td>
                    </tr>";
            }
            tableEnd();
        }
    } else {
        /** @var UserSessionRepository $userSessionRepository */
        $userSessionRepository = $app[UserSessionRepository::class];
        /** @var UserSurveillanceRepository $userSuveillanceRepository */
        $userSuveillanceRepository = $app[UserSurveillanceRepository::class];
        $sessions = $userSuveillanceRepository->countPerSession(UserSurveillanceSearch::create()->userId($_GET['surveillance']));

        echo "<p>Die erweiterte Beobachtung ist automatisch für User unter Beobachtung aktiv!</p>";
        echo "<p>" . button("Neu laden", "?page=$page&amp;sub=$sub&amp;surveillance=" . $_GET['surveillance']) . " &nbsp; " . button("Zurück", "?page=$page&amp;sub=$sub") . "</p>";

        echo "<table class=\"tb\"><tr>";
        echo "<th>Login</th>
            <th>Letzte Aktivit&auml;t</th>";
        echo "<th>Session-Dauer</th>
            <th>Aktionen</th>
            <th>Aktionen/Minute</th>
            <th>Optionen</th>
            </tr>";
        foreach ($sessions as $sessionId => $count) {
            if ($count > 0) {
                $userSession = $userSessionRepository->findLog($sessionId);
                if ($userSession === null) {
                    $userSession = $userSessionRepository->find($sessionId);

                    if ($userSession === null) {
                        throw new \RuntimeException('User session not found');
                    }
                }
                echo "<tr>";
                echo "<td>" . ($userSession->timeLogin > 0 ? date("d.m.Y H:i", $userSession->timeLogin) : '-') . "</td>";
                echo "<td>" . ($userSession->timeAction > 0 ? date("d.m.Y H:i", $userSession->timeAction) : '-') . "</td>";
                echo "<td>";
                $dur = max($userSession->timeLogout ?? 0, $userSession->timeAction) - $userSession->timeLogin;
                if ($dur > 0)
                    echo StringUtils::formatTimespan($dur);
                else
                    echo "-";
                if ($dur > 60) {
                    $apm = round($count / $dur * 60, 1);
                } else if ($dur > 0) {
                    $apm = $count;
                } else {
                    $apm = '-';
                }
                echo "</td>
                    <td>" . $count . "</td>
                    <td>" . $apm . "</td>
                    <td><a href=\"?page=$page&sub=$sub&surveillance=" . $_GET['surveillance'] . "&amp;session=" . $sessionId . "\">Details</a></td>
                    </tr>";
            }
        }
        echo "</table>";

        echo "<p>" . button("Zurück", "?page=$page&amp;sub=$sub") . "</p>";
    }
}

//
// List observed users
//
else {
    if (isset($_POST['observe_add'])) {
        $userRepository->updateObserve((int) $_POST['user_id'], $_POST['user_observe']);
    }
    if (isset($_GET['del'])) {
        $userRepository->updateObserve((int) $_POST['del'], null);
    }
    if (isset($_POST['del_text'])) {
        $userRepository->updateObserve((int) $_POST['del'], null);
    }
    if (isset($_POST['save_text'])) {
        $userRepository->updateObserve((int) $_POST['user_id'], $_POST['user_observe']);
    }

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
        <fieldset>
        <legend>Hinzufügen</legend>
        <table class=\"tb\">
        <tr><th>
        Benutzer:</th><td><select name=\"user_id\">";
    $userNicks = $userRepository->searchUserNicknames(UserSearch::create()->notObserved());
    foreach ($userNicks as $userId => $userNick) {
        echo "<option value=\"" . $userId . "\">" . $userNick . "</option>";
    }
    echo "</select></td></tr>
            <tr><th>Grund:</th><td><textarea name=\"user_observe\" cols=\"80\" rows=\"5\">Multiverdacht</textarea></td></tr>
            </table><br/>
         <input type=\"submit\" name=\"observe_add\" value=\"Zur Beobachtungsliste hinzufügen\" />
         </fieldset>
        </form><br/>";

    echo "Folgende User stehen unter Beobachtung:<br/><br/>";
    $users = $userRepository->searchAdminView(UserSearch::create()->observed());
    if (count($users) > 0) {
        echo "<table class=\"tb\">
            <tr>
                <th style=\"width:150px;\">Nick</th>
                <th style=\"width:100px;\">Punkte</th>
                <th>Text</th>
                <th>Online</th>
                <th>Details</th>
                <th style=\"width:200px;\">Optionen</th>
            </tr>";
        foreach ($users as $user) {
            echo "<tr>
                    <td><a href=\"?page=$page&amp;sub=edit&amp;id=" . $user->id . "\">" . $user->nick . "</a></td>
                    <td " . tm("Punkteverlauf", "<img src=\"../misc/stats.image.php?user=" . $user->id . "\" alt=\"Diagramm\" style=\"width:600px;height:400px;\" />") . ">" . StringUtils::formatNumber($user->points) . "</td>
                    <td>" . stripslashes($user->observe) . "</td>";
            if ($user->timeAction > 0)
                echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
            elseif ($user->timeLog > 0)
                echo "<td class=\"tbldata\">" . date("d.m.Y H:i", $user->timeLog) . "</td>";
            else
                echo "<td class=\"tbldata\">Noch nicht eingeloggt!</td>";

            /** @var UserSurveillanceRepository $userSuveillanceRepository */
            $userSuveillanceRepository = $app[UserSurveillanceRepository::class];
            $dnum = $userSuveillanceRepository->count(UserSurveillanceSearch::create()->userId($user->id));
            echo "<td>" . StringUtils::formatNumber($dnum) . "</td>
                    <td>
                        <a href=\"?page=$page&amp;sub=$sub&amp;surveillance=" . $user->id . "\">Details</a>
                        <a href=\"?page=$page&amp;sub=$sub&amp;text=" . $user->id . "\">Text ändern</a>
                        <a href=\"?page=$page&amp;sub=$sub&amp;del=" . $user->id . "\">Entfernen</a>
                    </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<i>Keine gefunden!</i>";
    }
}
