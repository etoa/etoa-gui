<script type="text/javascript">
    function checkWarDeclaration() {
        f = document.forms['wardeclaration'];
        if (f.alliance_bnd_text.value == "") {
            alert("Du musst eine Nachricht schreiben!");
            f.alliance_bnd_text.focus();
            return false;
        }
        if (f.alliance_bnd_text_pub.value == "") {
            alert("Du musst eine öffentliche Kriegserklärung hinzufügen!");
            f.alliance_bnd_text_pub.focus();
            return false;
        }
        return true;
    }

    function checkPactOffer() {
        f = document.forms['pactoffer'];
        if (f.alliance_bnd_name.value == "") {
            alert("Du musst dem Bündnis einen Namen geben!");
            f.alliance_bnd_name.focus();
            return false;
        }
        if (f.alliance_bnd_text.value == "") {
            alert("Du musst eine Nachricht schreiben!");
            f.alliance_bnd_text.focus();
            return false;
        }

        return true;
    }

    function checkEndPact() {
        f = document.forms['endpact'];
        if (f.pact_end_text.value == "") {
            alert("Du musst eine Nachricht schreiben!");
            f.pact_end_text.focus();
            return false;
        }
        return true;
    }
</script>

<?PHP

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use EtoA\Message\MessageRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;

if (Alliance::checkActionRights(AllianceRights::RELATIONS)) {
    echo "<h2>Diplomatie</h2>";

    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];
    $allianceNamesWithTags = $allianceRepository->getAllianceNamesWithTags();
    /** @var MessageRepository $messageRepository */
    $messageRepository = $app[MessageRepository::class];
    /** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
    $allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];
    /** @var AllianceHistoryRepository $allianceHistoryRepository */
    $allianceHistoryRepository = $app[AllianceHistoryRepository::class];

    //
    // Kriegserklärung schreiben
    //
    if (isset($_GET['begin_war']) && intval($_GET['begin_war']) > 0) {
        $aid = intval($_GET['begin_war']);

        $check = false;
        if (!isset($_GET['begin_bnd']) || $_GET['begin_bnd'] != $cu->allianceId) {
            $check = true;
        }

        $otherAlliance = $allianceRepository->getAlliance($aid);
        if ($otherAlliance !== null && $check) {
            echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\" name=\"wardeclaration\">";
            checker_init();

            tableStart("Kriegserkl&auml;rung an die Allianz " . $otherAlliance->nameWithTag);
            echo "<tr><th>Nachricht:</th><td><textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text\"></textarea></td></tr>";
            echo "<tr><th>Öffentlicher Text:</th><td><textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text_pub\"></textarea></td></tr>";
            tableEnd();

            echo "<input type=\"hidden\" name=\"alliance_bnd_alliance_id\" value=\"" . $otherAlliance->id . "\" />";
            echo "<input type=\"submit\" name=\"sbmit_new_war\" value=\"Senden\" onclick=\"return checkWarDeclaration()\" onsubmit=\"return checkWarDeclaration()\" />&nbsp;
                    <input type=\"button\" onclick=\"document.location='?page=alliance&action=relations'\" value=\"Zur&uuml;ck\" />";
            echo "</form>";
        } else {
            error_msg("Diese Allianz existiert nicht!");
        }
    }

    //
    // Bündnisanfrage schreiben
    //
    elseif (isset($_GET['begin_bnd']) && intval($_GET['begin_bnd']) > 0) {
        $aid = intval($_GET['begin_bnd']);

        $otherAlliance = $allianceRepository->getAlliance($aid);
        if ($otherAlliance !== null && $otherAlliance->id != $cu->allianceId) {

            if ($otherAlliance->acceptBnd) {
                echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\" name=\"pactoffer\">";
                checker_init();

                tableStart("B&uuml;ndnisanfrage an die Allianz " . $otherAlliance->nameWithTag);
                echo "<tr>
                            <th>Name des Bündnisses:</th>
                            <td>
                                <input type=\"text\" size=\"30\" maxlength=\"30\" name=\"alliance_bnd_name\" />
                            </td>
                        </tr>";
                echo "<tr>
                            <th>Bündnisanfrage:</th>
                            <td>
                                <textarea rows=\"10\" cols=\"50\" name=\"alliance_bnd_text\"></textarea>
                            </td>
                        </tr>";
                tableEnd();

                echo "<input type=\"hidden\" name=\"alliance_bnd_alliance_id\" value=\"" . $otherAlliance->id . "\" />";
                echo "<input type=\"submit\" name=\"sbmit_new_bnd\" value=\"Senden\" onclick=\"return checkPactOffer()\" onsubmit=\"return checkPactOffer()\" />&nbsp;
                        <input type=\"button\" onclick=\"document.location='?page=alliance&action=relations'\" value=\"Zur&uuml;ck\" />";
                echo "</form>";
            } else {
                error_msg("Die Allianz nimmt keine Bündnisanfragen an!", 1);
            }
        } else {
            error_msg("Diese Allianz existiert nicht!");
        }
    }

    //
    // Büdniss/Kriegs- Text ansehen
    //
    elseif (isset($_GET['view']) && intval($_GET['view']) > 0) {
        $id = intval($_GET['view']);

        $diplomacy = $allianceDiplomacyRepository->getDiplomacy($id, $cu->allianceId);
        if ($diplomacy !== null) {
            echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\">";

            switch ($diplomacy->level) {
                case AllianceDiplomacyLevel::BND_REQUEST:
                    tableStart("Status der Bündnissanfrage");
                    echo "<tr>
                                <th style=\"width:200px;\">Allianz</th>
                                <td>" . $diplomacy->otherAllianceName . "</td>
                            </tr>";
                    echo "<tr>
                                <th style=\"width:200px;\">Bündnissname</th>
                                <td>" . BBCodeUtils::toHTML($diplomacy->name) . "</td>
                            </tr>";
                    echo "<tr>
                                <th style=\"width:200px;\">Text</th>
                                <td>" . BBCodeUtils::toHTML($diplomacy->text) . "</td>
                            </tr>";
                    if ($diplomacy->alliance1Id == $cu->allianceId) {
                        echo "<tr>
                                    <th style=\"width:200px;\">Status</th>
                                    <td>Die Anfrage wurde noch nicht angenommen.</td>
                                </tr>";
                    } else {
                        echo "<tr>
                                    <th style=\"width:200px;\">Antwort</th>
                                    <td><textarea name=\"pact_answer\" rows=\"6\" cols=\"70\"></textarea></td>
                                </tr>";
                    }
                    tableEnd();
                    echo "<input type=\"hidden\" name=\"id\" value=\"" . $diplomacy->id . "\" />";
                    if ($diplomacy->alliance1Id == $cu->allianceId) {
                        echo "<input type=\"submit\" name=\"submit_withdraw_pact\" value=\"Bündnisangebot zurückziehen\" onclick=\"return confirm('Angebot wirklich zurückziehen?')\" /> &nbsp; ";
                    } else {
                        echo "<input type=\"submit\" name=\"pact_accept\" value=\"Bündnisangebot annehmen\" /> &nbsp; ";
                        echo "<input type=\"submit\" name=\"pact_reject\" value=\"Bündnisangebot ablehnen\" /> &nbsp; ";
                    }
                    break;
                case AllianceDiplomacyLevel::BND_CONFIRMED:
                    tableStart("Bündnis \"" . $diplomacy->name . "\"");
                    echo "<tr>
                                <th style=\"width:200px;\">Allianz</th>
                                <td>" . $diplomacy->otherAllianceName . "</td>
                            </tr>";
                    echo "<tr>
                                <th style=\"width:200px;\">Anfragetext</th>
                                <td>" . BBCodeUtils::toHTML($diplomacy->text) . "</td>
                            </tr>";
                    echo "<tr>
                                <th style=\"width:200px;\">Öffentlicher Text</th>
                                <td><textarea name=\"alliance_bnd_text_pub\" rows=\"6\" cols=\"70\">" . StringUtils::encodeDBStringForTextarea($diplomacy->publicText) . "</textarea></td>
                            </tr>";
                    tableEnd();
                    echo "<input type=\"hidden\" name=\"id\" value=\"" . $diplomacy->id . "\" />";
                    echo "<input type=\"submit\" name=\"submit_pact_public_text\" value=\"Speichern\" /> &nbsp; ";
                    break;
                case AllianceDiplomacyLevel::WAR:
                    tableStart("Krieg");
                    echo "<tr>
                                <th style=\"width:200px;\">Allianz</th>
                                <td>" . $diplomacy->otherAllianceName . "</td>
                            </tr>";
                    echo "<tr>
                                <th style=\"width:200px;\">Kriegserklärung</th>
                                <td>" . BBCodeUtils::toHTML($diplomacy->text) . "</td>
                            </tr>";
                    if ($diplomacy->alliance1Id == $cu->allianceId) {
                        echo "<tr>
                                    <th style=\"width:200px;\">Öffentlicher Text</th>
                                    <td><textarea name=\"alliance_bnd_text_pub\" rows=\"6\" cols=\"70\">" . StringUtils::encodeDBStringForTextarea($diplomacy->publicText) . "</textarea></td>
                                </tr>";
                    } else {
                        echo "<tr>
                                    <th style=\"width:200px;\">Öffentlicher Text</th>
                                    <td>" . BBCodeUtils::toHTML($diplomacy->publicText) . "</td>
                                </tr>";
                    }
                    tableEnd();
                    if ($diplomacy->alliance1Id == $cu->allianceId) {
                        echo "<input type=\"hidden\" name=\"id\" value=\"" . $diplomacy->id . "\" />";
                        echo "<input type=\"submit\" name=\"submit_war_public_text\" value=\"Speichern\" /> &nbsp; ";
                    }
                    break;
                default:
                    echo "Test";
            }
            echo "<input type=\"button\" onclick=\"document.location='?page=alliance&amp;action=relations';\" value=\"Zur&uuml;ck\" />";
            echo "</form>";
        } else {
            error_msg("Datensatz nicht vorhanden!");
        }
    }

    //
    // End pact
    //
    elseif (isset($_GET['end_pact']) && intval($_GET['end_pact']) > 0) {
        $id = intval($_GET['end_pact']);

        $diplomacy = $allianceDiplomacyRepository->getDiplomacy($id, $cu->allianceId(), AllianceDiplomacyLevel::BND_CONFIRMED);
        if ($diplomacy !== null) {
            echo "<form action=\"?page=$page&amp;action=relations\" method=\"post\" name=\"endpact\">";

            tableStart("Bündnis \"" . stripslashes($diplomacy->name) . "\" beenden");
            echo "<tr>
                        <th style=\"width:200px;\">Allianz</th>
                        <td>" . $diplomacy->otherAllianceName . "</td>
                    </tr>";
            echo "<tr>
                        <th style=\"width:200px;\">Begründung</th>
                        <td><textarea name=\"pact_end_text\" rows=\"6\" cols=\"70\"></textarea></td>
                    </tr>";
            tableEnd();
            echo "<input type=\"hidden\" name=\"id\" value=\"" . $diplomacy->id . "\" />";
            echo "<input type=\"submit\" name=\"submit_pact_end\" value=\"Auflösen\"  onclick=\"return checkEndPact()\" onsubmit=\"return checkEndPact()\" /> &nbsp; ";
            echo "<input type=\"button\" onclick=\"document.location='?page=alliance&amp;action=relations';\" value=\"Zur&uuml;ck\" />";
            echo "</form>";
        }
    }

    //
    // Beziehungsübersicht anzeigen
    //
    else {
        // Save pact offer
        if (isset($_POST['sbmit_new_bnd']) && isset($_POST['alliance_bnd_alliance_id']) && checker_verify()) {
            $id = intval($_POST['alliance_bnd_alliance_id']);

            if ($allianceDiplomacyRepository->existsDiplomacyBetween($cu->allianceId(), $id)) {
                error_msg("Deine Allianz steht schon in einer Beziehung (B&uuml;ndnis/Krieg) mit der ausgew&auml;hlten Allianz oder es ist bereits eine Bewerbung um ein B&uuml;ndnis vorhanden!");
            } else {
                $allianceDiplomacyRepository->add($cu->allianceId, $id, AllianceDiplomacyLevel::BND_REQUEST, $_POST['alliance_bnd_text'], $_POST['alliance_bnd_name'], $cu->getId());
                success_msg("Du hast einer Allianz erfolgreich ein B&uuml;ndnis angeboten!");

                //Nachricht an den Leader der gegnerischen Allianz schreiben
                $founderId = $allianceRepository->getFounderId($id);
                $messageRepository->createSystemMessage($founderId, MSG_ALLYMAIL_CAT, 'Bündnisanfrage', "Die Allianz [b]" . $allianceNamesWithTags[$cu->allianceId] . "[/b] fragt euch für ein Bündnis an.\n
                        [b]Text:[/b] " . addslashes($_POST['alliance_bnd_text']) . "\n
                        Geschrieben von [b]" . $cu->nick . "[/b].\n Gehe auf die [page=alliance]Allianzseite[/page] um die Anfrage zu bearbeiten!");
            }
        }

        // Save war
        if (isset($_POST['sbmit_new_war']) && intval($_POST['alliance_bnd_alliance_id']) > 0 && checker_verify()) {
            $id = intval($_POST['alliance_bnd_alliance_id']);

            if ($allianceDiplomacyRepository->existsDiplomacyBetween($cu->allianceId(), $id)) {
                error_msg("Deine Allianz steht schon in einer Beziehung (B&uuml;ndnis/Krieg) mit der ausgew&auml;hlten Allianz oder es ist bereits eine Bewerbung um ein B&uuml;ndnis vorhanden!");
            } else {
                $allianceDiplomacyRepository->add($cu->allianceId(), $id, AllianceDiplomacyLevel::WAR, $_POST['alliance_bnd_text'], '', $cu->id, DIPLOMACY_POINTS_PER_WAR, $_POST['alliance_bnd_text_pub']);

                success_msg("Du hast einer Allianz den Krieg erkl&auml;rt!");

                $allianceHistoryRepository->addEntry((int) $cu->allianceId, "Der Allianz [b]" . $allianceNamesWithTags[$id] . "[/b] wird der Krieg erkl&auml;rt!");
                $allianceHistoryRepository->addEntry($id, "Die Allianz [b]" . $allianceNamesWithTags[$cu->allianceId] . "[/b] erkl&auml;rt den Krieg!");

                //Nachricht an den Leader der gegnerischen Allianz schreiben
                $founderId = $allianceRepository->getFounderId($id);
                $messageRepository->createSystemMessage($founderId, MSG_ALLYMAIL_CAT, 'Kriegserklärung', "Die Allianz [b]" . $allianceNamesWithTags[$cu->allianceId] . "[/b] erklärt euch den Krieg!\n
                        Die Kriegserklärung wurde von [b]" . $cu->nick . "[/b] geschrieben.\n Geh auf die Allianzseite für mehr Details!");
            }
        }

        // End pact
        if (isset($_POST['submit_pact_end']) && isset($_POST['id']) && intval($_POST['id']) > 0) {
            $id = intval($_POST['id']);

            $diplomacy = $allianceDiplomacyRepository->getDiplomacy($id, $cu->allianceId, AllianceDiplomacyLevel::BND_CONFIRMED);
            if ($diplomacy !== null) {
                if ($diplomacy->alliance1Id == $cu->allianceId) {
                    $opId = $diplomacy->alliance2Id;
                    $opName = $diplomacy->alliance2Name;
                    $opTag = $diplomacy->alliance2Tag;
                    $selfId = $diplomacy->alliance1Id;
                    $selfName = $diplomacy->alliance1Name;
                    $selfTag = $diplomacy->alliance1Tag;
                } else {
                    $opId = $diplomacy->alliance1Id;
                    $opName = $diplomacy->alliance1Name;
                    $opTag = $diplomacy->alliance1Tag;
                    $selfId = $diplomacy->alliance2Id;
                    $selfName = $diplomacy->alliance2Name;
                    $selfTag = $diplomacy->alliance2Tag;
                }

                //Delete Bnd Forum
                /** @var AllianceBoardTopicRepository $allianceBoardRepository */
                $allianceBoardRepository = $app[AllianceBoardTopicRepository::class];
                $allianceBoardRepository->deleteBndTopic($diplomacy->id);

                // Delete entity
                $allianceDiplomacyRepository->deleteDiplomacy($diplomacy->id);

                // Add log
                $allianceHistoryRepository->addEntry($selfId, "Das Bündnis [b]" . $diplomacy->name . "[/b] mit der Allianz [b][" . $opTag . "] " . $opName . "[/b] wird aufgelöst!");
                $allianceHistoryRepository->addEntry($opId, "Die Allianz [b][" . $selfTag . "] " . $selfName . "[/b] löst das Bündnis [b]" . $diplomacy->name . "[/b] auf!");

                // Send message to leader
                $founderId = $allianceRepository->getFounderId($opId);
                $messageRepository->createSystemMessage($founderId, MSG_ALLYMAIL_CAT, "Bündnis " . $diplomacy->name . " beendet", "Die Allianz [b][" . $selfTag . "] " . $selfName . "[/b] beendet ihr Bündnis [b]" . $diplomacy->name . "[/b] mit eurer Allianz!\n
                        Ausgelöst von [b]" . $cu->nick . "[/b].\nBegründung: " . $_POST['pact_end_text']);

                echo "Das B&uuml;ndnis <b>" . $diplomacy->name . "</b> mit der Allianz <b>" . $opName . "</b> wurde aufgel&ouml;st!<br/><br/>";
            }
        }

        // Withdraw pact offer
        if (isset($_POST['submit_withdraw_pact']) && isset($_POST['id']) && intval($_POST['id']) > 0) {
            $id = intval($_POST['id']);

            $diplomacy = $allianceDiplomacyRepository->getDiplomacy($id, $cu->allianceId());
            if ($diplomacy !== null && $diplomacy->alliance1Id == $cu->allianceId()) {
                $allianceDiplomacyRepository->deleteDiplomacy($diplomacy->id);

                // Inform opposite leader
                $otherAlliance = $allianceRepository->getAlliance($diplomacy->alliance2Id);
                $messageRepository->createSystemMessage($otherAlliance->founderId, MSG_ALLYMAIL_CAT, "Anfrage zurückgenommen", "Die Allianz [b]" . $diplomacy->alliance1Name . "[/b] hat ihre Büdnisanfrage wieder zurückgezogen.");

                // Display message
                echo "Anfrage gel&ouml;scht! Die Allianzleitung der Allianz <b>" . $diplomacy->otherAllianceName . "</b> wurde per Nachricht dar&uuml;ber informiert.<br/><br/>";
            }
        }

        // Accept pact offer
        if (isset($_POST['pact_accept']) && isset($_POST['id']) && intval($_POST['id']) > 0) {
            $id = intval($_POST['id']);

            $diplomacy = $allianceDiplomacyRepository->getDiplomacy($id, $cu->allianceId(), AllianceDiplomacyLevel::BND_REQUEST);
            if ($diplomacy !== null && $diplomacy->alliance2Id == $cu->allianceId()) {
                // Send message to alliance leader
                $otherFounderId = $allianceRepository->getFounderId($diplomacy->alliance1Id);
                $text = "Das Bündnis [b]" . $diplomacy->name . "[/b] zwischen den Allianzen [b][" . $diplomacy->alliance1Tag . "] " . $diplomacy->alliance1Name . "[/b] und [b][" . $diplomacy->alliance2Tag . "] " . $diplomacy->alliance2Name . "[/b] ist zustande gekommen!\n\nBitte denke daran, einen öffentlichen Text zum Bündnis hinzuzufügen!\n[b]Nachricht:[/b] " . $_POST['pact_answer'];
                $messageRepository->createSystemMessage($otherFounderId, MSG_ALLYMAIL_CAT, "Bündnis angenommen", $text);

                // Log decision
                $text = "Die Allianzen [b][" . $diplomacy->alliance1Tag . "] " . $diplomacy->alliance1Name . "[/b] und [b][" . $diplomacy->alliance2Tag . "] " . $diplomacy->alliance2Name . "[/b] schliessen ein Bündnis!";
                $allianceHistoryRepository->addEntry($diplomacy->alliance2Id, $text);
                $allianceHistoryRepository->addEntry($diplomacy->alliance1Id, $text);

                // Save pact
                $allianceDiplomacyRepository->acceptBnd($id, DIPLOMACY_POINTS_PER_PACT);
                success_msg("Bündniss angenommen! Bitte denke daran, einen öffentlichen Text zum Bündnis hinzuzufügen!");
            }
        }

        // Reject pact offer
        if (isset($_POST['pact_reject']) && isset($_POST['id']) && intval($_POST['id']) > 0) {
            $id = intval($_POST['id']);

            $diplomacy = $allianceDiplomacyRepository->getDiplomacy($id, $cu->allianceId(), AllianceDiplomacyLevel::BND_REQUEST);
            if ($diplomacy !== null && $diplomacy->alliance2Id == $cu->allianceId()) {
                // Nachricht an den Leader der anfragenden Allianz
                $otherFounderId = $allianceRepository->getFounderId($diplomacy->alliance1Id);
                $text = "Die Bündnisanfrage [b]" . $diplomacy->name . "[/b] wurde von der Allianz [b][" . $diplomacy->alliance2Tag . "] " . $diplomacy->alliance2Name . "[/b] abgelehnt!\n\n[b]Nachricht:[/b] " . $_POST['pact_answer'];
                $messageRepository->createSystemMessage($otherFounderId, MSG_ALLYMAIL_CAT, "Bündnisantrag abgelehnt", $text);

                // Löscht BND
                $allianceDiplomacyRepository->deleteDiplomacy($diplomacy->id);

                // Logt die Absage
                $allianceHistoryRepository->addEntry($diplomacy->alliance1Id, "Die Bündnisanfrage [b]" . $diplomacy->name . "[/b] der Allianz [b][" . $diplomacy->alliance2Tag . "] " . $diplomacy->alliance2Name . "[/b] wird abgelehnt!");
                $allianceHistoryRepository->addEntry($diplomacy->alliance2Id, "Die Bündnisanfrage [b]" . $diplomacy->name . "[/b] wird von der Allianz [b][" . $diplomacy->alliance1Tag . "] " . $diplomacy->alliance1Name . "[/b] abgelehnt!");

                success_msg("Bündniss abgelehnt!");
            }
        }

        // Save public pact text
        if (isset($_POST['submit_pact_public_text']) && isset($_POST['id']) && intval($_POST['id']) > 0) {
            $id = intval($_POST['id']);

            $allianceDiplomacyRepository->updatePublicText($id, $cu->allianceId(), AllianceDiplomacyLevel::BND_CONFIRMED, $_POST['alliance_bnd_text_pub']);
            success_msg("Text gespeichert!");
        }

        // Save public war text
        if (isset($_POST['submit_war_public_text']) && isset($_POST['id']) && intval($_POST['id']) > 0) {
            $id = intval($_POST['id']);

            $allianceDiplomacyRepository->updatePublicText($id, $cu->allianceId(), AllianceDiplomacyLevel::WAR, $_POST['alliance_bnd_text_pub']);
            success_msg("Text gespeichert!");
        }

        // Beziehungen laden
        $diplomacies = $allianceDiplomacyRepository->getDiplomacies($cu->allianceId);
        $relations = array();
        if (count($diplomacies) > 0) {
            foreach ($diplomacies as $diplomacy) {
                $relations[$diplomacy->otherAllianceId] = $diplomacy;
            }
        }

        // Allianzen laden
        $alliances = $allianceRepository->getAlliances();
        if (count($alliances) > 1) {
            tableStart("&Uuml;bersicht");
            echo "<tr><th colspan=\"2\">Allianz</td>
                    <th>Status</td>
                    <th>Start</td>
                    <th>Ende / Name</td>
                    <th>Aktionen</td>
                    </tr>";
            foreach ($alliances as $otherAlliance) {
                if ($otherAlliance->id === $cu->allianceId()) {
                    continue;
                }

                echo "<tr>
                            <td>
                                <a href=\"?page=alliance&amp;info_id=" . $otherAlliance->id . "\">
                                [" . $otherAlliance->tag . "]
                                </a>
                            </td>
                            <td>
                             " . BBCodeUtils::toHTML($otherAlliance->name) . "
                            </td>";

                if (isset($relations[$otherAlliance->id])) {
                    $relation = $relations[$otherAlliance->id];
                    if ($relation->level === AllianceDiplomacyLevel::BND_CONFIRMED) {
                        echo "<td style=\"color:#0f0;\">B&uuml;ndnis</td>";
                        echo "<td>" . StringUtils::formatDate($relation->date) . "</td>";
                        echo "<td>" . $relation->name . "</td>";
                    } elseif ($relation->level === AllianceDiplomacyLevel::WAR) {
                        echo "<td style=\"color:#f00;\">Krieg</td>";
                        echo "<td>" . StringUtils::formatDate($relation->date) . "</td>";
                        echo "<td>" . StringUtils::formatDate($relation->date + WAR_DURATION) . "</td>";
                    } elseif ($relation->level === AllianceDiplomacyLevel::PEACE) {
                        echo "<td style=\"color:#3f9;\">Frieden</td>";
                        echo "<td>" . StringUtils::formatDate($relation->date) . "</td>";
                        echo "<td>" . StringUtils::formatDate($relation->date + PEACE_DURATION) . "</td>";
                    } elseif ($relation->level === AllianceDiplomacyLevel::BND_REQUEST) {
                        if ($relation->alliance2Id === $otherAlliance->id) {
                            echo "<td style=\"color:#ff0;\">Anfrage</td>";
                        } else {
                            echo "<td style=\"color:#f90;\">Anfrage an uns</td>";
                        }
                        echo "<td>" . StringUtils::formatDate($relation->date) . "</td>";
                        echo "<td>-</td>";
                    } else {
                        echo "<td>-</td>";
                        echo "<td>-</td>";
                        echo "<td>-</td>";
                    }
                } else {
                    echo "<td>-</td>";
                    echo "<td>-</td>";
                    echo "<td>-</td>";
                }

                echo "<td>";

                if (isset($relations[$otherAlliance->id])) {
                    $relation = $relations[$otherAlliance->id];
                    if ($relation->level === AllianceDiplomacyLevel::BND_CONFIRMED) {
                        echo "<a href=\"?page=$page&action=relations&amp;view=" . $relation->id . "\">Details</a> &nbsp; ";
                        echo "<a href=\"?page=$page&action=relations&amp;end_pact=" . $relation->id . "\">Auflösen</a> ";
                    } elseif ($relation->level === AllianceDiplomacyLevel::WAR) {
                        echo "<a href=\"?page=$page&action=relations&view=" . $relation->id . "\">Kriegserklärung</a> ";
                    } elseif ($relation->level === AllianceDiplomacyLevel::PEACE) {
                        echo "-";
                    } elseif ($relation->level === AllianceDiplomacyLevel::BND_REQUEST) {
                        if ($relation->alliance2Id === $otherAlliance->id) {
                            echo "<a href=\"?page=$page&action=relations&view=" . $relation->id . "\">Anschauen / Löschen</a> ";
                        } else {
                            echo "<a href=\"?page=$page&action=relations&view=" . $relation->id . "\">Beantworten</a> ";
                        }
                    } else {
                        if ($otherAlliance->acceptBnd) {
                            echo "<a href=\"?page=$page&action=relations&amp;begin_bnd=" . $otherAlliance->id . "\">B&uuml;ndnis</a> &nbsp; ";
                        }
                        echo "<a href=\"?page=$page&action=relations&amp;begin_war=" . $otherAlliance->id . "\">Krieg</a> ";
                    }
                } else {
                    if ($otherAlliance->acceptBnd) {
                        echo "<a href=\"?page=$page&action=relations&amp;begin_bnd=" . $otherAlliance->id . "\">B&uuml;ndnis</a> &nbsp; ";
                    }
                    echo "<a href=\"?page=$page&action=relations&amp;begin_war=" . $otherAlliance->id . "\">Krieg</a> ";
                }
                echo "</td></tr>";
            }
            tableEnd();
        } else
            error_msg("Es gibt noch keine Allianzen, welcher du den Krieg erkl&auml;ren kannst.");
        echo "<input type=\"button\" value=\"Zur&uuml;ck zur Hauptseite\" onclick=\"document.location='?page=$page'\" />";
    }
}
