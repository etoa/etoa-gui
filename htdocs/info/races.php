<?PHP

declare(strict_types=1);

use EtoA\Defense\DefenseDataRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResIcons;
use EtoA\Universe\Resources\ResourceNames;

/** @var RaceDataRepository $raceRepository */
$raceRepository = $app[RaceDataRepository::class];

$raceNames = $raceRepository->getRaceNames();
$url = "?$link&amp;site=$site";

/** @var \Symfony\Component\HttpFoundation\Request $request */

if ($request->query->has('id')) {
    $raceId = $request->query->getInt('id');
    $race = $raceRepository->getRace($raceId);

    echo "<h2>Rassen</h2>";

    HelpUtil::breadCrumbs(["Rassen", "races"], [$race->name, $race->id], 1);
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
    echo BBCodeUtils::toHTML($race->comment) . "<br/><br/>";

    // Bonus / Malus
    tableStart('', 300);
    echo "<tr><th colspan=\"2\">St&auml;rken / Schw&auml;chen:</th></tr>";
    if ($race->metal !== 1.0) {
        echo "<tr><th>" . ResIcons::METAL . "Produktion von " . ResourceNames::METAL . ":</td><td>" . StringUtils::formatPercentString($race->metal, true) . "</td></tr>";
    }
    if ($race->crystal !== 1.0) {
        echo "<tr><th>" . ResIcons::CRYSTAL . "Produktion von " . ResourceNames::CRYSTAL . ":</td><td>" . StringUtils::formatPercentString($race->crystal, true) . "</td></tr>";
    }
    if ($race->plastic !== 1.0) {
        echo "<tr><th>" . ResIcons::PLASTIC . "Produktion von " . ResourceNames::PLASTIC . ":</td><td>" . StringUtils::formatPercentString($race->plastic, true) . "</td></tr>";
    }
    if ($race->fuel !== 1.0) {
        echo "<tr><th>" . ResIcons::FUEL . "Produktion von " . ResourceNames::FUEL . ":</td><td>" . StringUtils::formatPercentString($race->fuel, true) . "</td></tr>";
    }
    if ($race->food !== 1.0) {
        echo "<tr><th>" . ResIcons::FOOD . "Produktion von " . ResourceNames::FOOD . ":</td><td>" . StringUtils::formatPercentString($race->food, true) . "</td></tr>";
    }
    if ($race->power !== 1.0) {
        echo "<tr><th>" . ResIcons::POWER . "Produktion von Energie:</td><td>" . StringUtils::formatPercentString($race->power, true) . "</td></tr>";
    }
    if ($race->population !== 1.0) {
        echo "<tr><th>" . ResIcons::PEOPLE . "Bevölkerungswachstum:</td><td>" . StringUtils::formatPercentString($race->population, true) . "</td></tr>";
    }
    if ($race->researchTime !== 1.0) {
        echo "<tr><th>" . ResIcons::TIME . "Forschungszeit:</td><td>" . StringUtils::formatPercentString($race->researchTime, true, true) . "</td></tr>";
    }
    if ($race->buildTime !== 1.0) {
        echo "<tr><th>" . ResIcons::TIME . "Bauzeit:</td><td>" . StringUtils::formatPercentString($race->buildTime, true, true) . "</td></tr>";
    }
    if ($race->fleetTime !== 1.0) {
        echo "<tr><th>" . ResIcons::TIME . "Fluggeschwindigkeit:</td><td>" . StringUtils::formatPercentString($race->fleetTime, true) . "</td></tr>";
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
            <th style=\"width:180px;\">" . $ship->name . "</th>
            <td>" . BBCodeUtils::toHTML($ship->shortComment) . "</td></tr>";
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
            $s_img = $defense->getImagePath('small');
            echo "<tr><td style=\"background:black;\"><img src=\"" . $s_img . "\" style=\"width:40px;height:40px;border:none;\" alt=\"def" . $defense->id . "\" /></td>
            <th style=\"width:180px;\">" . $defense->name . "</th>
            <td>" . BBCodeUtils::toHTML($defense->shortComment) . "</td></tr>";
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
        echo "<td>" . BBCodeUtils::toHTML($race->shortComment) . "</td></tr>";
    }
    tableEnd();

    //
    //Bonus-Malus table to compare all the races
    //

    tableStart("Bonus-Malus Vergleichstabelle");
    echo "<tr><th><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_metal\">" . ResourceNames::METAL . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_crystal\">" . ResourceNames::CRYSTAL . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_plastic\">" . ResourceNames::PLASTIC . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_fuel\">" . ResourceNames::FUEL . "</a></th>";
    echo "<th><a href=\"?$link&amp;site=$site&amp;order=f_food\">" . ResourceNames::FOOD . "</a></th>";
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
