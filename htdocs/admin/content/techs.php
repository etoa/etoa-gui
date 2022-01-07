<?PHP

use EtoA\Admin\Forms\TechnologiesForm;
use EtoA\Admin\Forms\TechnologyTypesForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyListItemSearch;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologySort;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

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

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "type") {
    TechnologyTypesForm::render($app, $request);
}

//
// Technologien
//
elseif ($sub == "data") {
    TechnologiesForm::render($app, $request);
}
//
// Anforderungen
//
elseif ($sub == "req") {

    define("TITLE", "Forschungsanforderungen");
    define("REQ_TBL", "tech_requirements");
    define("ITEM_ENABLE_FLD", "tech_show");

    define("ITEM_IMAGE_PATH", ObjectWithImage::BASE_PATH . "/technologies/technology<DB_TABLE_ID>_small.png");

    $objectNames = $technologyDataRepository->getTechnologyNames(true, TechnologySort::type());
    include("inc/requirements.inc.php");
}

//
// Liste
//
else {
    \EtoA\Admin\LegacyTemplateTitleHelper::$title = 'Forschungsliste';

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

        $search = TechnologyListItemSearch::create();
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

            $search->userId((int) $updata[1]);
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
            $_SESSION['search']['tech']['query'] = serialize($search);
        }

        // Suchquery generieren
        elseif (!isset($_SESSION['search']['tech']['query'])) {
            if ($_POST['planet_id'] != '') {
                $search->entityId((int) $_POST['planet_id']);
            }
            if ($_POST['planet_name'] != '') {
                $search->likePlanetName($_POST['planet_name']);
            }
            if ($_POST['user_id'] != '') {
                $search->userId((int) $_POST['user_id']);
            }
            if ($_POST['user_nick'] != "") {
                $search->likeUserNick($_POST['user_nick']);
            }
            if ($_POST['tech_id'] != '') {
                $search->technologyId($_POST['tech_id']);

            }
            $_SESSION['search']['tech']['query'] = serialize($search);
        } else
            $sql = unserialize($_SESSION['search']['tech']['query'], ['allowed_classes' => [TechnologyListItemSearch::class]]);

        $technologyListItems = $technologyRepository->adminSearchQueueItems($search);
        if (count($technologyListItems) > 0) {
            echo count($technologyListItems) . " Datens&auml;tze vorhanden<br/><br/>";
            if (count($technologyListItems) > 20)
                echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /><br/><br/>";

            echo "<table class=\"tbl\">";
            echo "<tr>";
            echo "<td class=\"tbltitle\" valign=\"top\">Planet</td>";
            echo "<td class=\"tbltitle\" valign=\"top\">Spieler</td>";
            echo "<td class=\"tbltitle\" valign=\"top\">Forschung</td>";
            echo "<td class=\"tbltitle\" valign=\"top\">Stufe</td>";
            echo "<td class=\"tbltitle\" valign=\"top\">Status</td>";
            echo "</tr>";
            foreach ($technologyListItems as $item) {
                if ($item->buildType == 3)
                    $style = " style=\"color:#0f0\"";
                else
                    $style = "";
                echo "<tr>";
                echo "<td class=\"tbldata\"$style " . mTT($item->planetName, $item->entity->coordinatesString()) . ">" . StringUtils::cutString($item->planetName != '' ? $item->planetName : 'Unbenannt', 11) . "</a> [" . $item->entity->id . "]</a></td>";
                echo "<td class=\"tbldata\"$style " . mTT($item->userNick, StringUtils::formatNumber($item->userPoints) . " Punkte") . ">" . StringUtils::cutString($item->userNick, 11) . "</a></td>";
                echo "<td class=\"tbldata\"$style>" . $item->technologyName . "</a></td>";
                echo "<td class=\"tbldata\"$style>" . StringUtils::formatNumber($item->currentLevel) . "</a></td>";
                echo "<td class=\"tbldata\"$style>" . $build_type[$item->buildType] . "</a></td>";
                echo "<td class=\"tbldata\">" . edit_button("?page=$page&sub=$sub&action=edit&techlist_id=" . $item->id) . "</td>";
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
        echo "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\" />&nbsp;";
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
        echo "<p>Es sind " . StringUtils::formatNumber($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.</p>";
    }
}
