<?PHP

use EtoA\Admin\Forms\BuildingsForm;
use EtoA\Admin\Forms\BuildingTypesForm;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Building\BuildingSort;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResourceNames;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var BuildingRepository $repository */
$repository = $app[BuildingRepository::class];

/** @var Request $request */
$request = Request::createFromGlobals();

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var BuildingPointRepository $buildingPointRepository */
$buildingPointRepository = $app[BuildingPointRepository::class];

/** @var RankingService $rankingService */
$rankingService = $app[RankingService::class];

if ($sub == "prices") {
    priceCalculator($repository);
} elseif ($sub == "type") {
    editCategories($app, $request);
} elseif ($sub == "data") {
    editData($app, $request);
} elseif ($sub == "req") {
    requirements($app);
} else {
    buildingList($request, $repository, $config);
}

function priceCalculator(BuildingRepository $repository)
{
    global $page;
    global $sub;

    $buildingNames = $repository->buildingNames();

    echo "<h1>Preisrechner</h1>";
    echo "<script type=\"text/javascript\">
        function showPrices()
        {
            xajax_buildingPrices(
            document.getElementById('c1_id').options[document.getElementById('c1_id').selectedIndex].value,
            document.getElementById('c1_level').options[document.getElementById('c1_level').selectedIndex].value
            )
        }

        function showTotalPrices()
        {
            xajax_totalBuildingPrices(xajax.getFormValues('totalCosts'));
        }
        </script>";

    echo "<h2>(Aus)baukosten (von Stufe x-1 auf Stufe x)</h2>";
    echo "<table class=\"tb\">
            <tr>
                <th>Gebäude</th>
                <th>Stufe</th>
                <th>Zeit</th>
                <th>" . ResourceNames::METAL . "</th>
                <th>" . ResourceNames::CRYSTAL . "</th>
                <th>" . ResourceNames::PLASTIC . "</th>
                <th>" . ResourceNames::FUEL . "</th>
                <th>" . ResourceNames::FOOD . "</th>
                <th>Energie</th>
            </tr>";
    echo "<tr>
        <td><select id=\"c1_id\" onchange=\"showPrices()\">";
    foreach ($buildingNames as $key => $value) {
        echo "<option value=\"" . $key . "\">" . $value . "</option>";
    }
    echo "</select></td>
        <td><select id=\"c1_level\" onchange=\"showPrices()\">";
    for ($x = 1; $x <= 40; $x++) {
        echo "<option value=\"" . ($x - 1) . "\">" . $x . "</option>";
    }
    echo "</select></td>
        <td id=\"c1_time\">-</td>
        <td id=\"c1_metal\">-</td>
        <td id=\"c1_crystal\">-</td>
        <td id=\"c1_plastic\">-</td>
        <td id=\"c1_fuel\">-</td>
        <td id=\"c1_food\">-</td>
        <td id=\"c1_power\">-</td>
        ";
    echo "</tr></table>";

    echo "<h2>Totale Kosten</h2>
        <form action=\"?page=$page&amp;sub=$sub\" method=\"post\" id=\"totalCosts\">";
    echo "<table class=\"tb\">
        <tr>
            <th>Gebäude</th>
            <th>Level</th>
            <th>" . ResourceNames::METAL . "</th>
            <th>" . ResourceNames::CRYSTAL . "</th>
            <th>" . ResourceNames::PLASTIC . "</th>
            <th>" . ResourceNames::FUEL . "</th>
            <th>" . ResourceNames::FOOD . "</th>
        </tr>";
    foreach ($buildingNames as $key => $value) {
        echo "<tr>
            <td>" . $value . "</td>
            <td>Level 0 bis <select name=\"b_lvl[" . $key . "]\" onchange=\"showTotalPrices()\">";
        for ($x = 0; $x <= 40; $x++) {
            echo "<option value=\"" . $x . "\">" . $x . "</option>";
        }
        echo "</select></td>
            <td id=\"b_metal_" . $key . "\">-</td>
            <td id=\"b_crystal_" . $key . "\">-</td>
            <td id=\"b_plastic_" . $key . "\">-</td>
            <td id=\"b_fuel_" . $key . "\">-</td>
            <td id=\"b_food_" . $key . "\">-</td>
            </tr>";
    }
    echo "<tr><td style=\"height:2px;\" colspan=\"7\"></tr>";
    echo "<tr>
            <td colspan=\"2\">Total</td>
            <td id=\"t_metal\">-</td>
            <td id=\"t_crystal\">-</td>
            <td id=\"t_plastic\">-</td>
            <td id=\"t_fuel\">-</td>
            <td id=\"t_food\">-</td>
        </tr>";
    echo "</table></form>";
}

function editCategories(Container $app, Request $request)
{
    BuildingTypesForm::render($app, $request);
}

function editData(Container $app, Request $request)
{
    BuildingsForm::render($app, $request);
}

function requirements(Container $app)
{
    define('TITLE', "Gebäudeanforderungen");
    define('ITEMS_TBL', "buildings");
    define('TYPES_TBL', "building_types");
    define('REQ_TBL', "building_requirements");

    define("ITEM_IMAGE_PATH", ObjectWithImage::BASE_PATH . "/buildings/building<DB_TABLE_ID>_small.png");

    /** @var BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[BuildingDataRepository::class];
    $objectNames = $buildingRepository->getBuildingNames(true, BuildingSort::type());
    include("inc/requirements.inc.php");
}

function buildingList(
    Request $request,
    BuildingRepository $repository,
    ConfigurationService $config,
) {
    global $page;
    global $sub;

    \EtoA\Admin\LegacyTemplateTitleHelper::$title = 'Gebäudeliste';

    $buildTypes = Building::getBuildTypes();

    $build_colors = [];
    $build_colors[0] = "inherit";
    $build_colors[1] = "red";
    $build_colors[2] = "orange";
    $build_colors[3] = "#0f0";
    $build_colors[4] = "orange";

    if ($request->request->has('save')) {
        $repository->updateBuildingListEntry(
            $request->request->getInt('buildlist_id'),
            $request->request->getInt('buildlist_current_level'),
            $request->request->getInt('buildlist_build_type'),
            (new \DateTime($request->request->get('buildlist_build_start_time')))->getTimestamp(),
            (new \DateTime($request->request->get('buildlist_build_end_time')))->getTimestamp()
        );
    } elseif ($request->request->has('del')) {
        $repository->deleteBuildingListEntry($request->request->getInt('buildlist_id'));
    }

    //
    // Datensatz bearbeiten
    //
    if ($request->query->has('action') && $request->query->get('action') == "edit") {
        echo "<h2>Datensatz bearbeiten</h2>";
        $entry = $repository->fetchBuildingListEntry($request->query->getInt('buildlist_id'));
        if ($entry !== null) {
            echo "<form action=\"?page=$page&sub=$sub&amp;action=search\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"buildlist_id\" value=\"" . $entry['buildlist_id'] . "\" />";
            echo "<table class=\"tb\">";
            echo "<tr><th>ID</th><td>" . $entry['buildlist_id'] . "</td></tr>";
            echo "<tr><th>Planet</th><td>" . $entry['planet_name'] . "</td></tr>";
            echo "<tr><th>Spieler</th><td>" . $entry['user_nick'] . "</td></tr>";
            echo "<tr><th>Gebäude</th><td>" . $entry['building_name'] . "</td></tr>";
            echo "<tr><th>Level</th><td><input type=\"text\" name=\"buildlist_current_level\" value=\"" . $entry['buildlist_current_level'] . "\" size=\"2\" maxlength=\"3\" /></td></tr>";
            echo "<tr><th>Baustatus</th><td><select name=\"buildlist_build_type\">";
            foreach ($buildTypes as $id => $val) {
                echo "<option value=\"$id\"";
                if ($entry['buildlist_build_type'] == $id) echo " selected=\"selected\"";
                echo ">$val</option>";
            }
            echo "</select></td></tr>";
            $bst = $entry['buildlist_build_start_time'] > 0 ? date($config->get('admin_dateformat'), $entry['buildlist_build_start_time']) : "";
            $bet = $entry['buildlist_build_end_time'] > 0 ? date($config->get('admin_dateformat'), $entry['buildlist_build_end_time']) : "";
            echo "<tr><th>Baustart</th><td><input type=\"text\" name=\"buildlist_build_start_time\" id=\"buildlist_build_start_time\" value=\"$bst\" size=\"20\" maxlength=\"30\" /> <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('buildlist_build_start_time').value='" . date("Y-d-m h:i") . "'\" /></td></tr>";
            echo "<tr><th>Bauende</th><td><input type=\"text\" name=\"buildlist_build_end_time\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
            echo "</table>";
            echo "<br/><input type=\"submit\" name=\"save\" value=\"Übernehmen\" />&nbsp;";
            echo "<input type=\"submit\" name=\"del\" value=\"Löschen\" />&nbsp;";
            echo "<input type=\"submit\" value=\"Zurück zu den Suchergebnissen\" />&nbsp;";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
            echo "</form>";
        } else {
            echo "Dieser Datensatz wurde gelöscht!<br/><br/><a href=\"?page=$page&amp;sub=$sub\">Neue Suche</a>";
        }
    }

    //
    // Suchergebnisse anzeigen
    //
    elseif (($request->request->has('buildlist_search') || $request->request->has('new') || isset($_SESSION['search']['buildings']['query'])) && ($request->query->has('action') && $request->query->get('action') == "search")) {
        if ($request->query->has('query') && $request->query->get('query') != "") {
            $qs = searchQueryDecode($request->query->get('query'));
            foreach ($qs as $k => $v) {
                $request->request->set($k, $v);
            }
            unset($_SESSION['search']['buildings']['query']);
        }

        echo "<h2>Suchergebnisse</h2>";

        $formData = $_SESSION['search']['buildings']['query'] ?? $request->request->all();
        $data = $repository->findByFormData($formData);
        if (count($data) > 0) {
            $_SESSION['search']['buildings']['query'] = $formData;

            echo count($data) . " Datensätze vorhanden<br/><br/>";
            if (count($data) > 20) {
                echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" /><br/><br/>";
            }

            echo "<table class=\"tbl\">";
            echo "<tr>";
            echo "<th class=\"tbltitle\">Planet</th>";
            echo "<th class=\"tbltitle\">Spieler</th>";
            echo "<th class=\"tbltitle\">Gebäude</th>";
            echo "<th class=\"tbltitle\">Stufe</th>";
            echo "<th class=\"tbltitle\">Status</th>";
            echo "<th></th>";
            echo "</tr>";
            foreach ($data as $arr) {
                echo "<tr>";
                echo "<td class=\"tbldata\"><a href=\"?page=galaxy&amp;sub=edit&amp;id=" . $arr['buildlist_entity_id'] . "\" title=\"" . $arr['planet_name'] . "\">" . StringUtils::cutString($arr['planet_name'] != '' ? $arr['planet_name'] : 'Unbenannt', 11) . "</a> [" . $arr['id'] . "]</td>";
                echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;user_id=" . $arr['buildlist_user_id'] . "\" title=\"" . $arr['user_nick'] . "\">" . StringUtils::cutString($arr['user_nick'], 11) . "</a></td>";
                echo "<td class=\"tbldata\">" . $arr['building_name'] . "</td>";
                echo "<td class=\"tbldata\">" . StringUtils::formatNumber($arr['buildlist_current_level']) . "</td>";
                echo "<td class=\"tbldata\" style=\"background:" . $build_colors[$arr['buildlist_build_type']] . "\">" . $buildTypes[$arr['buildlist_build_type']] . "</td>";
                echo "<td class=\"tbldata\">" . edit_button("?page=$page&amp;sub=$sub&amp;action=edit&amp;buildlist_id=" . $arr['buildlist_id']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
        } else {
            echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Zurück\" />";
        }
    }

    //
    // Suchformular
    //
    else {
        $buildingNames = $repository->buildingNames();

        echo '<div class="tabs">
            <ul>
                <li><a href="#tabs-1">Schnellsuche</a></li>
                <li><a href="#tabs-2">Erweiterte Suche</a></li>
            </ul>
            <div id="tabs-1">';

        // Hinzufügen
        echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\" name=\"selector\">";

        //Sonnensystem
        echo '<table class="tb">';
        echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
            <select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
        echo "<option value=\"0\">Sektor X</option>";
        for ($x = 1; $x <= $config->param1Int('num_of_sectors'); $x++) {
            echo "<option value=\"$x\">$x</option>";
        }
        echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
        echo "<option value=\"0\">Sektor Y</option>";
        for ($x = 1; $x <= $config->param2Int('num_of_sectors'); $x++) {
            echo "<option value=\"$x\">$x</option>";
        }
        echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
        echo "<option value=\"0\">Zelle X</option>";
        for ($x = 1; $x <= $config->param1Int('num_of_cells'); $x++) {
            echo "<option value=\"$x\">$x</option>";
        }
        echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
        echo "<option value=\"0\">Zelle Y</option>";
        for ($x = 1; $x <= $config->param2Int('num_of_cells'); $x++) {
            echo "<option value=\"$x\">$x</option>";
        }
        echo "</select></td></tr>";

        //User
        echo "<tr><th class=\"tbltitle\"><i>oder</i> User</th><td class=\"tbldata\">";
        echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onchange=\"xajax_searchUserList(this.value,'showBuildingsOnPlanet');\" onkeyup=\"xajax_searchUserList(this.value,'showBuildingsOnPlanet');\"><br>
            <div id=\"userlist\">&nbsp;</div>";
        echo "</td></tr>";

        //Planeten
        echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User wählen...</td></tr>";

        //Gebäude Hinzufügen
        echo "<tr><th class=\"tbltitle\">Hinzufügen:</th><td class=\"tbldata\">
            <input type=\"text\" name=\"buildlist_current_level\" value=\"1\" size=\"7\" maxlength=\"10\" />
            <select name=\"building_id\">";
        foreach ($buildingNames as $key => $value) {
            echo "<option value=\"" . $key . "\">" . $value . "</option>";
        }
        echo "</select> &nbsp;
            <input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addBuildingToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzufügen\" />
            <input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addAllBuildingToPlanet(xajax.getFormValues('selector')," . count($buildingNames) . ");\" value=\"Alle hinzufügen\" /></td></tr>";

        //Gebäude wählen
        echo "<tr><td class=\"tbldata\" id=\"shipsOnPlanet\" colspan=\"2\">Planet wählen...</td></tr>";
        tableEnd();
        echo "</form>";

        //Focus
        echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').focus();</script>";

        //Add User
        if (searchQueryArray($sa, $so)) {
            if (isset($sa['user_nick'])) {
                echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"" . $sa['user_nick'][1] . "\";xajax_searchUserList('" . $sa['user_nick'][1] . "','showBuildingsOnPlanet');</script>";
            }
        }

        echo '</div><div id="tabs-2">';

        unset($_SESSION['search']['buildings']['query']);

        echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
        echo '<table class="tb">';
        echo "<tr><th class=\"tbltitle\">Planet ID</th><td class=\"tbldata\"><input type=\"text\" name=\"entity_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th class=\"tbltitle\">Planetname</th><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" />&nbsp;";
        echo fieldComparisonSelectBox('planet_name');
        echo "</td></tr>";
        echo "<tr><th class=\"tbltitle\">Spieler ID</th><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th class=\"tbltitle\">Spieler Nick</th><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\" />&nbsp;";
        echo fieldComparisonSelectBox('user_nick');
        echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
        echo "<tr><th class=\"tbltitle\">Gebäude</th><td class=\"tbldata\"><select name=\"building_id\"><option value=\"\"><i>---</i></option>";
        foreach ($buildingNames as $key => $value) {
            echo "<option value=\"" . $key . "\">" . $value . "</option>";
        }
        echo "</select></td></tr>";
        tableEnd();
        echo "<br/><input type=\"submit\" name=\"buildlist_search\" value=\"Suche starten\" />";
        echo "</form>";

        echo '
                </div>
            </div>';

        echo "<p>Es sind <b>" . StringUtils::formatNumber($repository->numBuildingListEntries()) . "</b> Einträge in der Datenbank vorhanden.</p>";
    }
}
