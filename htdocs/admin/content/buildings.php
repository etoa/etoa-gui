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
