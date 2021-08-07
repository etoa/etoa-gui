<?PHP

use EtoA\UI\Tooltip;
use EtoA\Universe\Planet\PlanetTypeRepository;

/** @var PlanetTypeRepository */
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
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_metal\">" . RES_METAL . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_crystal\">" . RES_CRYSTAL . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_plastic\">" . RES_PLASTIC . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_fuel\">" . RES_FUEL . "</td>";
echo "<td class=\"tbltitle\"><a href=\"?$link&amp;site=$site&amp;order=f_food\">" . RES_FOOD . "</td>";
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
        $tt->addGoodCond("Ermöglich " . RES_FUEL . "abbau");
    $tt->addComment($planetType->comment);
    echo "<td class=\"tbltitle\" " . $tt->toString() . ">" . $planetType->name . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->metal, 1) . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->crystal, 1) . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->plastic, 1) . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->fuel, 1) . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->food, 1) . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->power, 1) . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->people, 1) . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->researchTime, 1, 1) . "</td>";
    echo "<td class=\"tbldata\">" . get_percent_string($planetType->buildTime, 1, 1) . "</td>";
    echo "</tr>";
}
tableEnd();
