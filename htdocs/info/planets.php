<?PHP

use EtoA\Support\StringUtils;
use EtoA\UI\Tooltip;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Resources\ResourceNames;

/** @var PlanetTypeRepository $planetTypeRepository */
$planetTypeRepository = $app[PlanetTypeRepository::class];

echo "<h2>Planeten</h2>";
HelpUtil::breadCrumbs(array("Planeten", "planets"));

if (isset($_GET['order']) && ctype_alpha($_GET['order'])) {
    $order = "type_" . $_GET['order'];
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
    $order = "type_name";
    $sort = "ASC";
}

tableStart("Planetenboni");
echo "<tr><td class=\"tbltitle\" colspan=\"2\"><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_metal\">" . ResourceNames::METAL . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_crystal\">" . ResourceNames::CRYSTAL . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_plastic\">" . ResourceNames::PLASTIC . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_fuel\">" . ResourceNames::FUEL . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_food\">" . ResourceNames::FOOD . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_power\">Energie</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_population\">Wachstum</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_buildtime\">Bauzeit</td>";
echo "</tr>";

$planetTypes = $planetTypeRepository->getPlanetTypes($order, $sort);
foreach ($planetTypes as $planetType) {
    $x = mt_rand(1, 5);

    echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000;\">";

    $tt = new Tooltip();
    $tt->addImage(IMAGE_PATH . "/" . IMAGE_PLANET_DIR . "/planet" . $planetType->id . "_" . $x . ".gif");
    echo "<img src=\"" . IMAGE_PATH . "/" . IMAGE_PLANET_DIR . "/planet" . $planetType->id . "_" . $x . "_small.gif\" width=\"40\" height=\"40\" alt=\"planet\" border=\"0\" / " . $tt->toString() . "></td>";

    $tt = new Tooltip();
    $tt->addIcon(IMAGE_PATH . "/" . IMAGE_PLANET_DIR . "/planet" . $planetType->id . "_" . $x . "_small.gif");
    $tt->addTitle($planetType->name);
    if ($planetType->habitable)
        $tt->addGoodCond("Bewohnbar");
    else
        $tt->addBadCond("Unbewohnbar");
    if ($planetType->collectGas)
        $tt->addGoodCond("Ermöglich " . ResourceNames::FUEL . "abbau");
    $tt->addComment($planetType->comment);
    echo "<td class=\"tbltitle\" " . $tt->toString() . ">" . $planetType->name . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->metal, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->crystal, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->plastic, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->fuel, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->food, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->power, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->people, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->researchTime, true, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->buildTime, true, true) . "</td>";
    echo "</tr>";
}
tableEnd();
