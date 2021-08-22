<?PHP

declare(strict_types=1);

use EtoA\Defense\DefenseDataRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\StringUtils;

/** @var RaceDataRepository $raceRepository */
$raceRepository = $app[RaceDataRepository::class];

$raceNames = $raceRepository->getRaceNames();
$url = "?$link&amp;site=$site";

/** @var \Symfony\Component\HttpFoundation\Request $request */

if ($request->query->has('id')) {
    $raceId = $request->query->getInt('id');
    $race = $raceRepository->getRace($raceId);

    echo "<h2>Rassen</h2>";

    HelpUtil::breadCrumbs(["Rassen", "races"], [text2html($race->name), $race->id], 1);
    echo "<select onchange=\"document.location='?$link&amp;site=races&id='+this.options[this.selectedIndex].value\">";
    foreach ($raceNames as $id => $raceName) {

        echo "<option value=\"" . $id . "\"";
        if ($id === $race->id) {
            echo " selected=\"selected\"";
        }

        echo ">" . $raceName . "</option>";
    }
    echo "</select><br/><br/>";

    // Info text
    echo text2html($race->comment) . "<br/><br/>";

    // Bonus / Malus
    tableStart('', 300);
    echo "<tr><th colspan=\"2\">St&auml;rken / Schw&auml;chen:</th></tr>";
    if ($race->metal !== 1.0) {
        echo "<tr><th>" . RES_ICON_METAL . "Produktion von " . RES_METAL . ":</td><td>" . StringUtils::formatPercentString($race->metal, true) . "</td></tr>";
    }
    if ($race->crystal !== 1.0) {
        echo "<tr><th>" . RES_ICON_CRYSTAL . "Produktion von " . RES_CRYSTAL . ":</td><td>" . StringUtils::formatPercentString($race->crystal, true) . "</td></tr>";
    }
    if ($race->plastic !== 1.0) {
        echo "<tr><th>" . RES_ICON_PLASTIC . "Produktion von " . RES_PLASTIC . ":</td><td>" . StringUtils::formatPercentString($race->plastic, true) . "</td></tr>";
    }
    if ($race->fuel !== 1.0) {
        echo "<tr><th>" . RES_ICON_FUEL . "Produktion von " . RES_FUEL . ":</td><td>" . StringUtils::formatPercentString($race->fuel, true) . "</td></tr>";
    }
    if ($race->food !== 1.0) {
        echo "<tr><th>" . RES_ICON_FOOD . "Produktion von " . RES_FOOD . ":</td><td>" . StringUtils::formatPercentString($race->food, true) . "</td></tr>";
    }
    if ($race->power !== 1.0) {
        echo "<tr><th>" . RES_ICON_POWER . "Produktion von Energie:</td><td>" . StringUtils::formatPercentString($race->power, true) . "</td></tr>";
    }
    if ($race->population !== 1.0) {
        echo "<tr><th>" . RES_ICON_PEOPLE . "Bevölkerungswachstum:</td><td>" . StringUtils::formatPercentString($race->population, true) . "</td></tr>";
    }
    if ($race->researchTime !== 1.0) {
        echo "<tr><th>" . RES_ICON_TIME . "Forschungszeit:</td><td>" . StringUtils::formatPercentString($race->researchTime, true, true) . "</td></tr>";
    }
    if ($race->buildTime !== 1.0) {
        echo "<tr><th>" . RES_ICON_TIME . "Bauzeit:</td><td>" . StringUtils::formatPercentString($race->buildTime, true, true) . "</td></tr>";
    }
    if ($race->fleetTime !== 1.0) {
        echo "<tr><th>" . RES_ICON_TIME . "Fluggeschwindigkeit:</td><td>" . StringUtils::formatPercentString($race->fleetTime, true) . "</td></tr>";
    }
    tableEnd();

    // Ships

    /** @var ShipDataRepository $shipDataRepository */
    $shipDataRepository = $app[ShipDataRepository::class];

    $ships = $shipDataRepository->getShipsByRace($raceId);
    if (count($ships) > 0) {
        tableStart('', 500);
        echo  "<tr><th colspan=\"3\">Spezielle Schiffe:</th></tr>";
        foreach ($ships as $ship) {
            echo "<tr><td style=\"background:black;\"><img src=\"" . $ship->getImagePath() . "\" style=\"width:40px;height:40px;border:none;\" alt=\"ship" . $ship->id . "\" /></td>
            <th style=\"width:180px;\">" . text2html($ship->name) . "</th>
            <td>" . text2html($ship->shortComment) . "</td></tr>";
        }
        tableEnd();
    }

    // Defenses
    /** @var DefenseDataRepository $defenseDataRepository */
    $defenseDataRepository = $app[DefenseDataRepository::class];
    $defenses = $defenseDataRepository->getDefenseByRace($raceId);
    if (count($defenses) > 0) {
        tableStart('', 500);
        echo  "<tr><th colspan=\"3\">Spezielle Verteidigung:</th></tr>";
        foreach ($defenses as $defense) {
            $s_img = IMAGE_PATH . "/" . IMAGE_DEF_DIR . "/def" . $defense->id . "_small." . IMAGE_EXT;
            echo "<tr><td style=\"background:black;\"><img src=\"" . $s_img . "\" style=\"width:40px;height:40px;border:none;\" alt=\"def" . $defense->id . "\" /></td>
            <th style=\"width:180px;\">" . text2html($defense->name) . "</th>
            <td>" . text2html($defense->shortComment) . "</td></tr>";
        }
        tableEnd();
    }
    echo button("Rassenübersicht", $url) . "&nbsp;&nbsp; ";
} else {

    echo "<h2>Rassen</h2>";

    HelpUtil::breadCrumbs(array("Rassen", "races"));

    //
    //Order
    //
    if (isset($_GET['order']) && ctype_alpha($_GET['order'])) {
        $order = "race_" . $_GET['order'];
        if ($_SESSION['help']['orderfield'] == $_GET['order']) {
            if (($_SESSION['help']['ordersort'] ?? false) === "DESC") {
                $sort = "ASC";
            } else {
                $sort = "DESC";
            }
        } else {
            if ($_GET['order'] === "name") {
                $sort = "ASC";
            } else {
                $sort = "DESC";
            }
        }

        $_SESSION['help']['orderfield'] = $_GET['order'];
        $_SESSION['help']['ordersort'] = $sort;
    } else {
        $order = "race_name";
        $sort = "ASC";
    }

    //
    //Table with a list of all races
    //
    $races = $raceRepository->getActiveRaces($order, $sort);
    tableStart("Kurzinformation");
    echo "<tr>";
    echo "<th>Name</th>";
    echo "<th>Kurzbeschreibug</th>
    </tr>";

    foreach ($races as $race) {
        echo "<tr>";
        echo "<td><a href=\"?$link&amp;site=races&amp;id=" . $race->id . "\">" . $race->name . "</a></td>";
        echo "<td>" . text2html($race->shortComment) . "</td></tr>";
    }
    tableEnd();

    //
    //Bonus-Malus table to compare all the races
    //

    tableStart("Bonus-Malus Vergleichstabelle");
    echo "<tr><th><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_metal\">" . RES_METAL . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_crystal\">" . RES_CRYSTAL . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_plastic\">" . RES_PLASTIC . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_fuel\">" . RES_FUEL . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_food\">" . RES_FOOD . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_power\">Energie</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_population\">Wachstum</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_researchtime\">Forschungszeit</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_buildtime\">Bauzeit</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_fleettime\">Fluggeschwindigkeit</a></th></tr>";

    foreach ($races as $race) {
        echo "<tr><td class=\"tbltitle\">" . $race->name . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->metal, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->crystal, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->plastic, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->fuel, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->food, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->power, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->population, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->researchTime, true, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->buildTime, true, true) . "</td>";
        echo "<td>" . StringUtils::formatPercentString($race->fleetTime, true) . "</td></tr>";
    }
    tableEnd();
}
