<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Ship\ShipRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer */
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

            $res = dbquery("
                SELECT
                    shiplist_ship_id,
                    shiplist_bunkered,
                    ship_actions,
                    ship_name,
                    ship_id,
                    ship_shortcomment
                FROM
                    shiplist
                    INNER JOIN
                    ships
                ON
                    ship_id=shiplist_ship_id
                WHERE
                    shiplist_user_id=" . $cu->id . "
                    AND shiplist_entity_id=" . $planet->id . "
                    AND shiplist_bunkered>0
                ;");
            $val = 0;
            $structure = 0;
            $count = 0;
            $jsAllShips = array();    // Array for selectable ships
            while ($arr = mysql_fetch_assoc($res)) {
                if ($ships[$arr['shiplist_ship_id']]->special) {
                    echo "<tr>
                        <td style=\"width:40px;background:#000;\">
                                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $arr['shiplist_ship_id'] . "_small." . IMAGE_EXT . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                        </td>";
                } else {
                    echo "<tr>
                        <td style=\"width:40px;background:#000;\">
                            <a href=\"?page=help&amp;site=shipyard&amp;id=" . $arr['shiplist_ship_id'] . "\">
                                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $arr['shiplist_ship_id'] . "_small." . IMAGE_EXT . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                            </a>
                        </td>";
                }

                $actions = array_filter(explode(",", $arr['ship_actions']));
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

                echo "<td " . tm($arr['ship_name'], "<img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $arr['ship_id'] . "_middle." . IMAGE_EXT . "\" style=\"float:left;margin-right:5px;\">" . text2html($arr['ship_shortcomment']) . "<br/>" . $acstr . "<br style=\"clear:both;\"/>") . ">" . $arr['ship_name'] . "</td>";
                echo "<td width=\"150\">" . nf($ships[$arr['shiplist_ship_id']]->structure) . "</td>";
                echo "<td width=\"110\">" . nf($arr['shiplist_bunkered']) . "<br/>";

                echo "</td>";
                echo "<td width=\"110\"><input type=\"text\"
                    id=\"ship_bunker_count_" . $arr['shiplist_ship_id'] . "\"
                    name=\"ship_bunker_count[" . $arr['shiplist_ship_id'] . "]\"
                    size=\"10\" value=\"$val\"
                    title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\"
                    onclick=\"this.select();\"
                    onkeyup=\"FormatNumber(this.id,this.value," . $arr['shiplist_bunkered'] . ",'','');\"/>
                <br/>
                <a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_" . $arr['shiplist_ship_id'] . "').value=" . $arr['shiplist_bunkered'] . ";document.getElementById('ship_bunker_count_" . $arr['shiplist_ship_id'] . "').select()\">Alle</a> &nbsp;
                <a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_" . $arr['shiplist_ship_id'] . "').value=0;document.getElementById('ship_count_" . $arr['shiplist_ship_id'] . "').select()\">Keine</a></td></tr>";
                $structure += $arr['shiplist_bunkered'] * $ships[$arr['shiplist_ship_id']]->structure;
                $count += $arr['shiplist_bunkered'];
                $jsAllShips["ship_bunker_count_" . $arr['shiplist_ship_id']] = $arr['shiplist_bunkered'];
            }
            echo "<tr><th colspan=\"2\">Benutzt:</th><td>" . nf($structure) . "/" . nf($fleetBunker->calculateBunkerFleetSpace($fleetBunkerLevel)) . "</td><td>" . nf($count) . "/" . nf($fleetBunker->calculateBunkerFleetCount($fleetBunkerLevel)) . "</td><td >";

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

            $res = dbquery("
                SELECT
                    shiplist_ship_id,
                    shiplist_count,
                    ship_actions,
                    ship_name,
                    ship_id,
                    ship_shortcomment
                FROM
                    shiplist
                INNER JOIN
                    ships
                ON
                    ship_id=shiplist_ship_id
                WHERE
                    shiplist_user_id=" . $cu->id . "
                    AND shiplist_entity_id=" . $planet->id . "
                    AND shiplist_count>0
                ;");

            $val = 0;
            $jsAllShips = array();    // Array for selectable ships
            while ($arr = mysql_fetch_assoc($res)) {
                if ($ships[$arr['shiplist_ship_id']]->special) {
                    echo "<tr>
                        <td style=\"width:40px;background:#000;\">
                            <a href=\"?page=ship_upgrade&amp;id=" . $arr['shiplist_ship_id'] . "\">
                                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $arr['shiplist_ship_id'] . "_small." . IMAGE_EXT . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                            </a>
                        </td>";
                } else {
                    echo "<tr>
                        <td style=\"width:40px;background:#000;\">
                            <a href=\"?page=help&amp;site=shipyard&amp;id=" . $arr['shiplist_ship_id'] . "\">
                                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $arr['shiplist_ship_id'] . "_small." . IMAGE_EXT . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                            </a>
                        </td>";
                }

                $actions = array_filter(explode(",", $arr['ship_actions']));
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

                echo "<td " . tm($arr['ship_name'], "<img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $arr['ship_id'] . "_middle." . IMAGE_EXT . "\" style=\"float:left;margin-right:5px;\">" . text2html($arr['ship_shortcomment']) . "<br/>" . $acstr . "<br style=\"clear:both;\"/>") . ">" . $arr['ship_name'] . "</td>";
                echo "<td width=\"150\">" . nf($ships[$arr['shiplist_ship_id']]->structure) . "</td>";
                echo "<td width=\"110\">" . nf($arr['shiplist_count']) . "<br/>";

                echo "</td>";
                echo "<td width=\"110\"><input type=\"text\"
                    id=\"ship_bunker_count_" . $arr['shiplist_ship_id'] . "\"
                    name=\"ship_bunker_count[" . $arr['shiplist_ship_id'] . "]\"
                    size=\"10\" value=\"$val\"
                    title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\"
                    onclick=\"this.select();\"
                    onkeyup=\"FormatNumber(this.id,this.value," . $arr['shiplist_count'] . ",'','');\"/>
                <br/>
                <a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_" . $arr['shiplist_ship_id'] . "').value=" . $arr['shiplist_count'] . ";document.getElementById('ship_bunker_count_" . $arr['shiplist_ship_id'] . "').select()\">Alle</a> &nbsp;
                <a href=\"javascript:;\" onclick=\"document.getElementById('ship_bunker_count_" . $arr['shiplist_ship_id'] . "').value=0;document.getElementById('ship_bunker_count_" . $arr['shiplist_ship_id'] . "').select()\">Keine</a></td></tr>";
                $jsAllShips["ship_bunker_count_" . $arr['shiplist_ship_id']] = $arr['shiplist_count'];
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
            <td><input type=\"text\" id=\"bunker_metal\" name=\"bunker_metal\" value=\"" . nf($planet->bunkerMetal) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">" . RES_ICON_CRYSTAL . "" . RES_CRYSTAL . "</th>
                <td><input type=\"text\" id=\"bunker_crysttal\" name=\"bunker_crystal\" value=\"" . nf($planet->bunkerCrystal) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">" . RES_ICON_PLASTIC . "" . RES_PLASTIC . "</th>
                <td><input type=\"text\" id=\"bunker_plastic\" name=\"bunker_plastic\" value=\"" . nf($planet->bunkerPlastic) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">" . RES_ICON_FUEL . "" . RES_FUEL . "</th>
                <td><input type=\"text\" id=\"bunker_fuel\" name=\"bunker_fuel\" value=\"" . nf($planet->bunkerFuel) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">" . RES_ICON_FOOD . "" . RES_FOOD . "</th>
                <td><input type=\"text\" id=\"bunker_food\" name=\"bunker_food\" value=\"" . nf($planet->bunkerFood) . "\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
            <tr><th style=\"width:150px\">Benutzt:</th>
            <td>" . nf($bunkered) . "/" . nf($resBunker->calculateBunkerResources($resBunkerLevel)) . "</td></tr>
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
