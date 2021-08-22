<?PHP

use EtoA\BuddyList\BuddyListRepository;
use EtoA\Message\MessageRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;

echo "<h1>Buddylist</h1>";
echo "F&uuml;ge Freunde zu deiner Buddylist hinzu um auf einen Blick zu sehen wer alles online ist:<br/><br/>";

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var MessageRepository $messageRepository */
$messageRepository = $app[MessageRepository::class];
/** @var BuddyListRepository $buddyListRepository */
$buddyListRepository = $app[BuddyListRepository::class];

//
// Erlaubnis erteilen
//
if (isset($_GET['allow']) && intval($_GET['allow']) > 0) {
    $blid = intval($_GET['allow']);
    if ($buddyListRepository->acceptBuddyRequest($cu->getId(), $blid)) {
        success_msg("Erlaubnis erteilt!");
    } else
        error_msg("Die Erlaubnis kann nicht erteilt werden weil die Anfrage gel&ouml;scht wurde!");
}

//
// Erlaubnis verweigern
//
if (isset($_GET['deny']) && intval($_GET['deny']) > 0) {
    $blid = intval($_GET['deny']);
    if ($buddyListRepository->rejectBuddyRequest($cu->getId(), $blid)) {
        success_msg("Die Anfrage wurde gel&ouml;scht!");
    } else
        error_msg("Die Anfrage konnte nicht gel&ouml;scht werden weil sie nicht mehr existiert!");
}

//
// Freund hinzufÃŒgen
//
if ((isset($_POST['buddy_nick']) && $_POST['buddy_nick'] != "" && $_POST['submit_buddy'] != "") || (isset($_GET['add_id']) && intval($_GET['add_id']) > 0)) {
    $userNick = $_POST['buddy_nick'];
    $userId = $userRepository->getUserIdByNick($userNick);
    if ($userId !== null) {
        if ($cu->id != $userId) {
            if (!$buddyListRepository->buddyListEntryExist($cu->getId(), $userId)) {
                $buddyListRepository->addBuddyRequest($cu->getId(), $userId);
                success_msg("[b]" . $userNick . "[/b] wurde zu deiner Liste hinzugef&uuml;gt und ihm wurde eine Best&auml;tigungsnachricht gesendet!");

                $messageRepository->createSystemMessage($userId, 5, "Buddylist-Anfrage von " . $cu->nick, "Der Spieler will dich zu seiner Freundesliste hinzuf&uuml;gen.\n\n[page buddylist]Anfrage bearbeiten[/page]");
            } else
                error_msg("Dieser Eintrag ist schon vorhanden!");
        } else
            error_msg("Du kannst nicht dich selbst zur Buddyliste hinzuf&uuml;gen!");
    } else
        error_msg("Der Spieler [b]" . $_POST['buddy_nick'] . "[/b] konnte nicht gefunden werden!");
}

//
// Entfernen
//
if (isset($_GET['remove']) && intval($_GET['remove']) > 0) {
    $rmid = intval($_GET['remove']);
    if ($buddyListRepository->removeBuddy($cu->getId(), $rmid)) {
        success_msg("Der Spieler wurde von der Freundesliste entfern!");
    }
}

if (isset($_GET['comment']) && intval($_GET['comment']) > 0) {
    $blid = intval($_GET['comment']);
    $buddy = $buddyListRepository->getBuddy($cu->getId(), $blid);
    if ($buddy !== null) {
        echo "<form action=\"?page=$page\" method=\"post\">";
        iBoxStart("Kommentar f&uuml;r " . $buddy->userNick . "");
        echo "<textarea name=\"bl_comment\" rows=\"5\" cols=\"60\">" . stripslashes($buddy->comment) . "</textarea>";
        iBoxEnd();

        echo "<input type=\"hidden\" name=\"buddy_id\" value=\"" . $buddy->userId . "\" />";
        echo "<input type=\"submit\" name=\"cmt_submit\" value=\"Speichern\" /> ";
        echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Abbrechen\" />";
        echo "</form>";
    } else {
        echo "Daten nicht gefunden!";
    }
} else {
    if (isset($_POST['cmt_submit'])) {
        $buddyListRepository->updateComment($cu->getId(), (int) $_POST['buddy_id'], $_POST['bl_comment']);
    }

    /** @var \EtoA\BuddyList\Buddy[] $buddies */
    $buddies = $buddyListRepository->getBuddies($cu->getId());
    if (count($buddies) > 0) {
        tableStart("Meine Freunde");
        echo "<tr>
                            <th>Nick</th>
                            <th>Punkte</th>
                            <th>Hauptplanet</th>
                            <th>Online</th>
                            <th>Kommentar</th>
                            <th>Aktion</th>
                    </tr>";
        foreach ($buddies as $buddy) {
            echo "<tr>
                            <td>" . $buddy->userNick . "</td>";
            if ($buddy->allowed) {
                $tp = Planet::getById($buddy->planetId);
                echo "<td>" . StringUtils::formatNumber($buddy->points) . "</td>";
                echo "<td><a href=\"?page=cell&amp;id=" . $tp->cellId() . "&amp;hl=" . $tp->id() . "\">" . $tp . "</a></td>";
                if ($buddy->isOnline)
                    echo "<td style=\"color:#0f0;\">online</td>";
                elseif ($buddy->lastActionLogTimestamp > 0)
                    echo "<td>" . date("d.m.Y H:i", $buddy->lastActionLogTimestamp) . "</td>";
                else
                    echo "<td>Noch nicht eingeloggt!</td>";
            } else
                echo "<td colspan=\"3\"><i>Noch keine Erlaubnis</i></td>";
            echo "<td>";
            if ($buddy->comment != "") {
                echo BBCodeUtils::toHTML($buddy->comment);
            }
            echo "</td>";
            echo "<td>
                                    <a href=\"?page=messages&mode=new&message_user_to=" . $buddy->userId . "\" title=\"Nachricht\">Nachricht</a>
                                    <a href=\"?page=userinfo&amp;id=" . $buddy->userId . "\" title=\"Info\">Profil</a><br/>
                                    <a href=\"?page=$page&comment=" . $buddy->userId . "\" title=\"Kommentar bearbeiten\">Kommentar</a> ";
            echo "<a href=\"?page=$page&remove=" . $buddy->userId . "\" onclick=\"return confirm('Willst du " . $buddy->userNick . " wirklich von deiner Liste entfernen?');\">Entfernen</a></td>";

            echo "</tr>";
        }
        tableEnd();
    } else {
        info_msg("Es sind noch keine Freunde in deiner Buddyliste eingetragen!");
    }

    $pendingBuddies = $buddyListRepository->getPendingBuddyRequests($cu->getId());
    if (count($pendingBuddies) > 0) {
        tableStart("Offene Anfragen");
        echo "<tr>
                            <th class=\"tbltitle\">Nick</th>
                            <th class=\"tbltitle\">Punkte</th>
                            <th class=\"tbltitle\">Aktion</th>
                    </tr>";
        foreach ($pendingBuddies as $pending) {
            echo "<tr>
                                    <td class=\"tbldata\">" . $pending->userNick . "</td>";
            echo "<td>" . StringUtils::formatNumber($pending->points) . "</td>";
            echo "<td style=\"width:280px;\">
                                    <a href=\"?page=messages&mode=new&message_user_to=" . $pending->userId . "\" title=\"Nachricht\">Nachricht</a>
                                    <a href=\"?page=userinfo&amp;id=" . $pending->userId . "\" title=\"Info\">Profil</a>
                                    <a href=\"?page=$page&amp;allow=" . $pending->userId . "\" style=\"color:#0f0\">Annehmen</a>
                                    <a href=\"?page=$page&amp;deny=" . $pending->userId . "\" style=\"color:#f90\">Zurückweisen</a>
                            </td>";

            echo "</tr>";
        }
        tableEnd();
    }

    echo "
            <h2>F&uuml;ge einen Freund hinzu</h2>
            <form action=\"?page=$page\" method=\"post\"><b>Nick:</b> <input type=\"text\" name=\"buddy_nick\" id=\"user_nick\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value)\"><br/><div class=\"citybox\" id=\"citybox\">&nbsp;</div><br>
      <input type=\"submit\" name=\"submit_buddy\" value=\"Freund hinzuf&uuml;gen\" />
            </form><br/><br/>";
}
