<?PHP

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageRepository;
use EtoA\Support\StringUtils;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var AllianceApplicationRepository $allianceApplicationRepository */
$allianceApplicationRepository = $app[AllianceApplicationRepository::class];

/** @var AllianceHistoryRepository $allianceHistoryRepository */
$allianceHistoryRepository = $app[AllianceHistoryRepository::class];

/** @var MessageRepository $messageRepository */
$messageRepository = $app[MessageRepository::class];

/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];

/** @var AllianceService $service */
$service = $app[AllianceService::class];

if ($config->getBoolean("alliance_allow")) {
    if ($cu->allianceId == 0) {
        $application = $allianceApplicationRepository->getUserApplication($cu->getId());

        //
        // Infotext bei aktiver Bewerbung
        //
        if ($application !== null) {
            // Bewerbung zurückziehen
            if (isset($_GET['action']) && $_GET['action'] == "cancelapplication") {
                $alliance = $allianceRepository->getAlliance($application->allianceId);
                $messageRepository->createSystemMessage($alliance->founderId, MessageCategoryId::ALLIANCE, "Bewerbung zurückgezogen", "Der Spieler " . $cu->nick . " hat die Bewerbung bei deiner Allianz zurückgezogen!");
                $allianceHistoryRepository->addEntry($application->allianceId, "Der Spieler [b]" . $cu->nick . "[/b] zieht seine Bewerbung zurück.");
                $allianceApplicationRepository->deleteApplication($cu->getId(), $application->allianceId);
                echo "Deine Bewerbung wurde gel&ouml;scht!<br/><br/>
        <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"OK\" />";
            }
            // Bewerbungsstatus anzeigen
            else {
                echo "<h2>Bewerbungsstatus</h2>";
                $alliance = $allianceRepository->getAlliance($application->allianceId);
                if ($alliance !== null) {
                    success_msg("Du hast dich am " . StringUtils::formatDate($application->timestamp) . " bei der Allianz " . $alliance->nameWithTag . " beworben
                 und musst nun darauf warten, dass deine Bewerbung akzeptiert wird!");
                    echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=cancelapplication';\" value=\"Bewerbung zurückziehen\" />";
                } else {
                    error_msg("Du hast dich am " . StringUtils::formatDate($application->timestamp) . " bei einer Allianz beworben, diese Allianz existiert aber leider nicht mehr.
                 Deine Bewerbung wurde deshalb gelöscht!");
                    echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Bei einer anderen Allianz bewerben\" />";
                }
            }
        }

        //
        // Allianzgründung
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "create") {
            echo "<h2>Gr&uuml;ndung einer Allianz</h2>";

            $defTag = "";
            $defName = "";
            $finish = false;
            // Allianzgründung speichern
            if (isset($_POST['createsubmit']) && $_POST['createsubmit'] != "" && checker_verify()) {
                try {
                    $alliance = $service->create(
                        $_POST['alliance_tag'],
                        $_POST['alliance_name'],
                        $cu->id
                    );
                    success_msg("Allianz [b]" . $alliance->toString() . "[/b] gegründet!");
                    echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Weiter\" />";
                    $finish = true;

                    $app['dispatcher']->dispatch(new \EtoA\Alliance\Event\AllianceCreate(), \EtoA\Alliance\Event\AllianceCreate::CREATE_SUCCESS);
                } catch (InvalidAllianceParametersException $ex) {
                    $defTag = $_POST['alliance_tag'];
                    $defName = $_POST['alliance_name'];
                    error_msg($ex->getMessage());
                }
            }
            if (!$finish) {
                echo "<form action=\"?page=$page&amp;action=create\" method=\"post\">";
                checker_init();
                tableStart("Allianz-Daten");
                echo "<tr><th>Tag / Name:</th>
                <td>
                [<input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"" . StringUtils::encodeDBStringToPlaintext($defTag) . "\" />]
                &nbsp; <input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"" . StringUtils::encodeDBStringToPlaintext($defName) . "\" /></td></tr>
                <tr><td colspan=\"2\">Alle weiteren Daten könnten nach der Erstellung im Allianzmenü geändert werden.</td></tr>";
                tableEnd();
                echo "<input type=\"submit\" name=\"createsubmit\" value=\"Speichern\" /> &nbsp;
                <input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Übersicht\" /></form>";
            }
        }

        //
        // Bewerbung bei einer Allianz
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "join") {
            // Bewerbungstext schreiben
            if (isset($_GET['alliance_id']) && intval($_GET['alliance_id']) > 0) {
                $alliance = $allianceRepository->getAlliance((int) $_GET['alliance_id']);
                if ($alliance !== null) {
                    echo "<h2>Bewerbung bei der Allianz " . $alliance->nameWithTag . "</h2>";
                    if ($alliance->acceptApplications) {
                        echo "<form action=\"?page=$page&amp;action=join\" method=\"post\">";
                        checker_init();
                        tableStart("Bewerbungstext");
                        echo "<tr><th>Nachricht:</th><td><textarea rows=\"15\" cols=\"80\" name=\"user_alliance_application\">" . $alliance->acceptApplications . "</textarea><br/>" . helpLink('textformat', 'Hilfe zur Formatierung') . "</td>";
                        tableEnd();
                        echo "<input type=\"hidden\" name=\"user_alliance_id\" value=\"" . $alliance->id . "\" />";
                        echo "<input type=\"submit\" name=\"submitapplication\" value=\"Senden\" />&nbsp;<input type=\"button\" onclick=\"document.location='?page=alliance&action=join'\" value=\"Zur&uuml;ck\" /></form>";
                    } else {
                        error_msg("Die Allianz nimmt keine Bewerbungen an!");
                    }
                } else {
                    error_msg("Allianzdatensatz nicht gefunden!");
                }
            }
            // Bewerbungstext senden
            elseif (isset($_POST['submitapplication']) && checker_verify()) {
                echo "<h2>Bewerbung abschicken</h2>";

                $aid = (int) $_POST['user_alliance_id'];
                if ($_POST['user_alliance_application'] != '') {
                    $alliance = $allianceRepository->getAlliance($aid);
                    $messageRepository->createSystemMessage($alliance->founderId, MessageCategoryId::ALLIANCE, "Bewerbung", "Der Spieler " . $cu->nick . " hat sich bei deiner Allianz beworben. Gehe auf die [page=alliance&action=applications]Allianzseite[/page] für Details!");
                    $allianceHistoryRepository->addEntry($aid, "Der Spieler [b]" . $cu->nick . "[/b] bewirbt sich sich bei der Allianz.");
                    $allianceApplicationRepository->addApplication($cu->getId(), $aid, $_POST['user_alliance_application']);

                    success_msg("Deine Bewerbung bei der Allianz " . $alliance->nameWithTag . " wurde gespeichert! Die Allianzleitung wurde informiert und wird deine Bewerbung ansehen.");
                    echo "<input value=\"&Uuml;bersicht\" type=\"button\" onclick=\"document.location='?page=$page'\" />";
                } else {
                    error_msg("Du musst einen Bewerbungstext eingeben!");
                    echo "<input value=\"Zur&uuml;ck\" type=\"button\" onclick=\"document.location='?page=$page&action=join&alliance_id=" . $aid . "'\" />";
                }
            }
            // Allianzauswahl anzeigen
            else {
                echo "<h2>Allianz w&auml;hlen</h2>
                Nicht alle Allianzen akzeptieren jederzeit eine Bewerbung. <br/>
                Im Folgenden findest du eine Liste der Allianzen die momentan Bewerbungen akzeptieren:<br/><br/>";

                $alliances = $allianceRepository->getAlliancesAcceptingApplications();
                if (count($alliances) > 0) {
                    tableStart("", "400", " align=\"center\"");
                    //					echo "<table width=\"300\" align=\"center\" class=\"tbl\">";
                    echo "<tr>
                                    <th>Tag</th>
                                    <th>Name</th>
                                    <th>Mitglieder</th>
                                    <th style=\"width:100px;\">Aktionen</th>
                            </tr>";
                    foreach ($alliances as $alliance) {
                        echo "<tr><td>" . $alliance->tag . "</td>
                        <td>" . $alliance->name . "</td>
                        <td>" . $alliance->memberCount . "</td>
                        <td><a href=\"?page=alliance&amp;info_id=" . $alliance->id . "\">Info</a>";
                        echo "&nbsp;<a href=\"?page=$page&action=join&alliance_id=" . $alliance->id . "\">Bewerben</a>";
                        echo "</td></tr>";
                    }
                    tableEnd();

                    $maxMemberCount = $config->getInt("alliance_max_member_count");
                    if ($maxMemberCount !== 0) {
                        echo "<p><b>Hinweis:</b> Eine Allianz darf maximal $maxMemberCount Mitglieder haben!</p>";
                    }

                    echo "<a href=\"?page=$page&amp;action=create\">Gründe</a> eine eigene Allianz.</a>";
                } else {
                    error_msg("Es gibt im Moment keine Allianzen denen man beitreten k&ouml;nnte!");
                    echo "<a href=\"?page=$page&amp;action=create\">Gründe</a> eine eigene Allianz.</a>";
                }
            }
        }

        //
        // Infotext wenn in keiner Allianz
        //
        else {
            echo "Es kann von Vorteil sein, wenn man sich nicht alleine gegen den Rest des Universums durchsetzen muss. Dazu gibt es das Allianz-System,
                mit dem du dich mit anderen Spielern als Team zusammentun kannst. Viele Allianzen pflegen eine regelm&auml;ssige Kommunikation, bieten Schutz vor
                Angriffen oder r&auml;chen dich wenn du angegriffen worden bist. Trete einer Allianz bei oder gr&uuml;nde selber eine Allianz.<br/><br/>";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Einer Allianz beitreten\" />&nbsp;&nbsp;&nbsp;";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=create';\" value=\"Eine Allianz gr&uuml;nden\" />";
        }
    }
} else
    echo "Allianzen sind zur Zeit deaktiviert.";
