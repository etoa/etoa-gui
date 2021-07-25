<?PHP

/** @var mixed[] $arr alliance data */
use EtoA\Alliance\AlliancePollRepository;

/** @var AlliancePollRepository $alliancePollRepository */
$alliancePollRepository = $app[AlliancePollRepository::class];

if (Alliance::checkActionRights('polls')) {
    echo "<h2>Umfragen verwalten</h2>";
    if (isset($_GET['pollaction']) && $_GET['pollaction'] == "create") {
        if (isset($_POST['pollsubmitnew']) && $_POST['pollsubmitnew'] && checker_verify()) {
            $_SESSION['alliance_poll']['poll_title'] = $_POST['poll_title'];
            $_SESSION['alliance_poll']['poll_question'] = $_POST['poll_question'];
            $_SESSION['alliance_poll']['poll_a1_text'] = $_POST['poll_a1_text'];
            $_SESSION['alliance_poll']['poll_a2_text'] = $_POST['poll_a2_text'];
            $_SESSION['alliance_poll']['poll_a3_text'] = $_POST['poll_a3_text'];
            $_SESSION['alliance_poll']['poll_a4_text'] = $_POST['poll_a4_text'];
            $_SESSION['alliance_poll']['poll_a5_text'] = $_POST['poll_a5_text'];
            $_SESSION['alliance_poll']['poll_a6_text'] = $_POST['poll_a6_text'];
            $_SESSION['alliance_poll']['poll_a7_text'] = $_POST['poll_a7_text'];
            $_SESSION['alliance_poll']['poll_a8_text'] = $_POST['poll_a8_text'];
            if ($_POST['poll_title'] != "") {
                if ($_POST['poll_question'] != "") {
                    if ($_POST['poll_a1_text'] != "" && $_POST['poll_a2_text'] != "") {
                        $alliancePollRepository->add($cu->allianceId(), $_POST['poll_title'], $_POST['poll_question'], $_POST['poll_a1_text'], $_POST['poll_a2_text'], $_POST['poll_a3_text'], $_POST['poll_a4_text'], $_POST['poll_a5_text'], $_POST['poll_a6_text'], $_POST['poll_a7_text'], $_POST['poll_a8_text']);
                        success_msg("Umfrage wurde gespeichert!");
                        $_SESSION['alliance_poll'] = null;
                        $created = true;
                    } else
                        error_msg("Mindestens die ersten zwei Antworten müssen definiert sein!");
                } else
                    error_msg("Frage fehlt!");
            } else
                error_msg("Titel fehlt!");
        }
        if (isset($created) && $created)
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=" . $_GET['action'] . "';\" value=\"Ok\" />";
        else {
            echo "<form action=\"?page=$page&amp;action=polls&amp;pollaction=create\" method=\"post\">";
            checker_init();
            tableStart("Neue Umfrage erstellen");
            echo "<tr><th colspan=\"2\">Es müssen mindestens <b>zwei</b> Antwortfelder ausgefüllt sein!</th>";
            echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"poll_title\" size=\"80\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_title'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Frage:</th><td><input type=\"text\" name=\"poll_question\" size=\"80\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_question'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Antwort 1:</th><td><input type=\"text\" name=\"poll_a1_text\" size=\"70\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_a1_text'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Antwort 2:</th><td><input type=\"text\" name=\"poll_a2_text\" size=\"70\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_a2_text'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Antwort 3:</th><td><input type=\"text\" name=\"poll_a3_text\" size=\"70\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_a3_text'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Antwort 4:</th><td><input type=\"text\" name=\"poll_a4_text\" size=\"70\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_a4_text'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Antwort 5:</th><td><input type=\"text\" name=\"poll_a5_text\" size=\"70\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_a5_text'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Antwort 6:</th><td><input type=\"text\" name=\"poll_a6_text\" size=\"70\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_a6_text'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Antwort 7:</th><td><input type=\"text\" name=\"poll_a7_text\" size=\"70\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_a7_text'] ?? '') . "\" /></td></tr>";
            echo "<tr><th>Antwort 8:</th><td><input type=\"text\" name=\"poll_a8_text\" size=\"70\" maxlength=\"150\" value=\"" . ($_SESSION['alliance_poll']['poll_a8_text'] ?? ''). "\" /></td></tr>";
            tableEnd();
            echo "<input type=\"submit\" name=\"pollsubmitnew\" value=\"Speichern\" /> &nbsp; ";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=" . $_GET['action'] . "';\" value=\"Zur&uuml;ck\" /></form>";
        }
    }
    //
    // Umfrage bearbeiten
    //
    elseif (isset($_GET['edit']) && intval($_GET['edit']) > 0) {
        $eid = intval($_GET['edit']);

        $poll = $alliancePollRepository->getPoll($eid, $cu->allianceId());
        if ($poll !== null) {
            $_SESSION['alliance_poll']['poll_title'] = $poll->title;
            $_SESSION['alliance_poll']['poll_question'] = $poll->question;
            $_SESSION['alliance_poll']['poll_a1_text'] = $poll->answer1;
            $_SESSION['alliance_poll']['poll_a2_text'] = $poll->answer2;
            $_SESSION['alliance_poll']['poll_a3_text'] = $poll->answer3;
            $_SESSION['alliance_poll']['poll_a4_text'] = $poll->answer4;
            $_SESSION['alliance_poll']['poll_a5_text'] = $poll->answer5;
            $_SESSION['alliance_poll']['poll_a6_text'] = $poll->answer6;
            $_SESSION['alliance_poll']['poll_a7_text'] = $poll->answer7;
            $_SESSION['alliance_poll']['poll_a8_text'] = $poll->answer8;

            $updated = false;
            if (isset($_POST['pollsubmit']) && $_POST['pollsubmit'] && checker_verify()) {
                $_SESSION['alliance_poll']['poll_title'] = $_POST['poll_title'];
                $_SESSION['alliance_poll']['poll_question'] = $_POST['poll_question'];
                $_SESSION['alliance_poll']['poll_a1_text'] = $_POST['poll_a1_text'];
                $_SESSION['alliance_poll']['poll_a2_text'] = $_POST['poll_a2_text'];
                $_SESSION['alliance_poll']['poll_a3_text'] = $_POST['poll_a3_text'];
                $_SESSION['alliance_poll']['poll_a4_text'] = $_POST['poll_a4_text'];
                $_SESSION['alliance_poll']['poll_a5_text'] = $_POST['poll_a5_text'];
                $_SESSION['alliance_poll']['poll_a6_text'] = $_POST['poll_a6_text'];
                $_SESSION['alliance_poll']['poll_a7_text'] = $_POST['poll_a7_text'];
                $_SESSION['alliance_poll']['poll_a8_text'] = $_POST['poll_a8_text'];
                if ($_POST['poll_title'] != "") {
                    if ($_POST['poll_question'] != "") {
                        if ($_POST['poll_a1_text'] != "" && $_POST['poll_a2_text'] != "") {
                            $alliancePollRepository->updatePoll($eid, $cu->allianceId(), $_POST['poll_title'], $_POST['poll_question'], $_POST['poll_a1_text'], $_POST['poll_a2_text'], $_POST['poll_a3_text'], $_POST['poll_a4_text'], $_POST['poll_a5_text'], $_POST['poll_a6_text'], $_POST['poll_a7_text'], $_POST['poll_a8_text']);
                            echo "Umfrage wurde gespeichert!";
                            $_SESSION['alliance_poll'] = null;
                            $updated = true;
                        } else
                            error_msg("Mindestens die ersten zwei Antworten müssen definiert sein!");
                    } else
                        error_msg("Frage fehlt!");
                } else
                    error_msg("Titel fehlt!");
            }
            if ($updated)
                echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=" . $_GET['action'] . "';\" value=\"Ok\" />";
            else {
                echo "<form action=\"?page=$page&amp;action=polls&amp;edit=" . $poll->id . "\" method=\"post\">";
                checker_init();
                tableStart("Umfrage bearbeiten");
                echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"poll_title\" size=\"80\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_title'] . "\" /></td></tr>";
                echo "<tr><th>Frage:</th><td><input type=\"text\" name=\"poll_question\" size=\"80\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_question'] . "\" /></td></tr>";
                echo "<tr><th>Antwort 1:</th><td><input type=\"text\" name=\"poll_a1_text\" size=\"70\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_a1_text'] . "\" /> " . $poll->answer1Count . " Stimmen</td></tr>";
                echo "<tr><th>Antwort 2:</th><td><input type=\"text\" name=\"poll_a2_text\" size=\"70\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_a2_text'] . "\" /> " . $poll->answer2Count . " Stimmen</td></tr>";
                echo "<tr><th>Antwort 3:</th><td><input type=\"text\" name=\"poll_a3_text\" size=\"70\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_a3_text'] . "\" /> " . $poll->answer3Count . " Stimmen</td></tr>";
                echo "<tr><th>Antwort 4:</th><td><input type=\"text\" name=\"poll_a4_text\" size=\"70\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_a4_text'] . "\" /> " . $poll->answer4Count . " Stimmen</td></tr>";
                echo "<tr><th>Antwort 5:</th><td><input type=\"text\" name=\"poll_a5_text\" size=\"70\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_a5_text'] . "\" /> " . $poll->answer5Count . " Stimmen</td></tr>";
                echo "<tr><th>Antwort 6:</th><td><input type=\"text\" name=\"poll_a6_text\" size=\"70\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_a6_text'] . "\" /> " . $poll->answer6Count . " Stimmen</td></tr>";
                echo "<tr><th>Antwort 7:</th><td><input type=\"text\" name=\"poll_a7_text\" size=\"70\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_a7_text'] . "\" /> " . $poll->answer7Count . " Stimmen</td></tr>";
                echo "<tr><th>Antwort 8:</th><td><input type=\"text\" name=\"poll_a8_text\" size=\"70\" maxlength=\"150\" value=\"" . $_SESSION['alliance_poll']['poll_a8_text'] . "\" /> " . $poll->answer8Count . " Stimmen</td></tr>";
                tableEnd();
                echo "<input type=\"submit\" name=\"pollsubmit\" value=\"Speichern\" /> &nbsp; ";
                echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=" . $_GET['action'] . "';\" value=\"Zur&uuml;ck\" /></form>";
            }
        }
    }
    //
    // Umfragen anzeigen
    //
    else {
        if (isset($_GET['del']) && intval($_GET['del']) > 0) {
            $did = intval($_GET['del']);

            $deleted = $alliancePollRepository->deletePoll($did, $cu->allianceId());
            if ($deleted) {
                success_msg("Umfrage wurde gel&ouml;scht!");
            }
        }
        if (isset($_GET['deactivate']) && intval($_GET['deactivate']) > 0) {
            $alliancePollRepository->updateActive((int) $_GET['deactivate'], $cu->allianceId(), false);
        }

        if (isset($_GET['activate']) && intval($_GET['activate']) > 0) {
            $alliancePollRepository->updateActive((int) $_GET['activate'], $cu->allianceId(), true);
        }

        $_SESSION['alliance_poll'] = null;
        $polls = $alliancePollRepository->getPolls($cu->allianceId());
        if (count($polls) > 0) {
            tableStart();
            echo "<tr><th>Titel</th><th>Frage</th><th>Erstellt</th><th style=\"width:200px;\">Aktionen</th></tr>";
            foreach ($polls as $poll) {
                echo "<tr><td>" . stripslashes($poll->title) . "</td>";
                echo "<td>" . stripslashes($poll->question) . "</td>";
                echo "<td>" . df($poll->timestamp) . "</td>";
                echo "<td><a href=\"?page=$page&amp;action=" . $_GET['action'] . "&amp;edit=" . $poll->id . "\">Bearbeiten</a> ";
                if ($poll->active)
                    echo "<a href=\"?page=$page&amp;action=" . $_GET['action'] . "&amp;deactivate=" . $poll->id . "\">Deaktivieren</a> ";
                else
                    echo "<a href=\"?page=$page&amp;action=" . $_GET['action'] . "&amp;activate=" . $poll->id . "\">Aktivieren</a> ";
                echo "<a href=\"?page=$page&amp;action=" . $_GET['action'] . "&amp;del=" . $poll->id . "\" onclick=\"return confirm('Umfrage wirklich löschen?');\">L&ouml;schen</a></td>";
            }
            tableEnd();
        } else
            error_msg("Keine Umfragen vorhanden!");
        echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=" . $_GET['action'] . "&pollaction=create'\" value=\"Neue Umfrage erstellen\" /> &nbsp;
            <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
    }
}
