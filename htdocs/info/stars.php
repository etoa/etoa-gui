<?PHP

use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Universe\Star\SolarTypeRepository;

/** @var SolarTypeRepository $solarTypeRepository */
$solarTypeRepository = $app[SolarTypeRepository::class];

echo "<h2>Sterne</h2>";
HelpUtil::breadCrumbs(array("Sterne", "stars"));

if (isset($_GET['order']) && ctype_alpha($_GET['order'])) {
    $order = "sol_type_" . $_GET['order'];
    if ($_SESSION['help']['orderfield'] == $_GET['order']) {
        if ($_SESSION['help']['ordersort'] == "DESC")
            $sort = "ASC";
        else
            $sort = "DESC";
    } else {
        if ($_GET['order'] == "name")
            $sort = "ASC";
        else
            $sort = "DESC";
    }
    $_SESSION['help']['orderfield'] = $_GET['order'];
    $_SESSION['help']['ordersort'] = $sort;
} else {
    $order = "sol_type_name";
    $sort = "ASC";
}

tableStart("Sternenboni");
echo "<tr><th colspan=\"2\" ><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_metal\">" . ResourceNames::METAL . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_crystal\">" . ResourceNames::CRYSTAL . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_plastic\">" . ResourceNames::PLASTIC . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_fuel\">" . ResourceNames::FUEL . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_food\">" . ResourceNames::FOOD . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_power\">Energie</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_population\">Wachstum</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_buildtime\">Bauzeit</th>";
echo "</tr>";

$solarTypes = $solarTypeRepository->getSolarTypes($order, $sort);
foreach ($solarTypes as $solarType) {
    echo "<tr><td style=\"width:40px;background:#000;vertical-align:middle;\">
                <img src=\"" . IMAGE_PATH . "/stars/star" . $solarType->id . "_small.png" . "\" width=\"40\" height=\"40\" alt=\"Stern\"/></a></td>";

    echo "<td " . tm($solarType->name, $solarType->comment) . "><b>" . $solarType->name . "</b></td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->metal, true) . "</td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->crystal, true) . "</td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->plastic, true) . "</td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->fuel, true) . "</td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->food, true) . "</td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->power, true) . "</td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->people, true) . "</td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->researchTime, true, true) . "</td>";
    echo "<td>" . StringUtils::formatPercentString($solarType->buildTime, true, true) . "</td>";
    echo "</tr>";
}
tableEnd();
