<?PHP

use EtoA\Defense\DefenseCategoryRepository;
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

/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];

echo "<h2>Verteidigung</h2>";

if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $did = (int) $_GET['id'];
    $defense = $defenseDataRepository->getDefense($did);
    if (null !== $defense) {
        HelpUtil::breadCrumbs(array("Verteidigung", "defense"), array($defense->name, $defense->id), 1);
        echo "<select onchange=\"document.location='?$link&amp;site=defense&id='+this.options[this.selectedIndex].value\">";
        $defenseNames = $defenseDataRepository->getDefenseNames();
        foreach ($defenseNames as $defenseId => $defenseName) {
            echo "<option value=\"" . $defenseId . "\"";
            if ($defenseId === $defense->id) echo " selected=\"selected\"";
            echo ">" . $defenseName . "</option>";
        }
        echo "</select><br/><br/>";

        tableStart($defense->name);
        echo "<tr><td width=\"220\" class=\"tbltitle\"><img src=\"" . IMAGE_PATH . "/" . IMAGE_DEF_DIR . "/def" . $defense->id . "." . IMAGE_EXT . "\" width=\"220\" height=\"220\" alt=\"Verteidigung\" /></td>";
        echo "<td class=\"tbldata\">" . BBCodeUtils::toHTML($defense->longComment) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Rasse</td><td class=\"tbldata\">";
        echo $defense->raceId > 0 ? $raceNames[$defense->raceId] . "</td></tr>" : "-</td></tr>";
        echo "<tr><td class=\"tbltitle\">" . ResIcons::METAL . "" . ResourceNames::METAL . "</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->costsMetal) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">" . ResIcons::CRYSTAL . "" . ResourceNames::CRYSTAL . "</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->costsCrystal) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">" . ResIcons::PLASTIC . "" . ResourceNames::PLASTIC . "</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->costsPlastic) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">" . ResIcons::FUEL . "" . ResourceNames::FUEL . "</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->costsFuel) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">" . ResIcons::FOOD . "" . ResourceNames::FOOD . "</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->costsFood) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Struktur</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->structure) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Abwehrschild</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->shield) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Schusskraft</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->weapon) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Reparatur</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->heal) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Platzverbrauch</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->fields) . " Felder</td></tr>";
        echo "<tr><td class=\"tbltitle\">Max. Anzahl</td><td class=\"tbldata\">" . StringUtils::formatNumber($defense->maxCount) . "</td></tr>";
        tableEnd();

        /** @var ShipDataRepository $shipDataRepository */
        $shipDataRepository = $app[ShipDataRepository::class];

        $ship = $shipDataRepository->getTransformedShipForDefense($defense->id);
        if ($ship !== null) {
            iBoxStart("Transformation");
            echo "Diese Verteidigungsanlage lässt sich auf ein Schiff verladen:<br/><br/>";
            echo "<a href=\"?$link&amp;site=shipyard&amp;id=" . $ship->id . "\">" . $ship->name . "</a>";
            iBoxEnd();
        }

        iBoxStart("Technikbaum");
        showTechTree("d", $defense->id);
        iBoxEnd();
    } else
        echo "Verteidigungsdaten nicht gefunden!";
    echo "<input type=\"button\" value=\"Verteidigungs&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
    echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=defense'\" /> &nbsp; ";
    if ($_SESSION['lastpage'] == "defense")
        echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Anlagen\" onclick=\"document.location='?page=defense'\" /> &nbsp; ";
} else {
    HelpUtil::breadCrumbs(array("Verteidigung", "defense"));

    if (isset($_GET['order']) && ctype_alpha($_GET['order'])) {
        $order = "def_" . $_GET['order'];
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
        $order = 'def_order';
        $sort = "ASC";
    }

    /** @var DefenseCategoryRepository $defenseCategoryRepository */
    $defenseCategoryRepository = $app[DefenseCategoryRepository::class];
    $defenseCategories = $defenseCategoryRepository->getAllCategories();
    foreach ($defenseCategories as $defenseCategory) {
        $defenses = $defenseDataRepository->getDefenseByCategory($defenseCategory->id);
        if (count($defenses) > 0) {
            tableStart($defenseCategory->name);

            echo "<tr>
                        <th colspan=\"2\"><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></th>
                        <th><a href=\"?$link&amp;site=$site&amp;order=race_id\">Rasse</a></th>
                        <th><a href=\"?$link&amp;site=$site&amp;order=fields\">Felder</a></th>
                        <th><a href=\"?$link&amp;site=$site&amp;order=weapon\">Waffen</a></th>
                        <th><a href=\"?$link&amp;site=$site&amp;order=structure\">Struktur</a></th>
                        <th><a href=\"?$link&amp;site=$site&amp;order=shield\">Schild</a></th>
                        <th><a href=\"?$link&amp;site=$site&amp;order=heal\">Reparatur</a></th>
                        <th><a href=\"?$link&amp;site=$site&amp;order=points\">Wert</a></th>
                    </tr>";
            foreach ($defenses as $defense) {
                $s_img = IMAGE_PATH . "/" . IMAGE_DEF_DIR . "/def" . $defense->id . "_small." . IMAGE_EXT;
                echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000;\">
                        <a href=\"?$link&site=$site&id=" . $defense->id . "\"><img src=\"$s_img\" alt=\"Verteidigung\" width=\"40\" height=\"40\" border=\"0\"/></a></td>";
                echo "<td>
                            <a href=\"?$link&site=$site&id=" . $defense->id . "\">" . $defense->name . "</a></td>";
                echo "<td>";
                if ($defense->raceId > 0)
                    echo $raceNames[$defense->raceId];
                else
                    echo "-";
                echo "<td>" . StringUtils::formatNumber($defense->fields) . "</td>";
                echo "<td>" . StringUtils::formatNumber($defense->weapon) . "</td>";
                echo "<td>" . StringUtils::formatNumber($defense->structure) . "</td>";
                echo "<td>" . StringUtils::formatNumber($defense->shield) . "</td>";
                echo "<td>" . StringUtils::formatNumber($defense->heal) . "</td>";
                echo "<td>" . StringUtils::formatNumber($defense->points) . "</td></tr>";
            }
            tableEnd();
        }
    }
}
