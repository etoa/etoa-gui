<?PHP

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AlliancePollRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRight;
use EtoA\Alliance\AllianceRightRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var AllianceRankRepository $allianceRankRepository */
$allianceRankRepository = $app[AllianceRankRepository::class];
/** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
$allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var AllianceRightRepository $allianceRightRepository */
$allianceRightRepository = $app[AllianceRightRepository::class];

echo "<h1>Allianz</h1>";
echo "<div id=\"allianceinfo\"></div>"; //nur zu entwicklungszwecken!

/**************************************************/
/* Allianzinformationen                           */
/**************************************************/
if ((isset($_GET['info_id']) && intval($_GET['info_id']) > 0) || (isset($_GET['id']) && intval($_GET['id']) > 0)) {
    require("alliance/info.inc.php");
}

/**************************************************/
/* User ist NICHT in einer Allianz                */
/**************************************************/
elseif ($cu->allianceId == 0) {
    if (time() > (int) $cu->allianceLeave + $config->getInt("alliance_leave_cooldown")) {
        require("alliance/foreign.inc.php");
    } else {
        echo '<p><b>Du musst ' . (floor((($cu->allianceLeave + $config->getInt("alliance_leave_cooldown")) - time()) / 60)) . ' Minuten warten bis du dich bei einer neuen Allianz bewerben kannst!</b></p>';
    }
} else {

    /**************************************************/
    /* User ist in der Allianz                        */
    /**************************************************/

    $myRankId = $cu->allianceRankId;

    // Allianzdaten laden
    $alliance = $allianceRepository->getAlliance($cu->allianceId());
    if ($alliance !== null) {
        $ally = new Alliance($cu->allianceId);


        // Rechte laden
        /** @var array<int, AllianceRight> $rights */
        $rights = [];
        /** @var array<string, bool> $myRight */
        $myRight = [];
        $allianceRights = $allianceRightRepository->getRights();
        if (count($allianceRights) > 0) {
            $rightIds = $allianceRankRepository->getAvailableRightIds($cu->allianceId(), $myRankId);

            foreach ($allianceRights as $right) {
                $rights[$right->id] = $right;
                $myRight[$right->key] = in_array($right->id, $rightIds, true);
            }
        }

        // Gründer prüfen
        $isFounder = ($ally->founderId == $cu->id) ? true : false;

        //
        // Allianzdaten ändern
        //
        if (isset($_GET['action']) && $_GET['action'] == "editdata") {
            if (Alliance::checkActionRights(AllianceRights::EDIT_DATA)) {
                require("alliance/editdata.inc.php");
            }
        }

        //
        // Bewerbungsvorlage bearbeiten
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "applicationtemplate") {
            if (Alliance::checkActionRights(AllianceRights::APPLICATION_TEMPLATE)) {
                require("alliance/applicationtemplate.inc.php");
            }
        }

        //
        // Umfragen anzeigen
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "viewpoll") {
            require("alliance/viewpoll.inc.php");
        }

        //
        // Umfragen erstellen / bearbeiten
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "polls") {
            if (Alliance::checkActionRights(AllianceRights::POLLS)) {
                require("alliance/polls.inc.php");
            }
        }

        //
        // Mitglieder bearbeiten
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "editmembers") {
            if (Alliance::checkActionRights(AllianceRights::EDIT_MEMBERS)) {
                require("alliance/editmembers.inc.php");
            }
        }

        //
        // Rundmail
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "massmail") {
            if (Alliance::checkActionRights(AllianceRights::MASS_MAIL)) {
                require("alliance/massmail.inc.php");
            }
        }

        //
        // Ränge
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "ranks") {
            if (Alliance::checkActionRights(AllianceRights::RANKS)) {
                require("alliance/ranks.inc.php");
            }
        }

        //
        // Bewerbungen
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "applications") {
            if (Alliance::checkActionRights(AllianceRights::APPLICATIONS)) {
                require("alliance/applications.inc.php");
            }
        }

        //
        // Allianz auflösen bestätigen
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "liquidate"  && !$cu->alliance->isAtWar()) {
            if (Alliance::checkActionRights(AllianceRights::LIQUIDATE)) {
                require("alliance/liquidate.inc.php");
            }
        }

        //
        // Allianz-News
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "alliancenews") {
            if (Alliance::checkActionRights(AllianceRights::ALLIANCE_NEWS)) {
                require("alliance/alliancenews.inc.php");
            }
        }

        //
        // Bündniss-/Kriegspartner wählen
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "relations") {
            if (Alliance::checkActionRights(AllianceRights::RELATIONS)) {
                require("alliance/diplomacy.inc.php");
            }
        }

        //
        // Geschichte anzeigen
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "history") {
            if (Alliance::checkActionRights(AllianceRights::HISTORY)) {
                require("alliance/history.inc.php");
            }
        }

        //
        // Mitglieder anzeigen
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "viewmembers") {
            if (Alliance::checkActionRights(AllianceRights::VIEW_MEMBERS)) {
                require("alliance/viewmembers.inc.php");
            }
        }

        //
        // Wings verwalten
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "wings" && $config->getBoolean('allow_wings')) {
            if (Alliance::checkActionRights(AllianceRights::WINGS)) {
                require("alliance/wings.inc.php");
            }
        }

        //
        // Allianz verlassen (Durchführen)
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "leave" && !$isFounder && !$cu->alliance->isAtWar()) {
            echo "<h2>Allianz-Austritt</h2>";
            if ($cu->allianceId != 0) {
                if (isset($_POST['submit_leave'])) {
                    if ($ally->kickMember($cu->id, 0)) {
                        success_msg("Du bist aus der Allianz ausgetreten!");
                    } else {
                        error_msg("Du konntest nicht aus der Allianz austreten, da die Allianz entweder im Krieg ist oder du noch Allianzflotten in der Luft hast!!");
                    }
                    echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
                } else {
                    echo "<form action=\"?page=$page&amp;action=leave\" method=\"post\">";
                    echo "<p>Willst du die Allianz wirklich verlassen?</p>";
                    echo "<p>
                    <input type=\"submit\" name=\"submit_leave\" value=\"Ja\" />
                    <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Nein\" />
                </p>";
                    echo "</form>";
                }
            } else
                echo "Du bist in keiner Allianz!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
        }

        //
        // Allianzbasis
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "base") {
            require("alliance/base.inc.php");
        }

        //
        // Allianz-Hauptseite anzeigen
        //
        else {
            // Änderungen übernehmen
            if (isset($_POST['editsubmit']) && checker_verify()) {
                // Prüft Korrektheit des Allianztags und Namen, wenn diese geändert haben
                $check = false;
                if ($_POST['alliance_tag'] != $alliance->tag || $_POST['alliance_name'] != $alliance->name) {
                    // Prüfen, ob der Allianzname bzw. Tag nicht nur aus Leerschlägen besteht
                    $check_tag = str_replace(' ', '', $_POST['alliance_tag']);
                    $check_name = str_replace(' ', '', $_POST['alliance_name']);

                    if ($check_name != '' && $check_tag != '') {
                        $check_tag = StringUtils::checkIllegalSigns($_POST['alliance_tag']);
                        $check_name = StringUtils::checkIllegalSigns($_POST['alliance_name']);
                        $signs = StringUtils::checkIllegalSigns("gibt eine liste von unerlaubten zeichen aus! ; < > & etc.");
                        if ($check_tag == "" && $check_name == "") {

                            // Prüft, ob dieser Tag oder Name bereits vorhanden ist
                            if ($allianceRepository->exists($_POST['alliance_tag'], $_POST['alliance_name'], $cu->allianceId())) {
                                error_msg("Der gewünschte Tag oder Name ist bereits vergeben!");
                            }
                            // Name / Tag sind noch nicht vergeben
                            else {
                                $check = true;
                            }
                        } else {
                            error_msg("Unerlaubte Zeichen (" . $signs . ") im Allianztag oder im Allianznamen!");
                        }
                    } else {
                        error_msg("Der Allianzname und Allianztag dürfen nicht nur aus Leerzeichen besttehen!");
                    }
                }

                // Name und/oder Tag wird übernommen
                if ($check) {
                    $alliance_tag = $_POST['alliance_tag'];
                    $alliance_name = $_POST['alliance_name'];

                    /** @var \EtoA\Alliance\AllianceHistoryRepository $allianceHistoryRepository */
                    $allianceHistoryRepository = $app[\EtoA\Alliance\AllianceHistoryRepository::class];
                    $allianceHistoryRepository->addEntry((int) $cu->allianceId, "[b]" . $cu->nick . "[/b] ändert den Allianzname und/oder Tag von [b]" . $alliance->name . " (" . $alliance->tag . ")[/b] in [b]" . $_POST['alliance_name'] . " (" . $_POST['alliance_tag'] . ")[/b]!");
                }
                // Name und/oder Tag sind fehlerhaft
                else {
                    $alliance_tag = $alliance->tag;
                    $alliance_name = $alliance->name;
                }

                // Prüft Korrektheit des Allianzbildes
                $alliance_img_string = "";
                $updatedAllianceImage = null;
                if (isset($_POST['alliance_img_del']) && $_POST['alliance_img_del'] == 1) {
                    if (file_exists(ALLIANCE_IMG_DIR . "/" . $alliance->image)) {
                        @unlink(ALLIANCE_IMG_DIR . "/" . $alliance->image);
                    }
                    $updatedAllianceImage = '';
                } elseif ($_FILES['alliance_img_file']['tmp_name'] != "") {
                    $imup = new ImageUpload('alliance_img_file', ALLIANCE_IMG_DIR, "alliance_" . $cu->allianceId . "_" . time());
                    $imup->setMaxSize(ALLIANCE_IMG_MAX_SIZE);
                    $imup->setMaxDim(ALLIANCE_IMG_MAX_WIDTH, ALLIANCE_IMG_MAX_HEIGHT);
                    $imup->enableResizing(ALLIANCE_IMG_WIDTH, ALLIANCE_IMG_HEIGHT);

                    if ($imup->process()) {
                        $updatedAllianceImage = $imup->getResultName();
                        success_msg("Allianzbild hochgeladen!");
                    }
                }

                if (!isset($message)) {
                    $message = "";
                }

                $allianceRepository->update($alliance->id, $alliance_tag, $alliance_name, $_POST['alliance_text'], $alliance->applicationTemplate, $_POST['alliance_url'], $alliance->founderId, $updatedAllianceImage, (bool) $_POST['alliance_accept_applications'], (bool) $_POST['alliance_accept_bnd'], (bool) $_POST['alliance_public_memberlist']);
                $alliance = $allianceRepository->getAlliance($cu->allianceId());
                echo "Die &Auml;nderungen wurden übernommen!<br/>" . $message . "<br/>";

                // Hack
                $ally = new Alliance($cu->allianceId);
            }

            // Bewerbungsvorlage speichern
            if (isset($_POST['applicationtemplatesubmit']) && $_POST['applicationtemplatesubmit'] != "" && checker_verify()) {
                $allianceRepository->updateApplicationText($alliance->id, $_POST['alliance_application_template']);
                echo "Die &Auml;nderungen wurden übernommen!<br/><br/>";
            }

            // Allianz auflösen
            if (
                isset($_POST['liquidatesubmit'])
                && $_POST['liquidatesubmit'] != ""
                && $isFounder
                && $cu->allianceId == $_POST['id_control']
                && checker_verify()
                && !$cu->alliance->isAtWar()
            ) {
                $ally->delete($cu);
                echo "Die Allianz wurde aufgel&ouml;st!<br/><br/>
            <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"&Uuml;bersicht\" />";
            }
            // Allianzdaten anzeigen
            else {

                $ally->visits++;


                tableStart("[" . stripslashes($alliance->tag) . "] " . stripslashes($alliance->name));
                if ($alliance->image != "") {
                    $im = ALLIANCE_IMG_DIR . "/" . $alliance->image;
                    if (file_exists($im)) {
                        $ims = getimagesize($im);
                        echo "<tr><td class=\"tblblack\" colspan=\"3\" style=\"text-align:center;background:#000\">
                    <img src=\"" . $im . "\" alt=\"Allianz-Logo\" style=\"width:" . $ims[0] . "px;height:" . $ims[1] . "\" /></td></tr>";
                    }
                }

                // Internes Forum verlinken
                /** @var AllianceBoardTopicRepository $allianceBoardTopicRepository */
                $allianceBoardTopicRepository = $app[AllianceBoardTopicRepository::class];
                if ($myRight[AllianceRights::ALLIANCE_BOARD] || $isFounder) {
                    $topic = $allianceBoardTopicRepository->getAllianceTopicWithLatestPost($alliance->id);
                } else {
                    $topic = $allianceBoardTopicRepository->getAllianceTopicWithLatestPost($alliance->id, $myRankId);
                }

                if ($topic !== null) {
                    $ps = "Neuster Post: <a href=\"?page=allianceboard&amp;topic=" . $topic->id . "#" . $topic->post->id . "\"><b>" . $topic->subject . "</b>, geschrieben von: <b>" . $topic->post->userNick . "</b>, <b>" . StringUtils::formatDate($topic->timestamp) . "</b></a>";
                } else
                    $ps = "<i>Noch keine Beitr&auml;ge vorhanden";
                echo "<tr><th>Internes Forum</th><td colspan=\"2\"><b><a href=\"?page=allianceboard\">Forum&uuml;bersicht</a></b> &nbsp; $ps</td></tr>";

                // Umfrage verlinken
                /** @var AlliancePollRepository $alliancePollRepository */
                $alliancePollRepository = $app[AlliancePollRepository::class];
                $polls = $alliancePollRepository->getPolls($alliance->id, 2);
                $pcnt = count($polls);
                if ($pcnt > 0) {
                    echo "<tr><th>Umfrage:</th>
                <td colspan=\"2\"><a href=\"?page=$page&amp;action=viewpoll\"><b>" . stripslashes($polls[0]->title) . ":</b> " . stripslashes($polls[0]->question) . "</a>";
                    if ($pcnt > 1)
                        echo " &nbsp; (<a href=\"?page=$page&amp;action=viewpoll\">mehr Umfragen</a>)";
                    echo "</td></tr>";
                }

                // Bewerbungen anzeigen
                if ($isFounder || $myRight[AllianceRights::APPLICATIONS]) {
                    /** @var AllianceApplicationRepository $allianceApplicationRepository */
                    $allianceApplicationRepository = $app[AllianceApplicationRepository::class];
                    $applications = $allianceApplicationRepository->countApplications($cu->allianceId());
                    if ($applications > 0) {
                        echo "<tr><th colspan=\"3\" align=\"center\">
                    <div align=\"center\"><b><a href=\"?page=$page&action=applications\">Es sind Bewerbungen vorhanden!</a></b></div>
                    </th></tr>";
                    }
                }

                // Wing-Anfrage
                if ($config->getBoolean('allow_wings') && ($isFounder || $myRight[AllianceRights::WINGS]) && $ally->motherRequestId > 0) {
                    echo "<tr><th colspan=\"3\" align=\"center\">
                <div align=\"center\"><b><a href=\"?page=$page&action=wings\">Es ist eine Wing-Anfrage vorhanden!</a></b></div>
                </th></tr>";
                }

                if ($config->getBoolean('allow_wings') && $ally->motherId != 0) {
                    echo "<tr>
                                <th colspan=\"3\" style=\"text-align:center;\">
                                    Diese Allianz ist ein Wing von <b><a href=\"?page=$page&amp;action=info&amp;id=" . $ally->motherId . "\">" . $ally->mother . "</a></b>
                                </th>
                            </tr>";
                }


                // Bündnissanfragen anzeigen
                if ($isFounder || $myRight[AllianceRights::RELATIONS]) {
                    if ($allianceDiplomacyRepository->hasPendingBndRequests($cu->allianceId()))
                        echo "<tr>
                        <th colspan=\"3\" style=\"text-align:center;color:#0f0\">
                            <a  style=\"color:#0f0\" href=\"?page=$page&action=relations\">Es sind B&uuml;ndnisanfragen vorhanden!</a>
                    </th></tr>";
                }

                // Kriegserklärung anzeigen
                $time = time() - 192600;
                if ($allianceDiplomacyRepository->wasWarDeclaredAgainstSince($cu->allianceId(), $time)) {
                    if ($isFounder || $myRight[AllianceRights::RELATIONS])
                        echo "<tr>
                    <th colspan=\"3\" align=\"center\"><b>
                        <div align=\"center\"><a href=\"?page=$page&action=relations\">Deiner Allianz wurde in den letzten 36h der Krieg erkl&auml;rt!</a></div></b></th></tr>";
                    else
                        echo "<tr><th colspan=\"3\" align=\"center\"><div align=\"center\"><b>Deiner Allianz wurde in den letzten 36h der Krieg erkl&auml;rt!</b></div></th></tr>";
                }

                // Verwaltung
                $adminBox = array();

                if ($isFounder || $myRight[AllianceRights::VIEW_MEMBERS]) {
                    $adminBox["Mitglieder anzeigen"] = "?page=$page&amp;action=viewmembers";
                }
                $adminBox["Allianzbasis"] = "?page=$page&action=base";
                if ($config->getBoolean('allow_wings') && ($isFounder || $myRight[AllianceRights::WINGS])) {
                    $adminBox["Wings verwalten"] = "?page=$page&action=wings";
                }
                if ($isFounder || $myRight[AllianceRights::HISTORY]) {
                    $adminBox["Geschichte"] = "?page=$page&action=history";
                }
                if ($isFounder || $myRight[AllianceRights::ALLIANCE_NEWS]) {
                    $adminBox["Allianznews (Rathaus)"] = "?page=$page&action=alliancenews";
                }
                if ($isFounder || $myRight[AllianceRights::RELATIONS]) {
                    $adminBox["Diplomatie"] = "?page=$page&action=relations";
                }
                if ($isFounder || $myRight[AllianceRights::POLLS]) {
                    $adminBox["Umfragen verwalten"] = "?page=$page&action=polls";
                }
                if ($isFounder || $myRight[AllianceRights::MASS_MAIL]) {
                    $adminBox["Rundmail"] = "?page=$page&action=massmail";
                }
                if ($isFounder || $myRight[AllianceRights::EDIT_MEMBERS]) {
                    $adminBox["Mitglieder verwalten"] = "?page=$page&action=editmembers";
                }
                if ($isFounder || $myRight[AllianceRights::RANKS]) {
                    $adminBox["Ränge"] = "?page=$page&action=ranks";
                }
                if ($isFounder || $myRight[AllianceRights::EDIT_DATA]) {
                    $adminBox["Allianz-Daten bearbeiten"] = "?page=$page&amp;action=editdata";
                }
                if ($isFounder || $myRight[AllianceRights::APPLICATION_TEMPLATE]) {
                    $adminBox["Bewerbungsvorlage"] = "?page=$page&action=applicationtemplate";
                }
                if ($isFounder && !$cu->alliance->isAtWar()) {
                    $adminBox["Allianz aufl&ouml;sen"] = "?page=$page&action=liquidate";
                }
                if (!$isFounder && !$cu->alliance->isAtWar()) {
                    $adminBox["Allianz verlassen"] = "?page=$page&action=leave";
                    //array_push($adminBox,"<a href=\"\" onclick=\"return confirm('Allianz wirklich verlassen?');\"></a>");
                }

                echo "<tr><th width=\"120\" >Verwaltung:</th>";
                echo "<td colspan=\"2\">";
                echo "<div class=\"threeColumnList allianceManagementLinks\">";
                foreach ($adminBox as $k => $v) {
                    echo "<a href=\"$v\">$k</a><br/>";
                }
                echo "</div>";
                echo "</td></tr>";


                // Letzte Ereignisse anzeigen
                if ($isFounder || $myRight[AllianceRights::HISTORY]) {
                    echo "<tr>
                    <th width=\"120\">Letzte Ereignisse:</th>
                    <td colspan=\"2\">";

                    /** @var AllianceHistoryRepository $allianceHistoryRepository */
                    $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                    $entries = $allianceHistoryRepository->findForAlliance($cu->allianceId, 5);
                    if (count($entries) > 0) {
                        foreach ($entries as $entry) {
                            echo "<div class=\"infoLog\">" . BBCodeUtils::toHTML($entry->text) . " <span>" . StringUtils::formatDate($entry->timestamp, false) . "</span></div>";
                        }
                    }
                    echo "</td></tr>";
                }

                // Text anzeigen
                if ($alliance->text != "") {
                    echo "<tr><td colspan=\"3\" style=\"text-align:center\">" . BBCodeUtils::toHTML($alliance->text) . "</td></tr>\n";
                }

                // Kriege
                $wars = $allianceDiplomacyRepository->getDiplomacies($ally->id, AllianceDiplomacyLevel::WAR);
                if (count($wars) > 0) {
                    echo "<tr>
                                <th>Kriege:</th>
                                <td>
                                    <table class=\"tbl\">
                                        <tr>
                                            <th>Allianz</th>
                                            <th>Punkte</th>
                                            <th>Zeitraum</th>
                                        </tr>";
                    foreach ($wars as $diplomacy) {
                        $opAlly = new Alliance($diplomacy->otherAllianceId);
                        echo "<tr>
                                            <td>
                                                <a href=\"?page=$page&amp;id=" . $diplomacy->otherAllianceId . "\">" . $opAlly . "</a>
                                            </td>
                                            <td>" . StringUtils::formatNumber($opAlly->points) . " / " . StringUtils::formatNumber($opAlly->avgPoints) . "</td>
                                            <td>" . StringUtils::formatDate($diplomacy->date, false) . " bis " . StringUtils::formatDate($diplomacy->date + WAR_DURATION, false) . "</td>
                                        </tr>";
                    }
                    echo "</table>
                                </td>
                            </tr>";
                }


                // Friedensabkommen
                $peace = $allianceDiplomacyRepository->getDiplomacies($ally->id, AllianceDiplomacyLevel::PEACE);
                if (count($peace) > 0) {
                    echo "<tr>
                                <th>Friedensabkommen:</th>
                                <td>
                                    <table class=\"tbl\">
                                        <tr>
                                            <th>Allianz</th>
                                            <th>Punkte</th>
                                            <th>Zeitraum</th>
                                        </tr>";
                    foreach ($peace as $diplomacy) {
                        $opAlly = new Alliance($diplomacy->otherAllianceId);
                        echo "<tr>
                                            <td>
                                                <a href=\"?page=$page&amp;id=" . $diplomacy->otherAllianceId . "\">" . $opAlly . "</a>
                                            </td>
                                            <td>" . StringUtils::formatNumber($opAlly->points) . " / " . StringUtils::formatNumber($opAlly->avgPoints) . "</td>
                                            <td>" . StringUtils::formatDate($diplomacy->date, false) . " bis " . StringUtils::formatDate($diplomacy->date + PEACE_DURATION, false) . "</td>
                                        </tr>";
                    }
                    echo "</table>
                                </td>
                            </tr>";
                }

                // Bündnisse
                $bnds = $allianceDiplomacyRepository->getDiplomacies($ally->id, AllianceDiplomacyLevel::BND_CONFIRMED);
                if (count($bnds) > 0) {
                    echo "<tr>
                                <th>Bündnisse:</th>
                                <td>
                                    <table class=\"tbl\">
                                        <tr>
                                            <th>Bündnisname</th>
                                            <th>Allianz</th>
                                            <th>Punkte</th>
                                            <th>Seit</th>
                                        </tr>";

                    foreach ($bnds as $diplomacy) {
                        $opAlly = new Alliance($diplomacy->otherAllianceId);
                        echo "<tr>
                                            <td>" . stripslashes($diplomacy->name) . "</td>
                                            <td><a href=\"?page=$page&amp;id=" . $diplomacy->otherAllianceId . "\">" . $opAlly . "</a></td>
                                            <td>" . StringUtils::formatNumber($opAlly->points) . " / " . StringUtils::formatNumber($opAlly->avgPoints) . "</td>
                                            <td>" . StringUtils::formatDate($diplomacy->date) . "</td>
                                        </tr>";
                    }
                    echo "</table>
                                </td>
                            </tr>";
                }

                // Besucher
                echo "<tr><th width=\"120\">Besucherzähler:</th>
            <td colspan=\"2\">" . StringUtils::formatNumber($ally->visits) . " intern / " . StringUtils::formatNumber($ally->visitsExt) . " extern</td></tr>\n";

                // Wings
                if ($config->getBoolean('allow_wings') && count($ally->wings) > 0) {
                    echo "<tr><th width=\"120\">Wings:</th><td colspan=\"2\">";
                    echo "<table class=\"tb\">";
                    echo "<tr>
                    <th>Name</th>
                    <th>Punkte</th>
                    <th>Mitglieder</th>
                    <th>Punkteschnitt</th>
                </tr>";
                    foreach ($ally->wings as $wid => $wdata) {
                        echo "<tr>
                    <td><a href=\"?page=alliance&amp;id=" . $wid . "\">" . $wdata . "</a></td>
                    <td>" . StringUtils::formatNumber($wdata->points) . "</td>
                    <td>" . $wdata->memberCount . "</td>
                    <td>" . StringUtils::formatNumber($wdata->avgPoints) . "</td>
                    </tr>";
                    }
                    echo "</td></tr>";
                    tableEnd();
                    echo "</td></tr>";
                }


                // Website
                if ($alliance->url != "") {
                    echo "<tr><th width=\"120\">Website/Forum:</th><td colspan=\"2\"><b>" .
                        StringUtils::formatLink($alliance->url) . "</a></b></td></tr>\n";
                }

                // Diverses
                echo "<tr><th width=\"120\">Mitglieder:</th>
            <td colspan=\"2\">" . $ally->memberCount . "</td></tr>\n";
                // Punkte
                echo "<tr>
                            <th>Punkte / Schnitt:</th>
                            <td colspan=\"2\">";
                echo StringUtils::formatNumber($ally->points) . " / " . StringUtils::formatNumber($ally->avgPoints) . "";
                echo "</td>
                        </tr>";
                echo "<tr><th width=\"120\">Gr&uuml;nder:</th>
            <td colspan=\"2\">
                <a href=\"?page=userinfo&amp;id=" . $ally->founderId . "\">" . $ally->founder . "</a></td></tr>";
                // Gründung
                echo "<tr>
                            <th>Gründungsdatum:</th>
                            <td colspan=\"2\">
                                " . StringUtils::formatDate($ally->foundationDate) . " (vor " . StringUtils::formatTimespan(time() - $ally->foundationDate) . ")
                            </td>
                        </tr>";
                echo "\n</table><br/>";
            }
        }
    } else {
        if ($_POST['resolvefalseallyid'] != "") {
            $userRepository->setAllianceId($cu->getId(), 0, 0);
            success_msg("Die fehlerhafte Verkn&uuml;pfung wurde gel&ouml;st!");
        } else
            echo "<form action=\"?page=$page\" method=\"post\">Diese Allianz existiert nicht!<br/><br/>
        <input type=\"submit\" name=\"resolvefalseallyid\" value=\"Fehlerhafte Allianzverkn&uuml;pfung l&ouml;schen\" /></form>";
    }
}
