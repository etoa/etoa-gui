<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\StringUtils;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];
/** @var BuildingDataRepository $buildingDataRepository */
$buildingDataRepository = $app[BuildingDataRepository::class];
/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

if ($cp) {
    $planet = $planetRepo->find($cp->id);

    echo "<h1>Bunker des Planeten " . $planet->name . "</h1>";

    echo $resourceBoxDrawer->getHTML($planet);

    $tabitems = array(
        "res" => "Rohstoffbunker",
        "bunker" => "Flottenbunker",
        "fleet" => "Schiffe einbunkern",
    );
    show_tab_menu("mode", $tabitems);

    $mode = (isset($_GET['mode']) && ctype_alsc($_GET['mode'])) ? $_GET['mode'] : "res";

    $fleetBunkerLevel = $buildingRepository->getBuildingLevel($cu->getId(), FLEET_BUNKER_ID, $planet->id);
    $resBunkerLevel = $buildingRepository->getBuildingLevel($cu->getId(), RES_BUNKER_ID, $planet->id);
    $fleetBunker = $buildingDataRepository->getBuilding(FLEET_BUNKER_ID);
    $resBunker = $buildingDataRepository->getBuilding(RES_BUNKER_ID);

    $ships = [];
    if ($mode == "fleet" || $mode == "bunker") {
        /** @var \EtoA\Ship\ShipDataRepository $shipDataRepository */
        $shipDataRepository = $app[\EtoA\Ship\ShipDataRepository::class];
        foreach ($shipDataRepository->getAllShips(true) as $ship) {
            $ships[$ship->id] = $ship;
        }
    }


    if ($mode == "bunker") {
        if ($fleetBunkerLevel > 0) {
            if (isset($_POST['submit_bunker_fleet']) && checker_verify()) {
                $count = 0;
                foreach ($_POST['ship_bunker_count'] as $shipId => $cnt) {
                    $cnt = nf_back($cnt);
                    if ($cnt > 0) {
                        $cnt = $shipRepository->leaveBunker($cu->getId(), $planet->id, $shipId, $cnt);
                        $count += $cnt;
                    }
                }
                if ($count > 0) {
                    echo "<br />";
                    success_msg("Schiffe wurden ausgebunkert!");
                }
            }

            echo "<form action=\"?page=$page&amp;mode=bunker\" method=\"post\">";
            checker_init();
            tableStart("Flottenbunker");
            echo "<tr>
                    <th colspan=\"5\">Schiffe wählen:</th>
                </tr>
                <tr>
                    <th colspan=\"2\">Typ</th>
                    <th width=\"150\">Struktur</th>
                    <th width=\"110\">Eingebunkert</th>
                    <th width=\"110\">Ausbunkern</th>
                </tr>";

            $bunkered = $shipRepository->getBunkeredCount($cu->getId(), $planet->id);
            $val = 0;
            $structure = 0;
            $count = 0;
            $jsAllShips = array();    // Array for selectable ships
            foreach ($bunkered as $shipId => $bunkeredCount) {
                if ($ships[$shipId]->special) {
                    echo "<tr>
                        <td style=\"width:40px;background:#000;\">
                                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipId . "_small." . IMAGE_EXT . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                        </td>";
                } else {
                    echo "<tr>
                        <td style=\"width:40px;background:#000;\">
                            <a href=\"?page=help&amp;site=shipyard&amp;id=" . $shipId . "\">
                                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipId . "_small." . IMAGE_EXT . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                            </a>
                        </td>";
                }

                $actions = array_filter(explode(",", $ships[$shipId]->actions));
                $accnt = count($actions);
                $acstr = '';
                if ($accnt > 0) {
                    $acstr = "<br/><b>Fähigkeiten:</b> ";
                    $x = 0;
                    foreach ($actions as $i) {
                        if ($ac = FleetAction::createFactory($i)) {
                            $acstr .= $ac;
                            if ($x < $accnt - 1)
                                $acstr .= ", ";
                        }
                        $x++;
                    }
                    $acstr .= "";
                }

                echo "<td " . tm($ships[$shipId]->name, "<img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipId . "_middle." . IMAGE_EXT . "\" style=\"float:left;margin-right:5px;\">" . text2html($ships[$shipId]->shortComment) . "<br/>" . $acstr . "<br style=\"clear:both;\"/>") . ">" . $ships[$shipId]->name . "</td>";
                echo "<td width=\"150\">" . StringUtils::formatNumber($ships[$shipId]->structure) . "</td>";
                echo "<td width=\"110\">" . StringUtils::formatNumber($bunkeredCount) . "<br/>";

                echo "</td>";
                echo "<td width=\"110\"><input type=\"text\"
                    id=\"ship_bunker_count_" . $shipId . "\"
                    name=\"ship_bunker_count[" . $shipId . "]\"
                    size=\"10\" value=\"$val\"
                    title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\"
                    onclick=\"this.select();\"
                    onkeyup=\"FormatNumber(this.id,this.value," . $bunkeredCount . ",'','');\"/>
                <br/>
                <a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_" . $shipId . "').value=" . $bunkeredCount . ";document.getElementById('ship_bunker_count_" . $shipId . "').select()\">Alle</a> &nbsp;
                <a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_" . $shipId . "').value=0;document.getElementById('ship_count_" . $shipId . "').select()\">Keine</a></td></tr>";
                $structure += $bunkeredCount * $ships[$shipId]->structure;
                $count += $bunkeredCount;
                $jsAllShips["ship_bunker_count_" . $shipId] = $bunkeredCount;
            }
            echo "<tr><th colspan=\"2\">Benutzt:</th><td>" . StringUtils::formatNumber($structure) . "/" . StringUtils::formatNumber($fleetBunker->calculateBunkerFleetSpace($fleetBunkerLevel)) . "</td><td>" . StringUtils::formatNumber($count) . "/" . StringUtils::formatNumber($fleetBunker->calculateBunkerFleetCount($fleetBunkerLevel)) . "</td><td >";

            // Select all ships button
            echo "<a href=\"javascript:;\" onclick=\"";
            foreach ($jsAllShips as $k => $v) {
                echo "document.getElementById('" . $k . "').value=" . $v . ";";
            }
            echo "\">Alle wählen</a>";
            echo "</td></tr>
            <tr><th colspan=\"2\">Verfügbar:</th><td><img src=\"misc/progress.image.php?r=1&w=100&p=" . round($structure / $fleetBunker->calculateBunkerFleetSpace($fleetBunkerLevel) * 100) . "\" alt=\"progress\" /></td>
            <td><img src=\"misc/progress.image.php?r=1&w=100&p=" . round($count / $fleetBunker->calculateBunkerFleetCount($fleetBunkerLevel) * 100) . "\" alt=\"progress\" /></td><td></td></tr>";
            tableEnd();
            echo "<input type=\"submit\" name=\"submit_bunker_fleet\" value=\"Ausbunkern\" />";
            echo "</form>";
        } else {
            echo "<br />";
            info_msg("Der Flottenbunker wurde noch nicht gebaut!");
        }
    } elseif ($mode == "fleet") {
        if ($fleetBunkerLevel > 0) {
            if (isset($_POST['submit_bunker_fleet']) && checker_verify()) {

                $count = $fleetBunker->calculateBunkerFleetCount($fleetBunkerLevel);
                $structure = $fleetBunker->calculateBunkerFleetSpace($fleetBunkerLevel);
                $countBunker = 0;
                $spaceBunker = 0;
                $counter = 0;

                $bunkeredShips = $shipRepository->getBunkeredCount($cu->getId(), $planet->id);
                foreach ($bunkeredShips as $shipId => $shipCount) {
                    $count -= $shipCount;
                    $structure -= $shipCount * $ships[$shipId]->structure;
                }

                foreach ($_POST['ship_bunker_count'] as $shipId => $cnt) {
                    $cnt = nf_back($cnt);
                    if ($cnt > 0) {
                        $countBunker = min($count, $cnt);
                        $spaceBunker = $ships[$shipId]->structure > 0 ? min($cnt, $structure / $ships[$shipId]->structure) : $cnt;
                        $cnt = (int) floor(min($countBunker, $spaceBunker));
                        $cnt = $shipRepository->bunker($cu->getId(), $planet->id, $shipId, $cnt);
                        $count -= $cnt;
                        $structure -= $cnt * $ships[$shipId]->structure;
                        $counter += $cnt;
                    }
                }
                if ($counter > 0) {
                    echo "<br />";
                    success_msg("Schiffe wurden eingebunkert!");
                } else {
                    echo "<br />";
                    error_msg("Schiffe konnten nicht eingebunkert werden, da kein Platz mehr vorhanden war!");
                }
            }

            echo "<form action=\"?page=$page&amp;mode=fleet\" method=\"post\">";
            checker_init();
            tableStart("Vorhandene Raumschiffe");
            echo "<tr>
                    <th colspan=\"5\">Schiffe wählen:</th>
                </tr>
                <tr>
                    <th colspan=\"2\">Typ</th>
                    <th width=\"150\">Struktur</th>
                    <th width=\"110\">Anzahl</th>
                    <th width=\"110\">Einbunkern</th>
                </tr>";

            $shipCounts = $shipRepository->getEntityShipCounts($cu->getId(), $planet->id);
            $val = 0;
            $jsAllShips = array();    // Array for selectable ships
            foreach ($shipCounts as $shipId => $shipCount) {
                if ($ships[$shipId]->special) {
                    echo "<tr>
                        <td style=\"width:40px;background:#000;\">
                            <a href=\"?page=ship_upgrade&amp;id=" . $shipId . "\">
                                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipId . "_small." . IMAGE_EXT . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                            </a>
                        </td>";
                } else {
                    echo "<tr>
                        <td style=\"width:40px;background:#000;\">
                            <a href=\"?page=help&amp;site=shipyard&amp;id=" . $shipId . "\">
                                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipId . "_small." . IMAGE_EXT . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                            </a>
                        </td>";
                }

                $actions = array_filter(explode(",", $ships[$shipId]->actions));
                $accnt = count($actions);
                $acstr = '';
                if ($accnt > 0) {
                    $acstr = "<br/><b>Fähigkeiten:</b> ";
                    $x = 0;
                    foreach ($actions as $i) {
                        if ($ac = FleetAction::createFactory($i)) {
                            $acstr .= $ac;
                            if ($x < $accnt - 1)
                                $acstr .= ", ";
                        }
                        $x++;
                    }
                    $acstr .= "";
                }

                echo "<td " . tm($ships[$shipId]->name, "<img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipId . "_middle." . IMAGE_EXT . "\" style=\"float:left;margin-right:5px;\">" . text2html($ships[$shipId]->shortComment) . "<br/>" . $acstr . "<br style=\"clear:both;\"/>") . ">" . $ships[$shipId]->name . "</td>";
                echo "<td width=\"150\">" . StringUtils::formatNumber($ships[$shipId]->structure) . "</td>";
                echo "<td width=\"110\">" . StringUtils::formatNumber($shipCount) . "<br/>";

                echo "</td>";
                echo "<td width=\"110\"><input type=\"text\"
                    id=\"ship_bunker_count_" . $shipId . "\"
                    name=\"ship_bunker_count[" . $shipId . "]\"
                    size=\"10\" value=\"$val\"
                    title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\"
                    onclick=\"this.select();\"
                    onkeyup=\"FormatNumber(this.id,this.value," . $shipCount . ",'','');\"/>
                <br/>
                <a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_" . $shipId . "').value=" . $shipCount . ";document.getElementById('ship_bunker_count_" . $shipId . "').select()\">Alle</a> &nbsp;
                <a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_" . $shipId . "').value=0;document.getElementById('ship_bunker_count_" . $shipId . "').select()\">Keine</a></td></tr>";
                $jsAllShips["ship_bunker_count_" . $shipId] = $shipCount;
            }
            echo "<tr><td colspan=\"3\"><td><td >";

            // Select all ships button
            echo "<a href=\"javascript:;\" onclick=\"";
            foreach ($jsAllShips as $k => $v) {
                echo "document.getElementById('" . $k . "').value=" . $v . ";";
            }
            echo "\">Alle wählen</a>";
            echo "</td></tr>";
            tableEnd();
            echo "<input type=\"submit\" name=\"submit_bunker_fleet\" value=\"Einbunkern\" />";
            echo "</form>";
        } else {
            echo "<br />";
            info_msg("Der Flottenbunker wurde noch nicht gebaut!");
        }
    } else {
        if ($resBunkerLevel > 0) {
            if (isset($_POST['submit_bunker_res']) && checker_verify()) {
                $sum = nf_back($_POST['bunker_metal']) + nf_back($_POST['bunker_crystal']) + nf_back($_POST['bunker_plastic']) + nf_back($_POST['bunker_fuel']) + nf_back($_POST['bunker_food']);
                $percent = $sum / $resBunker->calculateBunkerResources($resBunkerLevel);
                if ($percent < 1) $percent = 1;

                $planetRepo->updateBunker(
                    $planet->id,
                    nf_back($_POST['bunker_metal']) / $percent,
                    nf_back($_POST['bunker_crystal']) / $percent,
                    nf_back($_POST['bunker_plastic']) / $percent,
                    nf_back($_POST['bunker_fuel']) / $percent,
                    nf_back($_POST['bunker_food']) / $percent
                );
                $planet = $planetRepo->find($cp->id);

                echo "<br />";
                success_msg("Änderungen wurden übernommen!");
            }

            //
            // Rohstoffbunker
            //
            $bunkered = $planet->bunkerMetal + $planet->bunkerCrystal + $planet->bunkerPlastic + $planet->bunkerFuel + $planet->bunkerFood;
            echo "<form action=\"?page=$page\" method=\"post\">";
            checker_init();
            tableStart("Rohstoffbunker", 400);
            echo "
            <tr><th style=\"width:150px\">" . RES_ICON_METAL . "" . RES_METAL . "</th>
            <td><input type=\"text\" id=\"bunker_metal\" name=\"bunker_metal\" value=\"" . StringUtils::formatNumber($planet->bunkerMetal) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">" . RES_ICON_CRYSTAL . "" . RES_CRYSTAL . "</th>
                <td><input type=\"text\" id=\"bunker_crysttal\" name=\"bunker_crystal\" value=\"" . StringUtils::formatNumber($planet->bunkerCrystal) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">" . RES_ICON_PLASTIC . "" . RES_PLASTIC . "</th>
                <td><input type=\"text\" id=\"bunker_plastic\" name=\"bunker_plastic\" value=\"" . StringUtils::formatNumber($planet->bunkerPlastic) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">" . RES_ICON_FUEL . "" . RES_FUEL . "</th>
                <td><input type=\"text\" id=\"bunker_fuel\" name=\"bunker_fuel\" value=\"" . StringUtils::formatNumber($planet->bunkerFuel) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">" . RES_ICON_FOOD . "" . RES_FOOD . "</th>
                <td><input type=\"text\" id=\"bunker_food\" name=\"bunker_food\" value=\"" . StringUtils::formatNumber($planet->bunkerFood) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">Benutzt:</th>
            <td>" . StringUtils::formatNumber($bunkered) . "/" . StringUtils::formatNumber($resBunker->calculateBunkerResources($resBunkerLevel)) . "</td></tr>
            <tr><th>Verfügbar:</th><td><img src=\"misc/progress.image.php?r=1&w=100&p=" . round($bunkered / $resBunker->calculateBunkerResources($resBunkerLevel) * 100) . "\" alt=\"progress\" /></td></tr>";
            tableEnd();
            echo "<input type=\"submit\" name=\"submit_bunker_res\" value=\"Speichern\" />";
            echo "</form>";
        } else {
            echo "<br />";
            info_msg("Der Rohstoffbunker wurde noch nicht gebaut!");
        }
    }
}
