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

    //
    // Bearbeiten
    //
    if (isset($_GET['action']) && $_GET['action'] == "edit") {
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

        // Technologien laden
        /** @var TechnologyDataRepository $technologyDataRepository */
        $technologyDataRepository = $app[TechnologyDataRepository::class];
        $technologyNames = $technologyDataRepository->getTechnologyNames(true);

        echo '<div id="tabs-2">';

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
