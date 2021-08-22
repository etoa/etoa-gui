<?PHP

use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipCategoryRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Support\StringUtils;

define("RANKING_SHIP_STRUCTURE", 20000);
define("RANKING_SHIP_SHIELD", 25000);
define("RANKING_SHIP_WEAPON", 50000);
define("RANKING_SHIP_SPEED", 5000);
define("RANKING_SHIP_CAPACITY", 100000);
define("RANKING_SHIP_FUEL", 60);

function rankingStars($val, $max2)
{
    $max = $max2;
    $img = "star_r";

    $t = $max / 5;
    $s = "";
    for ($x = 0; $x < 5; $x++) {
        if ($val == 0)
            $s .= "<img src=\"images/star_g.gif\" />";
        elseif ($val > 3 * $max)
            $s .= "<img src=\"images/star_y.gif\" />";
        elseif ($val > $t * $x)
            $s .= "<img src=\"images/" . $img . ".gif\" />";
        else
            $s .= "<img src=\"images/star_g.gif\" />";
    }
    return $s;
}

function shipRanking(\EtoA\Ship\Ship $ship)
{
    ob_start();
    echo "<table class=\"tb\">";
    echo "<tr><th>Struktur:</th><td>" . rankingStars($ship->structure, RANKING_SHIP_STRUCTURE) . "</td></tr>";
    echo "<tr><th>Schilder:</th><td>" . rankingStars($ship->shield, RANKING_SHIP_SHIELD) . "</td></tr>";
    echo "<tr><th>Waffen:</th><td>" . rankingStars($ship->weapon, RANKING_SHIP_WEAPON) . "</td></tr>";
    echo "<tr><th>Speed:</th><td>" . rankingStars($ship->speed, RANKING_SHIP_SPEED) . "</td></tr>";
    echo "<tr><th>Kapazität:</th><td>" . rankingStars($ship->capacity, RANKING_SHIP_CAPACITY) . "</td></tr>";
    echo "<tr><th>Reisekosten:</th><td>" . rankingStars($ship->fuelUse, RANKING_SHIP_FUEL) . "</td></tr>";
    echo "</table>";
    $s = ob_get_contents();
    ob_end_clean();
    return $s;
}

echo "<h2>Raumschiffe</h2>";

/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];

/** @var ShipCategoryRepository $shipCategoryRepository */
$shipCategoryRepository = $app[ShipCategoryRepository::class];

/** @var RaceDataRepository $raceRepository */
$raceRepository = $app[RaceDataRepository::class];
$raceNames = $raceRepository->getRaceNames();

//
// Details
//
if (isset($_GET['id'])) {
    $sid = (int) $_GET['id'];

    $ship = $shipDataRepository->getShip($sid);
    if ($ship !== null) {
        $shipCategory = $shipCategoryRepository->getCategory($ship->catId);
        HelpUtil::breadCrumbs(array("Schiffe", "shipyard"), array(text2html($ship->name), $ship->id), 1);
        echo "<select onchange=\"document.location='?page=help&site=shipyard&id='+this.options[this.selectedIndex].value\">";
        $shipNames = $shipDataRepository->getShipNames();
        foreach ($shipNames as $shipId => $shipName) {
            echo "<option value=\"" . $shipId . "\"";
            if ($shipId === $ship->id) echo " selected=\"selected\"";
            echo ">" . $shipName . "</option>";
        }
        echo "</select><br/><br/>";

        tableStart($ship->name);

        echo "<tr>
            <td class=\"tbltitle\" style=\"width:220px;background:#000\">
                <img src=\"" . IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $ship->id . "." . IMAGE_EXT . "\" width=\"220\" height=\"220\" alt=\"Schiff\" />
            </td>
            <td class=\"tbldata\" colspan=\"3\">
                " . text2html($ship->longComment) . "
            </td>
        </tr>";

        if ($ship->raceId > 0) {
            echo "<tr><th class=\"tbltitle\">Rasse:</th><td colspan=\"3\" class=\"tbldata\">Dieses Schiff kann exklusiv nur durch das Volk der <b>" . $raceNames[$ship->raceId] . "</b> gebaut werden!</td></tr>";
        }

        echo "<tr>
            <th class=\"tbltitle\">Bewertung:
            </th><td class=\"tbldata\" colspan=\"3\">
                " . shipRanking($ship) . "
            </td>
        </tr>";

        echo "<tr><th class=\"tbltitle\">Kategorie:</th><td class=\"tbldata\" colspan=\"3\">" . $shipCategory->name . "</td></tr>";
        echo "<tr><th class=\"tbltitle\">Anzahl Piloten:</th><td class=\"tbldata\" colspan=\"3\">" . nf($ship->pilots) . "</td></tr>";

        echo "<tr><td colspan=\"4\" style=\"height:30px;\"></td></tr>";

        echo "<tr><th class=\"tbltitle\" colspan=\"2\" style=\"text-align:center\">Kosten</th>
                    <th class=\"tbltitle\" colspan=\"2\" style=\"text-align:center\">Technische Daten</th></tr>";

        echo "<tr>
            <td style=\"padding:0px;\">
            <table class=\"tb\">";
        echo "<tr>
                        <td style=\"width:170px;font-weight:bold;\" class=\"resmetal\">" . RES_METAL . "</td>
                        <td style=\"width:350px\">" . nf($ship->costsMetal) . " t</td>
                </tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\" class=\"rescrystal\">" . RES_CRYSTAL . "</td>
                        <td style=\"width:350px\">" . nf($ship->costsCrystal) . " t</td>
                </tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\" class=\"resplastic\">" . RES_PLASTIC . "</td>
                        <td style=\"width:350px\">" . nf($ship->costsPlastic) . " t</td>
                </tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\" class=\"resfuel\">" . RES_FUEL . "</td>
                        <td style=\"width:350px\">" . nf($ship->costsFuel) . " t</td>
                </tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\" class=\"resfood\">" . RES_FOOD . "</td>
                        <td style=\"width:350px\">" . nf($ship->costsFood) . " t</td>
                </tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\" class=\"resfuel\">/100 AE</td>
                        <td style=\"width:350px\">" . nf($ship->fuelUse) . " t</td>
                </tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\" class=\"resfuel\">Start</td>
                        <td style=\"width:350px\">" . nf($ship->fuelUseLaunch) . " t</td>
                </tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\" class=\"resfuel\">Landung</td>
                        <td style=\"width:350px\">" . nf($ship->fuelUseLanding) . " t</td>
                </tr>";

        echo "</table>
            </td>
            <td  colspan=\"3\" style=\"padding:0px;\">
                <table class=\"tb\" style=\"width:100%\">";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Struktur</td>
                        <td>" . nf($ship->structure) . "</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Schutzschild</td>
                        <td>" . nf($ship->shield) . "</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Waffen</td>
                        <td>" . nf($ship->weapon) . "</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Heilung</td>
                        <td>" . nf($ship->heal) . "</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Antriebstechnologie</td>
                        <td>";

        /** @var ShipRequirementRepository $shipRequirementRepository */
        $shipRequirementRepository = $app[ShipRequirementRepository::class];
        $technologies = $shipRequirementRepository->getRequiredSpeedTechnologies($ship->id);
        foreach ($technologies as $technology) {
            echo "<a href=\"?page=help&amp;site=research&amp;id=" . $technology->id . "\">" . $technology->name . "</a> (Stufe " . $technology->requiredLevel . ")<br/>";
        }

        echo "</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Laderaum</td>
                        <td>" . nf($ship->capacity) . " t</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Passagierraum</td>
                        <td>" . nf($ship->peopleCapacity) . "</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Geschwindigkeit</td>
                        <td>" . nf($ship->speed / FLEET_FACTOR_F) . " AE/h</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Startdauer</td>
                        <td>" . StringUtils::formatTimespan($ship->timeToStart / FLEET_FACTOR_S) . "</td></tr>";
        echo "<tr>
                        <td style=\"font-weight:bold;\">Landedauer</td>
                        <td>" . StringUtils::formatTimespan($ship->timeToLand / FLEET_FACTOR_L) . "</td></tr>";
        echo "</table>
            </td></tr>";




        echo "<tr><td colspan=\"4\" style=\"height:30px;\"></td></tr>";

        echo "<tr><th class=\"tbltitle\" colspan=\"4\" style=\"text-align:center\">Fähigkeiten</th></tr>";

        $actions = array_filter(explode(",", $ship->actions));
        $accnt = 0;
        if (count($actions) > 0) {
            echo "<tr><td colspan=\"4\" style=\"padding:0px\">
                <table class=\"tb\" style=\"width:100%\">";
            foreach ($actions as $i) {
                if ($ac = FleetAction::createFactory($i)) {
                    echo "<tr>
                            <td class=\"tbldata\" style=\"width:150px;\">" . $ac . "</td>
                            <td class=\"tbldata\">" . $ac->desc() . "</td>
                            <td class=\"tbldata\" style=\"width:150px;\" ><a href=\"?page=help&site=action&action=" . $i . "\">Details</a></td></tr>";
                    $accnt++;
                }
            }
            echo "</table>";
            echo "</td></tr>";
        }
        if ($accnt == 0)
            echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center\">Keine Spezialfähigkeit vorhanden!</td></tr>";

        echo "<tr><td colspan=\"4\" style=\"height:30px;\"></td></tr>";


        echo "<tr><th class=\"tbltitle\" colspan=\"4\" style=\"text-align:center\">Voraussetzungen</th></tr>";
        echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center\">";
        echo "<div id=\"reqInfo\" style=\"width:100%;\">
            <br/><div class=\"loadingMsg\">Bitte warten...</div>
            </div>";
        echo '<script type="text/javascript">xajax_reqInfo(' . $ship->id . ',"s")</script>';
        echo "</td></tr>";

        tableEnd();
    } else
        echo "Schiffdaten nicht gefunden!<br><br>";

    echo "<input type=\"button\" value=\"Schiff&uuml;bersicht\" onclick=\"document.location='?$link&site=$site'\" /> &nbsp; ";
    echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=ships'\" /> &nbsp; ";
    if ($_SESSION['lastpage'] == "haven")
        echo "<input type=\"button\" value=\"Zur&uuml;ck zum Hafen\" onclick=\"document.location='?page=haven'\" /> &nbsp; ";
    if ($_SESSION['lastpage'] == "shipyard")
        echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumschiffwerft\" onclick=\"document.location='?page=shipyard'\" /> &nbsp; ";
}

//
// Übersicht
//
else {
    HelpUtil::breadCrumbs(array("Schiffe", "shipyard"));

    if (isset($_GET['order'])) {
        if (ctype_alpha($_GET['order'])) {
            $order = "ship_" . $_GET['order'];
        } else if ($_GET['order'] === "race_id") {
            $order = "race_name";
        } else {
            $order = "ship_order";
        }
        if ($_SESSION['help']['orderfield'] == $_GET['order']) {
            if ($_SESSION['help']['ordersort'] == "DESC")
                $sort = "ASC";
            else
                $sort = "DESC";
        } else {
            if (($_GET['order'] === "name") || ($_GET['order'] === "race_id"))
                $sort = "ASC";
            else
                $sort = "DESC";
        }
        $_SESSION['help']['orderfield'] = $_GET['order'];
        $_SESSION['help']['ordersort'] = $sort;
    } else {
        $order = "ship_order";
        $sort = "ASC";
    }

    $shipCategories = $shipCategoryRepository->getAllCategories();
    foreach ($shipCategories as $shipCategory) {
        $ships = $shipDataRepository->getShipsByCategory($shipCategory->id, $order, $sort);
        if (count($ships) > 0) {
            tableStart($shipCategory->name);
            echo "<tr><th colspan=\"2\"><a href=\"?$link&amp;site=$site&amp;order=name\">Name</a></th>";
            echo "<th><a href=\"?$link&amp;site=$site&amp;order=race_id\">Rasse</a></th>";
            echo "<th><a href=\"?$link&amp;site=$site&amp;order=capacity\">Kapazität</a></th>";
            echo "<th><a href=\"?$link&amp;site=$site&amp;order=speed\">Speed</a></th>";
            echo "<th><a href=\"?$link&amp;site=$site&amp;order=fuel_use\">Treibstoff</a></th>";
            echo "<th><a href=\"?$link&amp;site=$site&amp;order=weapon\">Waffen</a></th>";
            echo "<th><a href=\"?$link&amp;site=$site&amp;order=structure\">Struktur</a></th>";
            echo "<th><a href=\"?$link&amp;site=$site&amp;order=shield\">Schild</a></th>";
            echo "<th><a href=\"?$link&amp;site=$site&amp;order=pilots\">Piloten</a></th>
                <th><a href=\"?$link&amp;site=$site&amp;order=points\">Wert</a></th>
                </tr>";

            foreach ($ships as $ship) {
                $s_img = IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $ship->id . "_small." . IMAGE_EXT;
                echo "<tr><td style=\"background:#000;width:40px;\">
                    <a href=\"?$link&site=$site&id=" . $ship->id . "\">
                    <img src=\"$s_img\" alt=\"Schiffbild\" width=\"40\" height=\"40\" border=\"0\"/></a></td>";
                echo "<td " . tm($ship->name, text2html($ship->shortComment) . "<br/><br/>" . shipRanking($ship)) . ">
                        <a href=\"?$link&site=$site&id=" . $ship->id . "\">" . $ship->name . "</a>
                    </td>";
                echo "<td>";
                echo $ship->raceId > 0 ? $raceNames[$ship->raceId] : '-';
                echo "<td>" . nf($ship->capacity) . "</td>";
                echo "<td>" . nf($ship->speed) . "</td>";
                echo "<td>" . nf($ship->fuelUse) . "</td>";
                echo "<td>" . nf($ship->weapon) . "</td>";
                echo "<td>" . nf($ship->structure) . "</td>";
                echo "<td>" . nf($ship->shield) . "</td>";
                echo "<td>" . nf($ship->pilots) . "</td>";
                echo "<td>" . nf($ship->points) . "</td>
                    </tr>";
            }
            tableEnd();
        }
    }
}
