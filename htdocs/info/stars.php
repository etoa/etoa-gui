<?PHP

use EtoA\Universe\Star\SolarTypeRepository;

/** @var SolarTypeRepository */
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
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_metal\">" . RES_METAL . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_crystal\">" . RES_CRYSTAL . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_plastic\">" . RES_PLASTIC . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_fuel\">" . RES_FUEL . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_food\">" . RES_FOOD . "</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_power\">Energie</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_population\">Wachstum</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</th>";
echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_buildtime\">Bauzeit</th>";
echo "</tr>";

$solarTypes = $solarTypeRepository->getSolarTypes($order, $sort);
foreach ($solarTypes as $solarType) {
    echo "<tr><td style=\"width:40px;background:#000;vertical-align:middle;\">
                <img src=\"" . IMAGE_PATH . "/stars/star" . $solarType->id . "_small." . IMAGE_EXT . "\" width=\"40\" height=\"40\" alt=\"Stern\"/></a></td>";

    echo "<td " . tm($solarType->name, $solarType->comment) . "><b>" . $solarType->name . "</b></td>";
    echo "<td>" . get_percent_string($solarType->metal, 1) . "</td>";
    echo "<td>" . get_percent_string($solarType->crystal, 1) . "</td>";
    echo "<td>" . get_percent_string($solarType->plastic, 1) . "</td>";
    echo "<td>" . get_percent_string($solarType->fuel, 1) . "</td>";
    echo "<td>" . get_percent_string($solarType->food, 1) . "</td>";
    echo "<td>" . get_percent_string($solarType->power, 1) . "</td>";
    echo "<td>" . get_percent_string($solarType->people, 1) . "</td>";
    echo "<td>" . get_percent_string($solarType->researchTime, 1, 1) . "</td>";
    echo "<td>" . get_percent_string($solarType->buildTime, 1, 1) . "</td>";
    echo "</tr>";
}
tableEnd();
