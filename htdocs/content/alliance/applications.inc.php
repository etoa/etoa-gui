<?PHP

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceMemberCosts;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;
use EtoA\User\UserService;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var AllianceApplicationRepository $allianceApplicationRepository */
$allianceApplicationRepository = $app[AllianceApplicationRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var AllianceMemberCosts $allianceMemberCosts */
$allianceMemberCosts = $app[AllianceMemberCosts::class];

if (Alliance::checkActionRights(AllianceRights::APPLICATIONS)) {
    $maxMemberCount = $config->getInt("alliance_max_member_count");

    echo "<h2>Bewerbungen</h2>";
    if (isset($_POST['applicationsubmit']) && checker_verify()) {
        if (count($_POST['application_answer']) > 0) {
            $cnt = 0;
            $alliance = $allianceRepository->getAlliance((int) $cu->allianceId);
            $currentMemberCount = $allianceRepository->countUsers((int) $cu->allianceId);
            $newMemberCount = $currentMemberCount;
            foreach ($_POST['application_answer'] as $id => $answer) {

                $nick = $_POST['application_user_nick_' . $id . ''];

                // Anfrage annehmen
                if ($answer == 2) {
                    if ($maxMemberCount != 0 && $newMemberCount >= $maxMemberCount) {
                        error_msg("Maximale Anzahl an Mitgliedern erreicht!");
                        break;
                    }

                    $cnt++;
                    $newMemberCount++;
                    success_msg($nick . " wurde angenommen.");

                    // Nachricht an den Bewerber schicken
                    /** @var \EtoA\Message\MessageRepository $messageRepository */
                    $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                    $messageRepository->createSystemMessage($id, MSG_ALLYMAIL_CAT, "Bewerbung angenommen", "Deine Allianzbewerbung wurde angenommen!\n\n[b]Antwort:[/b]\n" . addslashes($_POST['application_answer_text'][$id]));

                    // Log schreiben
                    /** @var AllianceHistoryRepository $allianceHistoryRepository */
                    $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                    $allianceHistoryRepository->addEntry((int) $cu->allianceId, "Die Bewerbung von [b]" . $nick . "[/b] wurde akzeptiert!");
                    Log::add(5, Log::INFO, "Der Spieler [b]" . $nick . "[/b] tritt der Allianz [b]" . $alliance->nameWithTag . "[/b] bei!");

                    /** @var UserService */
                    $userService = $app[UserService::class];
                    $userService->addToUserLog($id, "alliance", "{nick} ist nun ein Mitglied der Allianz " . $alliance->name . ".");

                    // Speichern
                    $userRepository->setAllianceId($id, $cu->allianceId());
                    $allianceApplicationRepository->deleteApplication((int) $id, $cu->allianceId());
                }
                // Anfrage ablehnen
                elseif ($answer == 1) {
                    $cnt++;
                    success_msg($nick . " wurde abgelehnt.");

                    // Nachricht an den Bewerber schicken
                    /** @var \EtoA\Message\MessageRepository $messageRepository */
                    $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                    $messageRepository->createSystemMessage($id, MSG_ALLYMAIL_CAT, "Bewerbung abgelehnt", "Deine Allianzbewerbung wurde abgelehnt!\n\n[b]Antwort:[/b]\n" . addslashes($_POST['application_answer_text'][$id]));

                    // Log schreiben
                    /** @var AllianceHistoryRepository $allianceHistoryRepository */
                    $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                    $allianceHistoryRepository->addEntry((int) $cu->allianceId, "Die Bewerbung von [b]" . $nick . "[/b] wurde abgelehnt!");

                    // Anfrage löschen
                    $allianceApplicationRepository->deleteApplication((int) $id, $cu->allianceId());
                }
                // Anfrage unbearbeitet lassen, jedoch Nachricht verschicken wenn etwas geschrieben ist
                else {
                    $text = str_replace(' ', '', $_POST['application_answer_text'][$id]);
                    if ($text != '') {
                        // Nachricht an den Bewerber schicken
                        /** @var \EtoA\Message\MessageRepository $messageRepository */
                        $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                        $messageRepository->createSystemMessage($id, MSG_ALLYMAIL_CAT, "Bewerbung: Nachricht", "Antwort auf die Bewerbung an die Allianz [b]" . $alliance->nameWithTag . "[/b]:\n" . $_POST['application_answer_text'][$id] . "");

                        $cnt++;
                        success_msg($nick . ": Nachricht gesendet");
                    }
                }
            }

            // Wenn neue Members hinzugefügt worde sind werden ev. die Allianzrohstoffe angepasst
            if ($newMemberCount > $currentMemberCount) {
                $allianceMemberCosts->increase($alliance->id, $currentMemberCount, $newMemberCount);
            }

            success_msg("Änderungen übernommen");
        }
    }

    $currentMemberCount = $allianceRepository->countUsers((int) $cu->allianceId);

    echo "<form action=\"?page=$page&action=applications\" method=\"post\" id=\"applicationsForm\">";
    checker_init();
    $applications = $allianceApplicationRepository->getAllianceApplications($cu->allianceId());
    if (count($applications) > 0) {
        tableStart("Bewerbungen prüfen");
        echo "<tr>
                        <th width=\"10%\">User</td>
                        <th width=\"35%\">Datum / Text</td>
                        <th width=\"35%\">Nachricht</td>
                        <th width=\"20%\">Aktion</td>
                    </tr>";
        foreach ($applications as $application) {
            echo "<tr>
            <td " . tm("Info", "Rang: " . $application->userRank . "<br>Punkte: " . nf($application->userPoints) . "<br>Registriert: " . date("d.m.Y H:i", $application->userRegistered) . "") . ">
                <a href=\"?page=userinfo&id=" . $application->userId . "\">" . $application->userNick . "</a>";

            // Übergibt Usernick dem Formular, damit beim Submit nicht nochmals eine DB Abfrage gestartet werden muss
            echo "<input type=\"hidden\" name=\"application_user_nick_" . $application->userId . "\" value=\"" . $application->userNick . "\" />
            </td>
            <td>" . df($application->timestamp) . "<br/><br/>" . text2html($application->text) . "</td>
            <td>
                <textarea rows=\"6\" cols=\"40\" name=\"application_answer_text[" . $application->userId . "]\" /></textarea><br/>" . helpLink('textformat', 'Hilfe zur Formatierung') . "
            </td>
            <td>";
            if ($maxMemberCount == 0 || $currentMemberCount < $maxMemberCount) {
                echo "<input type=\"radio\" name=\"application_answer[" . $application->userId . "]\" value=\"2\" onchange=\"xajax_showAllianceMemberAddCosts('" . $cu->allianceId() . "',xajax.getFormValues('applicationsForm'));\"/> <span " . tm("Anfrage annehmen", "" . $application->userNick . " wird in die Allianz aufgenommen.<br>Eine Nachricht wird versendet.") . ">Annehmen</span><br><br>";
            }
            echo "<input type=\"radio\" name=\"application_answer[" . $application->userId . "]\" value=\"1\" onchange=\"xajax_showAllianceMemberAddCosts('" . $cu->allianceId() . "',xajax.getFormValues('applicationsForm'));\"/> <span " . tm("Anfrage ablehnen", "" . $application->userNick . " wird der Zutritt zu der Allianz verweigert.<br>Eine Nachricht wird versendet.") . ">Ablehnen</span><br><br>";
            echo "<input type=\"radio\" name=\"application_answer[" . $application->userId . "]\" value=\"0\" checked=\"checked\" onchange=\"xajax_showAllianceMemberAddCosts('" . $cu->allianceId() . "',xajax.getFormValues('applicationsForm'));\"/> <span " . tm("Anfrage nicht bearbeiten", "Sofern vorhanden, wird eine Nachricht an " . $application->userNick . " geschickt.") . ">Nicht bearbeiten</span>";
            echo "</td>
            </tr>";
        }
        echo "<tr id=\"memberCosts\" style=\"display: none;\"><td colspan=\"4\" id=\"memberCostsTD\"></td></tr>";
        tableEnd();
        echo "<input type=\"submit\" name=\"applicationsubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
    } else {
        error_msg("Keine Bewerbungen vorhanden!");
    }
    echo "<input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" /></form>";

    if ($maxMemberCount != 0) {
        echo "<p><b>Hinweis:</b> Eine Allianz darf maximal $maxMemberCount Mitglieder haben (aktuell $currentMemberCount)!</p>";
    }
}
