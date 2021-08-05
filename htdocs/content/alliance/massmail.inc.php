<?PHP

use EtoA\Alliance\AllianceRights;
use EtoA\Message\MessageRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

/** @var MessageRepository $messageRepository */
$messageRepository = $app[MessageRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var mixed[] $arr alliance data */

if (Alliance::checkActionRights(AllianceRights::MASS_MAIL)) {
    echo "<h2>Rundmail</h2>";

    // Nachricht senden
    if (isset($_POST['submit']) && checker_verify()) {
        $allianceUsers = $userRepository->searchUserNicknames(UserSearch::create()->allianceId((int) $arr['alliance_id'])->notUser($cu->getId()));
        if (count($allianceUsers) > 0) {
            foreach (array_keys($allianceUsers) as $allianceUserId) {
                $subject = addslashes($_POST['message_subject']) . "";

                $messageRepository->sendFromUserToUser(
                    $cu->getId(),
                    $allianceUserId,
                    $_POST['message_subject'],
                    $_POST['message_text'],
                    MSG_ALLYMAIL_CAT
                );
            }
            success_msg("Nachricht wurde gesendet!");
            echo "<input type=\"button\" value=\"Neue Nachricht schreiben\" onclick=\"document.location='?page=$page&action=massmail'\" /> &nbsp; ";
            echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
        } else {
            error_msg("Nachricht wurde nicht gesendet, keine Mitglieder vorhanden!");
            echo "<input type=\"button\" value=\"Zur&uuml;ck\" onClick=\"document.location='?page=$page'\" />";
        }
    } else {
        echo "<form action=\"?page=$page&action=massmail\" method=\"POST\">";
        checker_init();
        if (isset($_GET['message_subject'])) {
            $subject = $_GET['message_subject'];
        } else {
            $subject = "";
        }
        tableStart("Nachricht verfassen");
        echo "<tr><th style=\"width:50px;\">Betreff:</th><td><input type=\"text\" name=\"message_subject\" value=\"" . $subject . "\" size=\"30\" maxlength=\"255\"></td></tr>";
        echo "<tr><th>Text:</th><td><textarea name=\"message_text\" rows=\"5\" cols=\"50\"></textarea><br/>" . helpLink('textformat', 'Hilfe zur Formatierung') . "</td></tr>";
        tableEnd();
        echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" /> &nbsp;<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
        echo "</form>";
    }
}
