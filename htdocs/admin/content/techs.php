<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ranking\RankingService;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologySort;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var TechnologyRepository $technologyRepository */
$technologyRepository = $app[TechnologyRepository::class];

/** @var TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[TechnologyDataRepository::class];

/** @var TechnologyPointRepository $technologyPointRepository */
$technologyPointRepository = $app[TechnologyPointRepository::class];

/** @var RankingService $rankingService */
$rankingService = $app[RankingService::class];
/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

//
// Forschungspunkte
//
if ($sub == "points") {
    echo "<h1>Forschungspunkte</h1>";
    echo "<h2>Forschungpsunkte neu berechnen</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
    if (isset($_POST['recalc']) && $_POST['recalc'] != "") {
        $numTechnologies = $rankingService->calcTechPoints();
        echo MessageBox::ok("", sprintf("Die Punkte von %s Technologien wurden aktualisiert!", $numTechnologies));
    }
    echo "Nach jeder &Auml;nderung an den Forschungen m&uuml;ssen die Forschungspunkte neu berechnet werden.<br/><br/> ";
    echo "<input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";

    echo "<h2>Forschungspunkte</h2>";
    $technologyNames = $technologyDataRepository->getTechnologyNames(true);
    if (count($technologyNames) > 0) {
        $techPoints = $technologyPointRepository->getAllMap();
        echo "<table class=\"tb\">";
        foreach ($technologyNames as $technologyId => $technologyName) {
            echo "<tr><th>" . $technologyName . "</th><td style=\"width:70%\"><table class=\"tb\">";
            if (isset($techPoints[$technologyId])) {
                $cnt = 0;
                foreach ($techPoints[$technologyId] as $level => $points) {
                    if ($cnt == 0)
                        echo "<tr>";
                    echo "<th>" . $level . "</th><td style=\"text-align: right\" title=\"$points\">" . nf($points) . "</td>";
                    if ($cnt == "3") {
                        echo "</tr>";
                        $cnt = 0;
                    } else
                        $cnt++;
                }
                if ($cnt != 0) {
                    for ($x = $cnt; $x < 4; $x++) {
                        echo "<td colspan=\"2\"></td>";
                    }
                    echo "</tr>";
                }
            }
            echo "</table></td></tr>";
        }
        echo "</table>";
    }
}

//
// Kategorien
//
elseif ($sub == "type") {
    simple_form("tech_types", $twig);
}

//
// Technologien
//
elseif ($sub == "data") {
    advanced_form("technologies", $twig);
}
//
// Anforderungen
//
elseif ($sub == "req") {

    define("TITLE", "Forschungsanforderungen");
    define("REQ_TBL", "tech_requirements");
    define("ITEM_ENABLE_FLD", "tech_show");

    define("ITEM_IMAGE_PATH", IMAGE_PATH . "/technologies/technology<DB_TABLE_ID>_small." . IMAGE_EXT);

    $objectNames = $technologyDataRepository->getTechnologyNames(true, TechnologySort::type());
    include("inc/requirements.inc.php");
}

//
// Liste
//
else {
    $twig->addGlobal('title', 'Forschungsliste');

    $build_type = [];
    $build_type[0] = "Unt&auml;tig";
    $build_type[3] = "Forschen";

    if (isset($_POST['techlist_search']) || (isset($_GET['action']) && ($_GET['action'] == "search" || $_GET['action'] == "searchresults")) || isset($_POST['new'])) {
        if (isset($_GET['query']) && $_GET['query'] != "") {
            $qs = searchQueryDecode($_GET['query']);
            foreach ($qs as $k => $v) {
                $_POST[$k] = $v;
            }
            $_SESSION['search']['tech']['query'] = null;
        }
        $sql = "";
        $query = "";
        $sqlstart = "
            SELECT
                    planet_name,
                    planets.id as id,
              entities.pos,
              cells.sx,cells.sy,
              cells.cx,cells.cy,
              user_nick,
              user_points,
              tech_name,
              techlist_id,
              techlist_build_type,
              techlist_current_level
            FROM
                techlist
            INNER JOIN
                technologies
            ON
                techlist.techlist_tech_id=technologies.tech_id
            INNER JOIN
                planets
            ON
                techlist_entity_id=planets.id
            INNER JOIN
                entities
            ON
                planets.id=entities.id
            INNER Join
                cells
            ON
                entities.cell_id=cells.id
            INNER JOIN
                users
            ON
                techlist.techlist_user_id=users.user_id
            ";
        $sqlend = "
            GROUP BY
                techlist_id
            ORDER BY
                techlist_entity_id,
                tech_type_id,
                tech_order,
                tech_name;";

        // Forschung hinzufügen
        if (isset($_POST['new'])) {
            $updata = explode(":", $_POST['planet_id']);

            $technologyNames = $technologyDataRepository->getTechnologyNames(true);
            if (isset($_POST['all_techs'])) {
                foreach (array_keys($technologyNames) as $technologyId) {
                    $technologyRepository->addTechnology($technologyId, (int) $_POST['techlist_current_level'], (int) $updata[1], (int) $updata[0]);
                }
                echo "Technologien wurden aktualisiert!<br/>";
            } else {
                $technologyRepository->addTechnology((int) $_POST['tech_id'], (int) $_POST['techlist_current_level'], (int) $updata[1], (int) $updata[0]);
                echo "Technologie wurde hinzugefügt!<br/>";
            }

            $sql = " AND user_id=" . $updata[1];
            $_SESSION['search']['tech']['query'] = null;

            // Hinzufügen
            echo "<h2>Neue Technologien hinzuf&uuml;gen</h2>";
            echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
            tableStart();
            echo "<tr><th class=\"tbltitle\">Technologie:</th><td class=\"tbldata\"><select name=\"tech_id\">";
            foreach ($technologyNames as $techId => $technologyName) {
                echo "<option value=\"" . $techId . "\"";
                if ($techId == $_POST['tech_id']) echo " selected=\"selected\"";
                echo ">" . $technologyName . "</option>";
            }
            echo "</select></td></tr>";
            if ($_POST['techlist_current_level'])
                $v = $_POST['techlist_current_level'];
            else
                $v = 1;
            echo "<tr><th class=\"tbltitle\">Stufe</th><td class=\"tbldata\"><input type=\"text\" name=\"techlist_current_level\" value=\"$v\" size=\"1\" maxlength=\"3\" /></td></tr>";
            echo "<tr><th class=\"tbltitle\">f&uuml;r den Spieler</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
            $userNicks = $userRepository->searchUserNicknames();
            $mainPlanets = $planetRepository->getMainPlanets();
            foreach ($mainPlanets as $mainPlanet) {
                echo "<option value=\"" . $mainPlanet->id . ":" . $mainPlanet->userId . "\"";
                if ($updata[1] == $mainPlanet->userId) echo " selected=\"selected\"";
                echo ">" . $userNicks[$mainPlanet->userId] . "</option>";
            }
            echo "</select></td></tr>";
            tableEnd();
            echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form><br/>";
            $sql = $sqlstart . $sql . $sqlend;
            $_SESSION['search']['tech']['query'] = $sql;
        }

        // Suchquery generieren
        elseif (!isset($_SESSION['search']['tech']['query'])) {
            if ($_POST['planet_id'] != '')
                $sql .= " AND planets.id='" . $_POST['planet_id'] . "'";
            if ($_POST['planet_name'] != '') {
                if (stristr($_POST['qmode']['planet_name'], "%"))
                    $addchars = "%";
                else $addchars = "";
                $sql .= " AND planet_name " . stripslashes($_POST['qmode']['planet_name']) . $_POST['planet_name'] . "$addchars'";
            }
            if ($_POST['user_id'] != '')
                $sql .= " AND user_id='" . $_POST['user_id'] . "'";
            if ($_POST['user_nick'] != "") {
                if (stristr($_POST['qmode']['user_nick'], "%"))
                    $addchars = "%";
                else $addchars = "";
                $sql .= " AND user_nick " . stripslashes($_POST['qmode']['user_nick']) . $_POST['user_nick'] . "$addchars'";
            }
            if ($_POST['tech_id'] != '')
                $sql .= " AND tech_id='" . $_POST['tech_id'] . "'";

            $sql = $sqlstart . $sql . $sqlend;
            $_SESSION['search']['tech']['query'] = $sql;
        } else
            $sql = $_SESSION['search']['tech']['query'];

        $res = dbquery($sql);
        if (mysql_num_rows($res) > 0) {
            echo mysql_num_rows($res) . " Datens&auml;tze vorhanden<br/><br/>";
            if (mysql_num_rows($res) > 20)
                echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /><br/><br/>";

            echo "<table class=\"tbl\">";
            echo "<tr>";
            echo "<td class=\"tbltitle\" valign=\"top\">Planet</td>";
            echo "<td class=\"tbltitle\" valign=\"top\">Spieler</td>";
            echo "<td class=\"tbltitle\" valign=\"top\">Forschung</td>";
            echo "<td class=\"tbltitle\" valign=\"top\">Stufe</td>";
            echo "<td class=\"tbltitle\" valign=\"top\">Status</td>";
            echo "</tr>";
            while ($arr = mysql_fetch_array($res)) {
                if ($arr['techlist_build_type'] == 3)
                    $style = " style=\"color:#0f0\"";
                else
                    $style = "";
                echo "<tr>";
                echo "<td class=\"tbldata\"$style " . mTT($arr['planet_name'], $arr['sx'] . "/" . $arr['sy'] . " : " . $arr['cx'] . "/" . $arr['cy'] . " : " . $arr['pos']) . ">" . cut_string($arr['planet_name'] != '' ? $arr['planet_name'] : 'Unbenannt', 11) . "</a> [" . $arr['id'] . "]</a></td>";
                echo "<td class=\"tbldata\"$style " . mTT($arr['user_nick'], nf($arr['user_points']) . " Punkte") . ">" . cut_string($arr['user_nick'], 11) . "</a></td>";
                echo "<td class=\"tbldata\"$style>" . $arr['tech_name'] . "</a></td>";
                echo "<td class=\"tbldata\"$style>" . nf($arr['techlist_current_level']) . "</a></td>";
                echo "<td class=\"tbldata\"$style>" . $build_type[$arr['techlist_build_type']] . "</a></td>";
                echo "<td class=\"tbldata\">" . edit_button("?page=$page&sub=$sub&action=edit&techlist_id=" . $arr['techlist_id']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
        } else {
            echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
        }
    }

    //
    // Bearbeiten
    //
    elseif (isset($_GET['action']) && $_GET['action'] == "edit") {
        if (isset($_POST['save'])) {
            $entry = $technologyRepository->getEntry($_GET['techlist_id']);
            if ($entry !== null) {
                $entry->currentLevel = (int) $_POST['techlist_current_level'];
                $entry->buildType = (int) $_POST['techlist_build_type'];
                $entry->startTime = $_POST['techlist_build_start_time'] ? (new \DateTime($_POST['techlist_build_start_time']))->getTimestamp() : 0;
                $entry->endTime = $_POST['techlist_build_end_time'] ? (new \DateTime($_POST['techlist_build_end_time']))->getTimestamp() : 0;
                $technologyRepository->save($entry);
            }

        } elseif (isset($_POST['del'])) {
            $technologyRepository->removeEntry($_GET['techlist_id']);
        }

        $entry = $technologyRepository->getEntry($_GET['techlist_id']);
        if ($entry !== null) {
            $technologyNames = $technologyDataRepository->getTechnologyNames(true);
            $userNick = $userRepository->getNick($entry->userId);
            $planet = $planetRepository->find($entry->entityId);
            echo "<form action=\"?page=$page&sub=$sub&action=edit&techlist_id=" . $entry->id . "\" method=\"post\">";
            echo "<table class=\"tbl\">";
            echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td><td class=\"tbldata\">" . $entry->id . "</td></tr>";
            echo "<tr><td class=\"tbltitle\" valign=\"top\">Planet</td><td class=\"tbldata\">" . ($planet !== null ? $planet->name : '') . "</td></tr>";
            echo "<tr><td class=\"tbltitle\" valign=\"top\">Spieler</td><td class=\"tbldata\">" . $userNick . "</td></tr>";
            echo "<tr><td class=\"tbltitle\" valign=\"top\">Geb&auml;ude</td><td class=\"tbldata\">" . $technologyNames[$entry->id] . "</td></tr>";
            echo "<tr><td class=\"tbltitle\" valign=\"top\">Level</td><td class=\"tbldata\"><input type=\"text\" name=\"techlist_current_level\" value=\"" . $entry->currentLevel . "\" size=\"2\" maxlength=\"3\" /></td></tr>";
            echo "<tr><td class=\"tbltitle\" valign=\"top\">Baustatus</td><td class=\"tbldata\"><select name=\"techlist_build_type\">";
            foreach ($build_type as $id => $val) {
                echo "<option value=\"$id\"";
                if ($entry->buildType == $id) echo " selected=\"selected\"";
                echo ">$val</option>";
            }
            echo "</select></td></tr>";

            if ($entry->startTime > 0) $bst = date($config->get('admin_dateformat'), $entry->startTime);
            else $bst = "";
            if ($entry->endTime > 0) $bet = date($config->get('admin_dateformat'), $entry->endTime);
            else $bet = "";
            echo "<tr><td class=\"tbltitle\" valign=\"top\">Baustart</td><td class=\"tbldata\"><input type=\"text\" name=\"techlist_build_start_time\" id=\"techlist_build_start_time\" value=\"$bst\" size=\"20\" maxlength=\"30\" /> <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('techlist_build_start_time').value='" . date("Y-m-d H:i:s") . "'\" /></td></tr>";
            echo "<tr><td class=\"tbltitle\" valign=\"top\">Bauende</td><td class=\"tbldata\"><input type=\"text\" name=\"techlist_build_end_time\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
            echo "</table>";
            echo "<br/><input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />&nbsp;";
            echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" />&nbsp;";
            echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" value=\"Neue Suche\" />&nbsp;";
            echo "</form>";
        } else
            echo "Dieser Datensatz wurde gel&ouml;scht!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />;";
    }

    //
    // Suchformular Technologien
    //
    else {
        echo '<div class="tabs">
            <ul>
                <li><a href="#tabs-1">Suchmaske</a></li>
                <li><a href="#tabs-2">Direkt hinzufügen</a></li>
            </ul>
            <div id="tabs-1">';

        $_SESSION['search']['tech']['query'] = null;

        // Technologien laden
        /** @var TechnologyDataRepository $technologyDataRepository */
        $technologyDataRepository = $app[TechnologyDataRepository::class];
        $technologyNames = $technologyDataRepository->getTechnologyNames(true);

        // Suchmaske
        echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
        echo "<table class=\"tbl\">";
        echo "<tr><td class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><td class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
        fieldqueryselbox('planet_name');
        echo "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\" />&nbsp;";
        fieldqueryselbox('user_nick');
        echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
        echo "<tr><td class=\"tbltitle\">Forschung</td><td class=\"tbldata\"><select name=\"tech_id\"><option value=\"\"><i>---</i></option>";
        foreach ($technologyNames as $techId => $technologyName)
            echo "<option value=\"" . $techId . "\">" . $technologyName . "</option>";
        echo "</select></td></tr>";
        echo "</table>";
        echo "<br/><input type=\"submit\" name=\"techlist_search\" value=\"Suche starten\" /></form>";

        echo '</div><div id="tabs-2">';

        // Hinzufügen
        echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
        echo "<table class=\"tbl\">";
        echo "<tr><th class=\"tbltitle\">Technologie</th><td class=\"tbldata\"><select name=\"tech_id\">";
        foreach ($technologyNames as $techId => $technologyName)
            echo "<option value=\"" . $techId . "\">" . $technologyName . "</option>";
        echo "</select><br>Alle Techs <input type='checkbox' name='all_techs'></td></tr>";
        echo "<tr><th class=\"tbltitle\">Stufe</th><td class=\"tbldata\"><input type=\"text\" name=\"techlist_current_level\" value=\"1\" size=\"1\" maxlength=\"3\" /></td></tr>";
        echo "<tr><th class=\"tbltitle\">f&uuml;r den Spieler</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
        $userNicks = $userRepository->searchUserNicknames();
        $mainPlanets = $planetRepository->getMainPlanets();
        foreach ($mainPlanets as $mainPlanet) {
            echo "<option value=\"" . $mainPlanet->id . ":" . $mainPlanet->userId . "\">" . $userNicks[$mainPlanet->userId] . "</option>";
        }
        echo "</select></td></tr>";
        tableEnd();
        echo "<p><input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></p></form>";

        echo '
                </div>
            </div>';

        $tblcnt = $technologyRepository->count();
        echo "<p>Es sind " . nf($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.</p>";
    }
}
