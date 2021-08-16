<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Log\FleetLogFacility;
use EtoA\Log\GameLogFacility;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;
use Twig\Environment;

// TODO
global $app;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

$twig->addGlobal('title', 'Logs');

echo "<div id=\"logsinfo\"></div>"; //nur zu entwicklungszwecken!

if ($sub == "errorlog") {
    errorlog($twig);
} elseif (isset($_POST['alliance_search']) && $_POST['alliance_search'] != "" || isset($_GET['action']) && $_GET['action'] == "searchresults") {
    logSearchResults();
} elseif (isset($_GET['sub']) && $_GET['sub'] == "view") {
    viewEntry();
} elseif (isset($_GET['sub']) && $_GET['sub'] == "logs_battle") {
    battleLog();
} elseif (isset($_GET['sub']) && $_GET['sub'] == "logs_game") {
    gameLog($config);
} elseif (isset($_POST['logs_submit']) && $_POST['logs_submit'] != "" && checker_verify()) {
    commonLog();
} elseif (isset($_GET['sub']) && $_GET['sub'] == "new_logs_page") {
    newLog();
} elseif ($sub == "check_fights") {
    checkFights();
} elseif ($sub == "gamelogs") {
    newGamelogs();
} elseif ($sub == "fleetlogs") {
    newFleetLogs();
} elseif ($sub == "debrislog") {
    debrisLog();
} else {
    newCommonLog();
}

function errorlog(Environment $twig)
{
    global $page;
    global $sub;

    if (isset($_POST['purgelog_submit'])) {
        file_put_contents(ERROR_LOGFILE, '');
        forward('?page=' . $page . '&sub=' . $sub);
    }

    $logFile = null;
    if (is_file(ERROR_LOGFILE)) {
        $logFile = file_get_contents(ERROR_LOGFILE);
    }

    echo $twig->render('admin/logs/errorlog.html.twig', [
        'logFile' => $logFile,
    ]);
    exit();
}

function logSearchResults()
{
    global $page;

    if ($_SESSION['logs']['query'] == "") {
        $sql = '';
        $sqlstart = "SELECT * FROM logs,log_cat WHERE log_cat=cat_id ";
        $sqlend = " ORDER BY log_realtime DESC, log_timestamp DESC";
        if ($_POST['limit'] > 0)
            $sqlend .= " LIMIT " . $_POST['limit'] . ";";
        if ($_POST['log_text'] != "") {
            if (stristr($_POST['qmode']['log_text'], "%")) $addchars = "%";
            else $addchars = "";
            $sql .= " AND log_text " . stripslashes($_POST['qmode']['log_text']) . $_POST['log_text'] . "$addchars'";
        }
        if ($_POST['log_text2'] != "") {
            if (stristr($_POST['qmode']['log_text2'], "%")) $addchars = "%";
            else $addchars = "";
            $sql .= " AND log_text " . stripslashes($_POST['qmode']['log_text2']) . $_POST['log_text2'] . "$addchars'";
        }
        if ($_POST['log_hostname'] != "") {
            if (stristr($_POST['qmode']['log_hostname'], "%")) $addchars = "%";
            else $addchars = "";
            $sql .= " AND log_hostname " . stripslashes($_POST['qmode']['log_hostname']) . $_POST['log_hostname'] . "$addchars'";
        }
        if ($_POST['log_ip'] != "") {
            if (stristr($_POST['qmode']['log_ip'], "%")) $addchars = "%";
            else $addchars = "";
            $sql .= " AND log_ip " . stripslashes($_POST['qmode']['log_ip']) . $_POST['log_ip'] . "$addchars'";
        }
        if ($_POST['log_cat'] > 0) {
            $sql .= " AND log_cat=" . $_POST['log_cat'];
        }
        $sql = $sqlstart . $sql . $sqlend;
        $_SESSION['logs']['query'] = $sql;
    } else
        $sql = $_SESSION['logs']['query'];

    $res = dbquery($sql);
    if (mysql_num_rows($res) > 0) {
        echo mysql_num_rows($res) . " Datens&auml;tze vorhanden<br/><br/>";
        if (mysql_num_rows($res) > 20)
            echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page'\" /><br/><br/>";

        echo "<table class=\"tbl\">";
        echo "<tr>";
        echo "<td class=\"tbltitle\" valign=\"top\">Zeit</td>";
        echo "<td class=\"tbltitle\" valign=\"top\">Kategorie</td>";
        echo "<td class=\"tbltitle\" valign=\"top\">Text</td>";
        echo "<td class=\"tbltitle\" valign=\"top\">Computer</td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
        while ($arr = mysql_fetch_array($res)) {
            echo "<tr>";
            echo "<td class=\"tbldata\" valign=\"top\"><b>" . date("d.m.Y H:i:s", $arr['log_timestamp']) . "</b><br/>";
            if ($arr['log_realtime'] > 0)
                echo date("Y-m-d H:i:s", $arr['log_realtime']);
            else
                echo "-";
            echo "</td>";
            echo "<td class=\"tbldata\">" . $arr['cat_name'] . "</td>";
            echo "<td class=\"tbldata\" valign=\"top\">" . text2html(cut_string($arr['log_text'], 300)) . "</td>";
            echo "<td class=\"tbldata\" valign=\"top\">" . $arr['log_ip'] . " " . $arr['log_hostname'] . "</td>";
            echo "<td class=\"tbldata\" valign=\"top\"><a href=\"?page=$page&amp;sub=view&amp;log_id=" . $arr['log_id'] . "\">detail</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page'\" />";
    } else {
        echo "Die Suche lieferte keine Resultate!<br/><br/>";
        echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" /><br/><br/>";
    }
}

function viewEntry()
{
    global $page;

    $res = dbquery("SELECT * FROM logs,log_cat WHERE log_cat=cat_id AND log_id=" . $_GET['log_id'] . ";");
    $arr = mysql_fetch_array($res);
    echo "<table class=\"tbl\">";
    echo "<tr><td class=\"tbltitle\" valign=\"top\">Zeit</td><td class=\"tbldata\">" . date("Y-m-d H:i:s", $arr['log_timestamp']) . "</td></tr>";
    echo "<tr><td class=\"tbltitle\" valign=\"top\">Realzeit</td><td class=\"tbldata\">";
    if ($arr['log_realtime'] > 0)
        echo date("Y-m-d H:i:s", $arr['log_realtime']);
    else
        echo "-";
    echo "</td></tr>";
    echo "<tr><td class=\"tbltitle\" valign=\"top\">Kategorie</td><td class=\"tbldata\">" . $arr['cat_name'] . "</td></tr>";
    echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td><td class=\"tbldata\">" . text2html($arr['log_text']) . "</td></tr>";
    echo "<tr><td class=\"tbltitle\" valign=\"top\">Hostname</td><td class=\"tbldata\">" . $arr['log_hostname'] . "</td></tr>";
    echo "<tr><td class=\"tbltitle\" valign=\"top\">IP</td><td class=\"tbldata\">" . $arr['log_ip'] . "</td></tr>";
    echo "</table>";
    echo "<br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
    echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page'\" />";
}

function battleLog()
{
    echo "Battle Log im aufbau!<br>";
}

function gameLog(ConfigurationService $config)
{
    global $page;

    $lres = dbquery("
    SELECT
        logs_game_cat_id,
        logs_game_cat_name,
        COUNT(logs_game_id) as cnt
    FROM
        logs_game_cat
        INNER JOIN
        logs_game
        ON logs_game_cat=logs_game_cat_id
    GROUP BY
        logs_game_cat_id;");
    $logs_game_type = array();
    while ($larr = mysql_fetch_array($lres)) {
        $logs_game_type[$larr['logs_game_cat_id']]['name'] = $larr['logs_game_cat_name'];
        $logs_game_type[$larr['logs_game_cat_id']]['cnt'] = $larr['cnt'];
    }


    $_SESSION['logs']['query'] = Null;
    echo "Suchmaske:<br/><br/>";
    echo "<form action=\"?page=$page\" method=\"post\">";
    echo "<table class=\"tbl\">";

    echo "<tr><td class=\"tbltitle\">Kategorie</td><td class=\"tbldata\"><select name=\"logs_game_cat\">";
    echo "<option value=\"0\">(nicht zugeordnet)</option>";
    foreach ($logs_game_type as $id => $val) {
        echo "<option value=\"$id\">" . $val['name'] . " (" . $val['cnt'] . ")</option>";
    }
    echo "</select></td></tr>";

    echo "<tr><td class=\"tbltitle\">Planetenname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
    fieldqueryselbox('planet_name');
    echo "</td></tr>";
    echo "<tr><td class=\"tbltitle\">Planeten-ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
    echo "<tr><td class=\"tbltitle\">Koordinaten</td><td class=\"tbldata\"><select name=\"cell_sx\">";
    echo "<option value=\"\">(egal)</option>";
    for ($x = 1; $x <= $config->param1Int('num_of_sectors'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select>/<select name=\"cell_sy\">";
    echo "<option value=\"\">(egal)</option>";
    for ($x = 1; $x <= $config->param2Int('num_of_sectors'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select> : <select name=\"cell_cx\">";
    echo "<option value=\"\">(egal)</option>";
    for ($x = 1; $x <= $config->param1Int('num_of_cells'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select>/<select name=\"cell_cy\">";
    echo "<option value=\"\">(egal)</option>";
    for ($x = 1; $x <= $config->param2Int('num_of_cells'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select> : <select name=\"planet_solsys_pos\">";
    echo "<option value=\"\">(egal)</option>";
    for ($x = 1; $x <= $config->param2Int('num_planets'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select></td></tr>";
    echo "<tr><td class=\"tbltitle\">Besitzer-ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
    echo "<tr><td class=\"tbltitle\">Besitzer</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
    fieldqueryselbox('user_nick');
    echo "</td></tr>";
    echo "<tr><td class=\"tbltitle\">Allianz-Tag</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
    fieldqueryselbox('alliance_tag');
    echo "</td></tr>";
    echo "</table></form>";
}

function commonLog()
{
    global $page, $app;

    $sql_query = stripslashes($_POST['sql_query']);

    if ($_POST['log_cat'] == "logs") {
        echo "allgemeine logs anzeigen...";
    } elseif ($_POST['log_cat'] == "logs_fleet") {
        $res = dbquery($sql_query);

        tableStart("" . mysql_num_rows($res) . " Ergebnisse");
        echo "<tr>
                <td class=\"tbltitle\" >Besitzer</td>
                <td class=\"tbltitle\" >Aktion</td>
                <td class=\"tbltitle\" >Start</td>
                <td class=\"tbltitle\" >Ziel</td>
                <td class=\"tbltitle\" >Startzeit</td>
                <td class=\"tbltitle\" >Landezeit</td>
                <td class=\"tbltitle\" >Bericht</td>
            </tr>";
        while ($arr = mysql_fetch_array($res)) {
            $user_nick = get_user_nick($arr["fleet_user_id"]);
            if ($user_nick == "") {
                $owner = "<span style=\"color:#99f\">System</span>";
            } else {
                $owner = $user_nick;
            }

            if ($fa = FleetAction::createFactory($arr['action'])) {
                echo "<tr>";
                echo "<td class=\"tbldata\">" . $owner . "</td>";
                echo "<td class=\"tbldata\"><span style=\"color:" . FleetAction::$attitudeColor[$fa->attitude()] . "\">";
                echo $fa . "</span><br/>";
                echo FleetAction::$statusCode[$arr['status']];
                echo "</td>";
                echo "<td class=\"tbldata\" >";
                $startEntity = Entity::createFactoryById($arr['entity_from']);
                echo $startEntity . "<br/>" . $startEntity->entityCodeString() . ", " . $startEntity->owner() . "</td>";
                echo "<td class=\"tbldata\">";
                $endEntity = Entity::createFactoryById($arr['entity_to']);
                echo $endEntity . "<br/>" . $endEntity->entityCodeString() . ", " . $endEntity->owner() . "</td>";
                echo "<td class=\"tbldata\" >" . date("d.m.y", $arr['landtime']) . " &nbsp; " . date("H:i:s", $arr['landtime']) . "</td>";
                echo "<td class=\"tbldata\" >" . date("d.m.y", $arr['landtime']) . " &nbsp; " . date("H:i:s", $arr['landtime']) . "</td>";
            } else {
                echo "<tr>";
                echo "<td class=\"tbldata\" >" . $owner . "</td>";
                echo "<td class=\"tbldata\"><span style=\"color:red\">";
                echo "Ungültig (" . $arr['action'] . ")</span><br/>";
                echo "</td>";
                echo "<td class=\"tbldata\" >";
                $startEntity = Entity::createFactoryById($arr['entity_from']);
                echo $startEntity . "<br/>" . $startEntity->entityCodeString() . ", " . $startEntity->owner() . "</td>";
                echo "<td class=\"tbldata\" >";
                $endEntity = Entity::createFactoryById($arr['entity_to']);
                echo $endEntity . "<br/>" . $endEntity->entityCodeString() . ", " . $endEntity->owner() . "</td>";
                echo "<td class=\"tbldata\" >" . date("d.m.y", $arr['landtime']) . " &nbsp; " . date("H:i:s", $arr['launchtime']) . "</td>";
                echo "<td class=\"tbldata\" >" . date("d.m.y", $arr['landtime']) . " &nbsp; " . date("H:i:s", $arr['landtime']) . "</td>";
            }

            $log_text = "hamer";
            echo "<td class=\"tbldata\" onclick=\"xajax_showFleetLogs('" . $log_text . "'," . $arr['id'] . ");\" " . mTT("", "Klicken für Anzeige des Berichtes!") . ">
                                <a href=\"javascript:;\">Anzeigen</a>
                            </td>
                        </tr>
                        <tr>
                            <td class=\"tbldata\" id=\"show_fleet_logs_" . $arr['id'] . "\" style=\"vertical-align:middle;\" colspan=\"7\" ondblclick=\"xajax_showFleetLogs('" . $log_text . "'," . $arr['id'] . ");\" " . mTT("", "Doppelklick zum deaktivieren des Fensters!") . ">
                            </td>
                        </tr>";
        }
        tableEnd();
    } elseif ($_POST['log_cat'] == "logs_battle") {
        echo "Legende:<br/>
        <span style=\"color:#0f0;font-weight:bold;\">Grüner Nick</span> = Flotte hat überlebt<br/>
        <span style=\"color:red;font-weight:bold;\">Roter Nick</span> = Flotte wurde zerstört<br><br>";

        $res = dbquery($sql_query);

        tableStart("" . mysql_num_rows($res) . " Ergebnisse");
        echo "<tr>
                        <td class=\"tbltitle\" style=\"width:26%\">Zeit</td>
                        <td class=\"tbltitle\" style=\"width:18%\">Krieg?</td>
                        <td class=\"tbltitle\" style=\"width:18%\">Zählt als Angriff?</td>
                        <td class=\"tbltitle\" style=\"width:18%\">Aktion</td>
                        <td class=\"tbltitle\" style=\"width:20%\">Bericht</td>
                    </tr>";
        while ($arr = mysql_fetch_array($res)) {
            $alliance_tag_a = "";
            $alliance_tag_d = "";

            if ($arr['logs_battle_user1_alliance_id'] > 0) {
                $alliance_tag_a = " [" . $arr['logs_battle_user1_alliance_tag'] . "]";
            }

            if ($arr['logs_battle_user2_alliance_id'] > 0) {
                $alliance_tag_d = " [" . $arr['logs_battle_user2_alliance_tag'] . "]";
            }

            // Erstellt KB-Header (Kontrahenten mit Winner/Looser)
            switch ($arr['logs_battle_result']) {
                case 1:    //angreifer hat gewonnen
                    $header_user_a = "<span style=\"color:#0f0;\">" . get_user_nick($arr['logs_battle_user1_id']) . "</span>" . $alliance_tag_a . "";
                    $header_user_d = "<span style=\"color:red;\">" . get_user_nick($arr['logs_battle_user2_id']) . "</span>" . $alliance_tag_d . "";
                    break;
                case 2:    //agreifer hat verloren
                    $header_user_a = "<span style=\"color:red;\">" . get_user_nick($arr['logs_battle_user1_id']) . "</span>" . $alliance_tag_a . "";
                    $header_user_d = "<span style=\"color:#0f0;\">" . get_user_nick($arr['logs_battle_user2_id']) . "</span>" . $alliance_tag_d . "";
                    break;
                case 3:    //beide flotten haben überlebt
                    $header_user_a = "<span style=\"color:#0f0;\">" . get_user_nick($arr['logs_battle_user1_id']) . "</span>" . $alliance_tag_a . "";
                    $header_user_d = "<span style=\"color:#0f0;\">" . get_user_nick($arr['logs_battle_user2_id']) . "</span>" . $alliance_tag_d . "";
                    break;
                case 4: //beide flotten sind kaputt
                    $header_user_a = "<span style=\"color:red;\">" . get_user_nick($arr['logs_battle_user1_id']) . "</span>" . $alliance_tag_a . "";
                    $header_user_d = "<span style=\"color:red;\">" . get_user_nick($arr['logs_battle_user2_id']) . "</span>" . $alliance_tag_d . "";
                    break;
                default:
                    throw new \InvalidArgumentException('Unexpected battle result: ' . $arr['logs_battle_result']);
            }

            // Krieg?
            if ($arr['logs_battle_alliances_have_war'] == 1) {
                $war = "<div style=\"color:red;font-weight:bold;\">Ja</div>";
            } else {
                $war = "Nein";
            }

            // Zählt der Angriff als Angriff? (Waffen>0)
            if ($arr['logs_battle_user1_weapon'] > 0) {
                $attack = "Ja";
            } else {
                $attack = "<div style=\"color:red;font-weight:bold;\">Nein</div>";
            }

            $battle = text2html($arr['logs_battle_fight']);

            echo "<tr>
                            <td class=\"tbltitle\" style=\"vertical-align:middle\" colspan=\"5\">
                            " . $header_user_a . " VS. " . $header_user_d . "
                            </td>
                        </tr>
                        <tr>
                            <td class=\"tbldata\">
                                <b>" . date("Y-m-d H:i:s", $arr['logs_battle_fleet_landtime']) . "</b><br>" . date("Y-m-d H:i:s", $arr['logs_battle_time']) . "
                            </td>
                            <td class=\"tbldata\">" . $war . "</td>
                            <td class=\"tbldata\">" . $attack . "</td>
                            <td class=\"tbldata\">" . $arr['logs_battle_fleet_action'] . "</td>
                            <td class=\"tbldata\" onclick=\"xajax_showBattle('" . $battle . "'," . $arr['logs_battle_id'] . ");\" " . mTT("", "Klicken für Anzeige des Berichtes!") . ">
                                <a href=\"javascript:;\">Anzeigen</a>
                            </td>
                        </tr>
                        <tr>
                            <td class=\"tbldata\" id=\"show_battle_" . $arr['logs_battle_id'] . "\" style=\"vertical-align:middle;\" colspan=\"5\" ondblclick=\"xajax_showBattle(''," . $arr['logs_battle_id'] . ");\" " . mTT("", "Doppelklick zum deaktivieren des Fensters!") . ">
                            </td>
                        </tr>
                        ";
        }


        tableEnd();
    } elseif ($_POST['log_cat'] == "logs_game") {
        echo "<form action=\"?page=" . $page . "\" method=\"post\">";

        /** @var \EtoA\Building\BuildingDataRepository $buildingRepository */
        $buildingRepository = $app[\EtoA\Building\BuildingDataRepository::class];
        $buildingNames = $buildingRepository->getBuildingNames(true);

        /** @var TechnologyDataRepository $technologyRepository */
        $technologyRepository = $app[TechnologyDataRepository::class];
        $technologyNames = $technologyRepository->getTechnologyNames(true);

        $res = dbquery($sql_query);

        tableStart("" . mysql_num_rows($res) . " Ergebnisse");
        echo "<tr>
                        <td class=\"tbltitle\" style=\"width:26%\">Zeit</td>
                        <td class=\"tbltitle\" style=\"width:18%\">Kategorie</td>
                        <td class=\"tbltitle\" style=\"width:18%\">User</td>
                        <td class=\"tbltitle\" style=\"width:18%\">Objekt</td>
                        <td class=\"tbltitle\" style=\"width:20%\">Bericht</td>
                    </tr>";
        while ($arr = mysql_fetch_array($res)) {
            //Objekt laden
            if ($arr['logs_game_building_id'] != 0) {
                $object = $buildingNames[$arr['logs_game_building_id']] ?? "Gebäude?";
            } elseif ($arr['logs_game_tech_id'] != 0) {
                $object = $technologyNames[$arr['logs_game_tech_id']] ?? "Forschung?";
            } else {
                $object = "";
            }

            $log_text = text2html(encode_logtext($arr['logs_game_text']));

            echo "<tr>
                            <td class=\"tbldata\">
                                <b>" . date("Y-m-d H:i:s", $arr['logs_game_timestamp']) . "</b><br>" . date("Y-m-d H:i:s", $arr['logs_game_realtime']) . "
                            </td>
                            <td class=\"tbldata\">" . $arr['logs_game_cat_name'] . "</td>
                            <td class=\"tbldata\">" . get_user_nick($arr['logs_game_user_id']) . "</td>
                            <td class=\"tbldata\">" . $object . "</td>
                            <td class=\"tbldata\" onclick=\"xajax_showGameLogs('" . $log_text . "'," . $arr['logs_game_id'] . ");\" " . mTT("", "Klicken für Anzeige des Berichtes!") . ">
                                <a href=\"javascript:;\">Anzeigen</a>
                            </td>
                        </tr>
                        <tr>
                            <td class=\"tbldata\" id=\"show_game_logs_" . $arr['logs_game_id'] . "\" style=\"vertical-align:middle;\" colspan=\"5\" ondblclick=\"xajax_showGameLogs(''," . $arr['logs_game_id'] . ");\" " . mTT("", "Doppelklick zum deaktivieren des Fensters!") . ">
                            </td>
                        </tr>";
        }

        tableEnd();
    }
}

function newLog()
{
    global $page;

    echo "<h1>Neue Log Seite!</h1><br>";

    echo "Suchmaske:<br/><br/>";
    echo "<form action=\"?page=$page\" method=\"post\" id=\"log_selector\">";
    $cstr = checker_init();

    tableStart();

    //Kategorie wählen
    echo "<tr>
                    <td class=\"tbltitle\">Kategorie</td><td class=\"tbldata\">
                        <select name=\"log_cat\" onChange=\"xajax_logSelectorCat(xajax.getFormValues('log_selector'),1);\">
                            <option value=\"0\">(nicht zugeordnet)</option>
                            <option value=\"logs\">Allgemeine</option>
                            <option value=\"logs_fleet\">Flotten</option>
                            <option value=\"logs_battle\">Kampfberichte</option>
                            <option value=\"logs_game\">Game</option>
                        </select>
                    </td>
                </tr>";
    tableEnd();

    // Suchformular
    echo "<div id=\"catSelector\"></div>";
    echo "</form>";
}

function checkFights()
{
    echo "<h2>Angriffsverletzung</h2>";

?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyAttackAbuseLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            clock = new Date(<?PHP time() ?>);

            // Wandelt Timestamp in Stunden, Minuten und Sekunden um
            document.getElementById('searchtime_y').value = clock.getYear();
            document.getElementById('searchtime_m').value = clock.getMonth();
            document.getElementById('searchtime_d').value = clock.getDay();
            document.getElementById('searchtime_h').value = clock.getHours();
            document.getElementById('searchtime_i').value = clock.getMinutes();
            document.getElementById('searchtime_s').value = clock.getSeconds();

            document.getElementById('searchentity').value = '';
            document.getElementById('searchuser').value = '';
            applyFilter(0);
        }
    </script>
    <?PHP

    echo '<fieldset style="width:800px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";
    echo "<label for=\"logsev\">Ab Schweregrad:</label>
    <select id=\"logsev\" name=\"logsev\" onchange=\"applyFilter(0)\">";
    foreach (LogSeverity::SEVERITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";
    echo "<label for=\"logcat\">Aktion:</label>
    <select id=\"flaction\" name=\"flaction\" onchange=\"applyFilter(0)\">
    <option value=\"\">(Egal)</option>";
    foreach (FleetAction::getAll() as $k => $v) {
        if ($v->attitude() == 3)
            echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";
    echo " <label for=\"searchtime\">Zeit:</label> ";
    show_timebox("searchtime", time());
    echo "&nbsp; ";
    echo "<br/><br/>";
    echo " <label for=\"searchfuser\">Angreifer:</label> <input type=\"text\" id=\"searchfuser\" name=\"searchfuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo " <label for=\"searcheuser\">Verteidiger:</label> <input type=\"text\" id=\"searcheuser\" name=\"searcheuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";


    echo "<input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showAttackAbuseLogs();
    echo "</div>";
}

function newGamelogs()
{
    global $app;

    echo "<h2>Spiellogs</h2>";

    ?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyGameLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            document.getElementById('logcat').value = 0;
            document.getElementById('logsev').value = 0;
            document.getElementById('searchtext').value = '';
            document.getElementById('searchuser').value = '';
            document.getElementById('searchalliance').value = '';
            document.getElementById('searchentity').value = '';
            fillObjectSelection();
            applyFilter(0);
            document.getElementById('searchtext').focus();
        }

        function fillObjectSelection() {
            elem = document.getElementById('object_id');
            elem.length = 0;
            elem.options[elem.options.length] = new Option('(Alle)', 0);
            switch (document.getElementById('logcat').value) {
                case '1':
                    <?PHP
                    /** @var BuildingDataRepository $buildingRepository */
                    $buildingRepository = $app[BuildingDataRepository::class];
                    foreach ($buildingRepository->getBuildingNames(true) as $buildingId => $buildingName) {
                        echo "elem.options[elem.options.length] = new Option('$buildingName',$buildingId);";
                    }
                    ?>
                    break;
                case '2':
                    <?PHP
                    /** @var TechnologyDataRepository $technologyRepository */
                    $technologyRepository = $app[TechnologyDataRepository::class];
                    foreach ($technologyRepository->getTechnologyNames(true) as $techId => $technologyName) {
                        echo "elem.options[elem.options.length] = new Option('$technologyName',$techId);";
                    }
                    ?>
                    break;
                case '3':
                    <?PHP
                    /** @var ShipDataRepository $shipRepository */
                    $shipRepository = $app[ShipDataRepository::class];
                    foreach ($shipRepository->getShipNames(true) as $shipId => $shipName) {
                        echo "elem.options[elem.options.length] = new Option('$shipName',$shipId);";
                    }
                    ?>
                    break;
                case '4':
                    <?PHP
                    /** @var DefenseDataRepository $defenseRepository */
                    $defenseRepository = $app[DefenseDataRepository::class];
                    foreach ($defenseRepository->getDefenseNames(true) as $defenseId => $defenseName) {
                        echo "elem.options[elem.options.length] = new Option('$defenseId',$defenseName);";
                    }
                    ?>
                    break;
                case '5':
                    <?PHP
                    $quests = require dirname(__DIR__) . '/../../data/quests.php';
                    foreach ($quests as $quest) {
                        echo "elem.options[elem.options.length] = new Option('" . $quest['title'] . "','" . $quest['id'] . "');";
                    }
                    ?>
                    break;
            }
        }
    </script>
    <?PHP

    echo '<fieldset style="width:950px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";
    echo "<label for=\"logsev\">Ab Schweregrad:</label>
    <select id=\"logsev\" name=\"logsev\" onchange=\"applyFilter(0)\">";
    foreach (LogSeverity::SEVERITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"logcat\">Kategorie:</label>
    <select id=\"logcat\" name=\"logcat\" onchange=\"fillObjectSelection();applyFilter(0)\">
    <option value=\"0\">(Alle)</option>";
    foreach (GameLogFacility::FACILITIES as $k => $v) {
        if ($k > 0)
            echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"object_id\">Objekt:</label>
    <select id=\"object_id\" name=\"object_id\" onchange=\"applyFilter(0)\">
    <option value=\"0\">(Alle)</option>";
    echo "</select> &nbsp; ";

    echo " <label for=\"searchtext\">Suchtext:</label> <input type=\"text\" id=\"searchtext\" name=\"searchtext\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo "<br/><br/>";

    echo " <label for=\"searchuser\">User:</label> <input type=\"text\" id=\"searchuser\" name=\"searchuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo " <label for=\"searchalliance\">Allianz:</label> <input type=\"text\" id=\"searchalliance\" name=\"searchalliance\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    // Todo: extend to any entity
    echo " <label for=\"searchentity\">Planet:</label> <input type=\"text\" id=\"searchentity\" name=\"searchentity\" value=\"\" autocomplete=\"off\" /> &nbsp; ";


    echo " &nbsp; <input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showGameLogs();
    echo "</div>";
}

function newFleetLogs()
{
    echo "<h2>Flottenlogs</h2>";

    ?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyFleetLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            document.getElementById('flaction').value = 0;
            document.getElementById('logsev').value = 0;
            document.getElementById('searchuser').value = '';
            applyFilter(0);
        }
    </script>
    <?PHP

    echo '<fieldset style="width:800px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";
    echo "<label for=\"logsev\">Ab Schweregrad:</label>
    <select id=\"logsev\" name=\"logsev\" onchange=\"applyFilter(0)\">";
    foreach (LogSeverity::SEVERITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"logfac\">Facility:</label>
    <select id=\"logfac\" name=\"logfac\" onchange=\"applyFilter(0)\">
    <option value=\"\">(Alle)</option>";
    foreach (FleetLogFacility::FACILITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"logcat\">Aktion:</label>
    <select id=\"flaction\" name=\"flaction\" onchange=\"applyFilter(0)\">
    <option value=\"\">(Egal)</option>";
    foreach (FleetAction::getAll() as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp;
    <select id=\"flstatus\" name=\"flstatus\" onchange=\"applyFilter(0)\">
    <option value=\"\">(Egal)</option>";
    foreach (FleetAction::$statusCode as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select><br/><br/> ";

    echo " <label for=\"searchuser\">Flottenuser:</label> <input type=\"text\" id=\"searchuser\" name=\"searchuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";

    echo " <label for=\"searcheuser\">Entityuser:</label> <input type=\"text\" id=\"searcheuser\" name=\"searcheuser\" value=\"\" autocomplete=\"off\" /> &nbsp;<br/><br/>";

    echo " <label for=\"start\">Start:</label> <input type=\"text\" id=\"start\" name=\"start\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo " <label for=\"target\">Ziel:</label> <input type=\"text\" id=\"target\" name=\"target\" value=\"\" autocomplete=\"off\" /> &nbsp; ";

    echo "<input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showFleetLogs();
    echo "</div>";
}

function debrisLog()
{
    echo "<h2>Trümmerfeld Logs</h2>";

    ?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyDebrisLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {

            var clock = new Date();
            document.getElementById('searchtime_y').value = clock.getFullYear();
            document.getElementById('searchtime_m').value = clock.getMonth() + 1;
            document.getElementById('searchtime_d').value = clock.getUTCDate();
            document.getElementById('searchtime_h').value = clock.getHours();
            document.getElementById('searchtime_i').value = clock.getMinutes();
            document.getElementById('searchuser').value = '';
            document.getElementById('searchadmin').value = '';
            applyFilter(0);
        }
    </script>
<?PHP

    echo '<fieldset style="width:950px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";

    echo " <label for=\"searchuser\">User:</label> <input type=\"text\" id=\"searchuser\" name=\"searchuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo " <label for=\"searchadmin\">Admin:</label> <input type=\"text\" id=\"searchadmin\" name=\"searchadmin\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    // Todo: extend to any entity
    echo "<br/><br/>";

    echo " <label for=\"searchtime\">Zeit:</label> ";
    show_timebox("searchtime", time());

    echo " &nbsp; <input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showDebrisLogs();
    echo "</div>";
}

function newCommonLog()
{
    echo "<h2>Allgemeine Logs</h2>";

?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            document.getElementById('logcat').value = 0;
            document.getElementById('logsev').value = 0;
            document.getElementById('searchtext').value = '';
            applyFilter(0);
            document.getElementById('searchtext').focus();
        }
    </script>
<?PHP

    echo '<fieldset style="width:900px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";
    echo "<label for=\"logsev\">Ab Schweregrad:</label>
    <select id=\"logsev\" name=\"logsev\" onchange=\"applyFilter(0)\">";
    foreach (LogSeverity::SEVERITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"logcat\">Kategorie:</label>
    <select id=\"logcat\" name=\"logcat\" onchange=\"applyFilter(0)\">
    <option value=\"0\">(Alle)</option>";
    foreach (LogFacility::FACILITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo " <label for=\"searchtext\">Suchtext:</label> <input type=\"text\" id=\"searchtext\" name=\"searchtext\" value=\"\" /> &nbsp;
    <input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);document.getElementById('searchtext').select();return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showLogs();
    echo "</div>";

    global $app;
    /** @var LogRepository $logRepository */
    $logRepository = $app[LogRepository::class];
    $tblcnt = $logRepository->count();
    echo "<p>Es sind " . nf($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.</p>";
}
