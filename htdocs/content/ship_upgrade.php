<?PHP

use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;

/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];
/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];
//
// Upgrade menu eines spezial Schiffes
//
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    echo "<h1>Schiffsupgrade-Menu</h1>";

    //
    // Upgrade speichern
    //
    if (
        isset($_POST['submit_upgrade']) && $_POST['submit_upgrade'] != ""
        && intval($_POST['id']) > 0 && $_POST['upgrade'] != ""
        && ctype_alpha($_POST['upgrade']) && checker_verify()
    ) {
        $shipList = $shipRepository->findForUser($cu->getId(), null, [(int) $_GET['id']]);
        if (count($shipList) > 0) {
            $item = $shipList[0];
            switch ($_POST['upgrade']) {
                case 'weapon':
                    $item->specialShipBonusWeapon++;
                    break;
                case 'structure':
                    $item->specialShipBonusStructure++;
                    break;
                case 'shield':
                    $item->specialShipBonusShield++;
                    break;
                case 'heal':
                    $item->specialShipBonusHeal++;
                    break;
                case 'capacity':
                    $item->specialShipBonusCapacity++;
                    break;
                case 'speed':
                    $item->specialShipBonusSpeed++;
                    break;
                case 'pilots':
                    $item->specialShipBonusPilots++;
                    break;
                case 'tarn':
                    $item->specialShipBonusTarn++;
                    break;
                case 'antrax':
                    $item->specialShipBonusAnthrax++;
                    break;
                case 'forsteal':
                    $item->specialShipBonusForSteal++;
                    break;
                case 'build_destroy':
                    $item->specialShipBonusBuildDestroy++;
                    break;
                case 'antrax_food':
                    $item->specialShipBonusAnthraxFood++;
                    break;
                case 'deactivade':
                    $item->specialShipBonusDeactivate++;
                    break;
                case 'readiness':
                    $item->specialShipBonusReadiness++;
                    break;
                default:
                    throw new \RuntimeException('Invalid special ability: ' . $_POST['upgrade']);
            }
            $item->specialShipLevel++;
            $shipRepository->saveItem($item);

            success_msg("Upgrade erfolgreich duchgeführt!");

            $app['dispatcher']->dispatch(new \EtoA\Ship\Event\ShipUpgrade(), \EtoA\Ship\Event\ShipUpgrade::UPGRADE_SUCCESS);
        }
    }


    //Liest alle notwendigen Daten für das Upgradende Schiff aus der DB heraus
    $specialShip = $shipDataRepository->getShip((int) $_GET['id'], false);
    $shipList = $shipRepository->findForUser($cu->getId(), null, [(int) $_GET['id']]);
    if (count($shipList) > 0) {
        $item = $shipList[0];

        $init_level = $item->specialShipLevel;
        $init_exp = $item->specialShipExp;
        $exp = $init_exp;

        $rest_exp = $exp;


        //Errechnet das Level aus den momentanen erfahrungen (exp)
        //Diese Schleife nicht löschen, die hat schon ihren Sinn, auch wenn nichts in der Klammer ist :P
        $level = 0;
        while ($exp >= ceil($specialShip->specialNeedExp * pow($specialShip->specialExpFactor, $level))) {
            $level++;
        }

        //Errechnet die benötigten EXP für das nächste Level
        $exp_for_next_level = ceil($specialShip->specialNeedExp  * pow($specialShip->specialExpFactor, $level));


        echo "<form action=\"?page=$page&amp;id=" . $specialShip->id . "\" method=\"post\">";
        checker_init();

        tableStart($specialShip->name);
        echo "
                 <tr>
                     <th width=\"25%\">Level</th>";

        if ($specialShip->specialMaxLevel <= $init_level && $specialShip->specialMaxLevel !== 0) {
            echo "<td width=\"10%\">" . $init_level . " (max.)</td>";
        } else {
            if ($level - $init_level <= 0) {
                echo "<td width=\"10%\">$init_level (+" . ($level - $init_level) . ")</td>";
            } else {
                echo "<td style=\"color:green;\" width=\"10%\">$init_level (+" . ($level - $init_level) . ")</td>";
            }
        }
        echo "
                     <td  width=\"65%\">Level des Schiffes</td>
                 </tr>
                 <tr>
                     <th width=\"25%\">Erfahrung</th>
                     <td width=\"10%\">" . StringUtils::formatNumber($item->specialShipExp) . "</td>
                     <td width=\"65%\">Erfahrung des Schiffes</td>
                 </tr>
                 <tr>
                     <th width=\"25%\">Ben. Erfahrung</th>";

        if ($specialShip->specialMaxLevel <= $init_level && $specialShip->specialMaxLevel != 0) {
            echo "<td width=\"10%\"> - </td>";
        } else {
            echo "<td width=\"10%\">" . StringUtils::formatNumber($exp_for_next_level) . "</td>";
        }

        echo "<td width=\"65%\">Benötigte Erfahrung bis zum nächsten LevelUp</td>
                 </tr>

                 ";
        tableEnd();

        //Zeigt alle Bonis die das Schiff upgraden kann
        tableStart("Bonis");
        echo "
                 <tr>
                     <th width=\"25%\">Skill</th>
                     <th width=\"10%\">Bonus</th>
                     <th width=\"63%\">Info</th>
                     <th width=\"2%\">LvL</th>
                 </tr>
                 ";


        // Waffentechnik Bonus
        if ($specialShip->specialBonusWeapon > 0) {
            echo "<tr>
                         <th>Waffen (" . $item->specialShipBonusWeapon . ")</th>
                         <td>" . (round($item->specialShipBonusWeapon * $specialShip->specialBonusWeapon * 100, 1)) . "%</td>
                         <td>Waffenbonus im Kampf (" . ($specialShip->specialBonusWeapon * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"weapon\" border=\"0\"></td>
                     </tr>";
        }
        // Struktur Bonus
        if ($specialShip->specialBonusStructure > 0) {
            echo "<tr>
                         <th>Panzerung (" . $item->specialShipBonusStructure . ")</th>
                         <td>" . (round($item->specialShipBonusStructure * $specialShip->specialBonusStructure * 100, 1)) . "%</td>
                         <td>Struktur im Kampf (" . ($specialShip->specialBonusStructure * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"structure\" border=\"0\"></td>
                     </tr>";
        }
        // Schild Bonus
        if ($specialShip->specialBonusShield > 0) {
            echo "<tr>
                         <th>Schild (" . $item->specialShipBonusShield . ")</th>
                         <td>" . (round($item->specialShipBonusShield * $specialShip->specialBonusShield * 100, 1)) . "%</td>
                         <td>Schildbonus im Kampf (" . ($specialShip->specialBonusShield * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"shield\" border=\"0\"></td>
                     </tr>";
        }
        // kapazitäts Bonus
        if ($specialShip->specialBonusCapacity > 0) {
            echo "<tr>
                         <th>Kapazität (" . $item->specialShipBonusCapacity . ")</th>
                         <td>" . (round($item->specialShipBonusCapacity * $specialShip->specialBonusCapacity * 100, 1)) . "%</td>
                         <td>Erhöht die Kapazität der ganzen Flotte (" . ($specialShip->specialBonusCapacity * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"capacity\" border=\"0\"></td>
                     </tr>";
        }
        // Speed Bonus
        if ($specialShip->specialBonusSpeed > 0) {
            echo "<tr>
                         <th>Speed (" . $item->specialShipBonusSpeed . ")</th>
                         <td>" . (round($item->specialShipBonusSpeed * $specialShip->specialBonusSpeed * 100, 1)) . "%</td>
                         <td>Erhöht den Speed der ganzen Flotte (" . ($specialShip->specialBonusSpeed * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"speed\" border=\"0\"></td>
                     </tr>";
        }
        // Tarn Bonus
        if ($specialShip->specialBonusTarn > 0) {
            echo "<tr>
                         <th>Tarnung (" . $item->specialShipBonusTarn . ")</th>
                         <td>" . (round($item->specialShipBonusTarn * $specialShip->specialBonusTarn * 100, 1)) . "%</td>
                         <td>Ermöglicht eine absolute Tarnung der Flotte (" . ($specialShip->specialBonusTarn * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"tarn\" border=\"0\"></td>
                     </tr>";
        }
        // Piloten Bonus
        if ($specialShip->specialBonusPilots > 0) {
            echo "<tr>
                         <th>Besatzung (" . $item->specialShipBonusPilots . ")</th>
                         <td>" . (round($item->specialShipBonusPilots * $specialShip->specialBonusPilots * 100, 1)) . "%</td>
                         <td>Verringert die benötigten Piloten der Flotte (" . ($specialShip->specialBonusPilots * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"pilots\" border=\"0\"></td>
                     </tr>";
        }
        // Heal Bonus
        if ($specialShip->specialBonusHeal > 0) {
            echo "<tr>
                         <th>Heilung (" . $item->specialShipBonusHeal . ")</th>
                         <td>" . (round($item->specialShipBonusHeal * $specialShip->specialBonusHeal * 100, 1)) . "%</td>
                         <td>Heilbonus im Kampf (" . ($specialShip->specialBonusHeal * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"heal\" border=\"0\"></td>
                     </tr>";
        }
        // Giftgas Bonus
        if ($specialShip->specialBonusAntrax > 0) {
            echo "<tr>
                         <th>Giftgas (" . $item->specialShipBonusAnthrax . ")</th>
                         <td>" . (round($item->specialShipBonusAnthrax * $specialShip->specialBonusAntrax * 100, 1)) . "%</td>
                         <td>Erhöht Giftgaseffekt (" . ($specialShip->specialBonusAntrax * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"heal\" border=\"0\"></td>
                     </tr>";
        }
        // Techklau Bonus
        if ($specialShip->specialBonusForsteal > 0) {
            echo "<tr>
                         <th>Spionageangriff (" . $item->specialShipBonusForSteal . ")</th>
                         <td>" . (round($item->specialShipBonusForSteal * $specialShip->specialBonusForsteal * 100, 1)) . "%</td>
                         <td>Erhöht die Erfolgschancen beim Spionageangriff (" . ($specialShip->specialBonusForsteal * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"forsteal\" border=\"0\"></td>
                     </tr>";
        }
        // Bombardieren Bonus
        if ($specialShip->specialBonusBuildDestroy > 0) {
            echo "<tr>
                         <th>Bombardieren (" . $item->specialShipBonusBuildDestroy . ")</th>
                         <td>" . (round($item->specialShipBonusBuildDestroy * $specialShip->specialBonusBuildDestroy * 100, 1)) . "%</td>
                         <td>Erhöht Bombardierungschancen (" . ($specialShip->specialBonusBuildDestroy * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"build_destroy\" border=\"0\"></td>
                     </tr>";
        }
        // Antrax Bonus
        if ($specialShip->specialBonusAntraxFood > 0) {
            echo "<tr>
                         <th>Antrax (" . $item->specialShipBonusAnthraxFood . ")</th>
                         <td>" . (round($item->specialShipBonusAnthraxFood * $specialShip->specialBonusAntraxFood * 100, 1)) . "%</td>
                         <td>Erhöht Antraxeffekt (" . ($specialShip->specialBonusAntraxFood * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"antrax_food\" border=\"0\"></td>
                     </tr>";
        }
        // Deaktivieren Bonus
        if ($specialShip->specialBonusDeactivate > 0) {
            echo "<tr>
                         <th>Deaktivieren (" . $item->specialShipBonusDeactivate . ")</th>
                         <td>" . (round($item->specialShipBonusDeactivate * $specialShip->specialBonusDeactivate * 100, 1)) . "%</td>
                         <td>Erhöht Deaktivierungschancen (" . ($specialShip->specialBonusDeactivate * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"deactivade\" border=\"0\"></td>
                     </tr>";
        }
        // Readyness Bonus
        if ($specialShip->specialBonusReadiness > 0) {
            echo "<tr>
                         <th>Bereitschaft (" . $item->specialShipBonusReadiness . ")</th>
                         <td>" . (round($item->specialShipBonusReadiness * $specialShip->specialBonusReadiness * 100, 1)) . "%</td>
                         <td>Verringert die Start- und Landezeit der ganzen Flotte (" . ($specialShip->specialBonusReadiness * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"readiness\" border=\"0\"></td>
                     </tr>";
        }



        tableEnd();

        //Level Button anzeigen, wenn genügend EXP vorhaden
        if ($level - $init_level > 0 && ($specialShip->specialMaxLevel > $init_level || $specialShip->specialMaxLevel === 0)) {
            echo "<input type=\"hidden\" name=\"id\" value=\"" . $specialShip->id . "\">";
            echo "<input type=\"submit\" class=\"button\" name=\"submit_upgrade\" value=\"Gewähltes Upgrade duchführen\" /><br><br>";
        }
        echo "</form>";


        echo "<input type=\"button\" value=\"Zurück zur Übersicht\" onclick=\"document.location='?page=ship_upgrade'\" />";
    } else {
        error_msg("Du musst dieses Schiff zuerst bauen, oder auf den Planeten wechseln, auf dem sich das Schiff befindet!", 1);
    }
}







//
// Spezial Schiffe Auflisten
//
else {
    echo "<h1>Spezialschiffe</h1>";

    //Listet alle spezial Schiffe auf die der user besitzt
    $specialShips = $shipDataRepository->searchShips(ShipSearch::create()->special(true));
    $shipList = [];
    foreach ($shipRepository->findForUser($cu->getId(), null, array_keys($specialShips)) as $item) {
        $shipList[$item->shipId] = $item;
    }

    if (count($shipList) > 0) {
        foreach ($specialShips as $ship) {
            if (!isset($shipList[$ship->id])) {
                continue;
            }
            $item = $shipList[$ship->id];
            $init_level = $item->specialShipLevel;
            $init_exp = $item->specialShipExp;
            $exp = $init_exp;
            $rest_exp = $exp;

            //Errechnet das Level aus den momentanen erfahrungen (exp)
            //Diese Schleife nicht löschen, die hat schon ihren Sinn, auch wenn nichts in der Klammer ist :P
            $level = 0;
            while ($exp >= ceil($ship->specialNeedExp * pow($ship->specialExpFactor, $level))) {
                $level++;
            }

            //Errechnet die benötigten EXP
            $exp_for_next_level = ceil($ship->specialNeedExp * pow($ship->specialExpFactor, $level));


            tableStart($ship->name);

            echo "
                        <tr>
                            <th style=\"width:220px;\">
                                <a href=\"?page=ship_upgrade&amp;id=" . $ship->id . "\"><img src=\"" . $ship->getImagePath('other') . "\" width=\"220\" height=\"220\" alt=\"Klicke hier um ins Upgrade Menu zu gelangen\" title=\"Klicke hier um ins Upgrade Menu zu gelangen\" border=\"0\"/></a></th>
                            <td colspan=\"3\">" . BBCodeUtils::toHTML($ship->longComment) . "</td>
                        </tr>";
            echo "
                         <tr>
                            <th class=\"tbltitle\">Level</th>";

            if ($ship->specialMaxLevel <= $init_level && $ship->specialMaxLevel !== 0) {
                echo "<td>$init_level (max.)</td>";
            } else {
                if ($level - $init_level <= 0) {
                    echo "<td>$init_level (+" . ($level - $init_level) . ")</td>";
                } else {
                    echo "<td style=\"color:green;\">$init_level (+" . ($level - $init_level) . ")</td>";
                }
            }
            echo "
                            <td>Level des Schiffes</td>
                         </tr>
                         <tr>
                            <th class=\"tbltitle\">Erfahrung</th>
                            <td>" . StringUtils::formatNumber($item->specialShipExp) . "</td>
                            <td>Erfahrung des Schiffes</td>
                         </tr>
                         <tr>
                            <th class=\"tbltitle\">Ben. Erfahrung</th>";

            if ($ship->specialMaxLevel <= $init_level && $ship->specialMaxLevel !== 0)
                echo "<td> - </td>";
            else
                echo "<td>" . StringUtils::formatNumber($exp_for_next_level) . "</td>";

            echo "<td>Benötigte Erfahrung bis zum nächsten LevelUp</td>
                         </tr>

                         ";
            tableEnd();
        }
    } else {
        echo "Du bist noch nicht im Besitz eines Spezialschiffes!<br>";
    }
}
