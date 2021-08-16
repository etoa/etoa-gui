<?PHP

use EtoA\Backend\BackendMessageService;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Building\BuildingSearch;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Technology\TechnologyRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

/** @var BuildingDataRepository $buildingDataRepository */
$buildingDataRepository = $app[BuildingDataRepository::class];

/** @var BackendMessageService $backendMessageService */
$backendMessageService = $app[BackendMessageService::class];
/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];
/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];

if ($cp) {

    $planet = $planetRepo->find($cp->id);

    //
    // Poduktionsrate umstellen
    //
    if (isset($_POST['submitpercent']) && $_POST['submitpercent'] != "") {
        if (count($_POST['buildlist_prod_percent']) > 0) {
            foreach ($_POST['buildlist_prod_percent'] as $id => $val) {
                $val = floatval($val);
                if ($val > 1) $val = 1;
                if ($val < 0) $val = 0;

                $buildingRepository->updateProductionPercent($cu->getId(), $planet->id, $id, $val);
            }
            success_msg("Änderungen gespeichert!");

            // Send
            $backendMessageService->updatePlanet($planet->id);
        }
    }

    echo "<h1>Wirtschaft des Planeten " . $planet->name . "</h1>";
    echo $resourceBoxDrawer->getHTML($planet);

    if (isset($_GET['action']) && $_GET['action'] == "update") {
        $backendMessageService->updatePlanet($planet->id);
        success_msg("Planet wird neu berechnet!");
    }

    //
    // Produktion pro Stunde und Energieverbrauch
    //
    $buildlistMap = [];
    foreach ($buildingRepository->findForUser($cu->getId(), $planet->id) as $item) {
        $buildlistMap[$item->buildingId] = $item;
    }

    $cnt = array(
        "metal" => 0,
        "crystal" => 0,
        "plastic" => 0,
        "fuel" => 0,
        "food" => 0
    );
    $prodIncludingBoni = [];
    $powerUsed = 0;
    echo "<form action=\"?page=$page\" method=\"post\">";
    $producingBuildings = array_intersect_key($buildingDataRepository->searchBuildings(BuildingSearch::create()->withProductionOrPowerUse()), $buildlistMap);
    if (count($producingBuildings) > 0) {
        tableStart("Rohstoffproduktion und Energieverbrauch");
        echo "<tr>
                        <th style=\"width:200px;\">Geb&auml;ude</th>";
        echo "<th class=\"resmetalcolor\">" . RES_METAL . "</th>";
        echo "<th class=\"rescrystalcolor\">" . RES_CRYSTAL . "</th>";
        echo "<th class=\"resplasticcolor\">" . RES_PLASTIC . "</th>";
        echo "<th class=\"resfuelcolor\">" . RES_FUEL . "</th>";
        echo "<th class=\"resfoodcolor\">" . RES_FOOD . "</th>";
        echo "<th class=\"respowercolor\" colspan=\"2\">" . RES_POWER . "</th>";
        echo "</tr>";

        $pwrcnt = 0;

        // array representing bare building production without boni
        // respects prod_percent
        $baseResourceProd = [
            'metal' => 0,
            'crystal' => 0,
            'plastic' => 0,
            'fuel' => 0,
            'food' => 0,
        ];

        $resourceKeys = ['metal', 'crystal', 'plastic', 'fuel', 'food'];

        foreach ($producingBuildings as $building) {
            $buildlist = $buildlistMap[$building->id];
            // Errechnen der Produktion pro Gebäude
            echo "<tr>
                <td>
                    " . $building->name . " (" . $buildlist->currentLevel . ")";
            if ($buildlist->prodPercent == 0) {
                echo "<br/><span style=\"color:red;font-size:8pt;\">Produktion ausgeschaltet!</span>";
            }
            echo "</td>";

            $bareBuildingProduction = [];
            $bareBuildingProduction['metal'] = $prodIncludingBoni['metal'] = $building->prodMetal * pow($building->productionFactor, $buildlist->currentLevel - 1);
            $bareBuildingProduction['crystal'] = $prodIncludingBoni['crystal'] = $building->prodCrystal * pow($building->productionFactor, $buildlist->currentLevel - 1);
            $bareBuildingProduction['plastic'] = $prodIncludingBoni['plastic'] = $building->prodPlastic * pow($building->productionFactor, $buildlist->currentLevel - 1);
            $bareBuildingProduction['fuel'] = $prodIncludingBoni['fuel'] = $building->prodFuel * pow($building->productionFactor, $buildlist->currentLevel - 1);
            $bareBuildingProduction['food'] = $prodIncludingBoni['food'] = $building->prodFood * pow($building->productionFactor, $buildlist->currentLevel - 1);

            // update base resource production, used later for bost calculation.
            foreach ($resourceKeys as $resourceKey) {
                $baseResourceProd[$resourceKey] += $bareBuildingProduction[$resourceKey] * $buildlist->prodPercent;
            }

            // Addieren der Planeten-, Rassen- und Spezialistenboni
            if ($bareBuildingProduction['metal'] != "") {
                $boni = $cp->typeMetal - 1 + $cu->race->metal - 1 + $cp->starMetal - 1 + $cu->specialist->prodMetal - 1;
                $prodIncludingBoni['metal'] += $bareBuildingProduction['metal'] * $boni;
            }
            if ($bareBuildingProduction['crystal'] != "") {
                $boni = $cp->typeCrystal - 1 + $cu->race->crystal - 1 + $cp->starCrystal - 1 + $cu->specialist->prodCrystal - 1;
                $prodIncludingBoni['crystal'] += $bareBuildingProduction['crystal'] * $boni;
            }
            if ($bareBuildingProduction['plastic'] != "") {
                $boni = $cp->typePlastic - 1 + $cu->race->plastic - 1 + $cp->starPlastic - 1 + $cu->specialist->prodPlastic - 1;
                $prodIncludingBoni['plastic'] += $bareBuildingProduction['plastic'] * $boni;
            }
            if ($bareBuildingProduction['fuel'] != "") {
                $boni = $cp->typeFuel - 1 + $cu->race->fuel - 1 + $cp->starFuel - 1 + $cu->specialist->prodFuel - 1  + $planet->getFuelProductionBonusFactor() * -1;
                $prodIncludingBoni['fuel'] += $bareBuildingProduction['fuel'] * $boni;
            }
            if ($bareBuildingProduction['food'] != "") {
                $boni = $cp->typeFood - 1 + $cu->race->food - 1 + $cp->starFood - 1 + $cu->specialist->prodFood - 1;
                $prodIncludingBoni['food'] += $bareBuildingProduction['food'] * $boni;
            }


            foreach ($resourceKeys as $resourceKey) {
                // apply production percent
                $prodIncludingBoni[$resourceKey] *= $buildlist->currentLevel;
                // add to total
                $cnt[$resourceKey] += floor($prodIncludingBoni[$resourceKey]);
            }

            $building_power_use = floor($building->powerUse * pow($building->productionFactor, $buildlist->currentLevel - 1));

            //KälteBonusString
            $fuelBonus = "Kältebonus: ";
            $spw = $planet->fuelProductionBonus();
            if ($spw >= 0) {
                $fuelBonus .= "<span style=\"color:#0f0\">+" . $spw . "%</span>";
            } else {
                $fuelBonus .= "<span style=\"color:#f00\">" . $spw . "%</span>";
            }
            $fuelBonus .= " " . RES_FUEL . "-Produktion";

            // Werte anzeigen
            foreach ($resourceKeys as $resourceKey) {
                echo "<td " . tm("Grundproduktion ohne Boni", nf(floor($bareBuildingProduction[$resourceKey])) . " t/h") . ">" . nf($prodIncludingBoni[$resourceKey], 1) . "</td>";
            }
            // energy
            echo "<td";
            if ($building_power_use > 0) {
                echo " style=\"color:#f00\"";
            }
            echo ">" . nf(ceil($building_power_use * $buildlist->prodPercent)) . "</td>";
            echo "<td>";

            if ($buildlist->buildType == RES_BUILDING_CAT) {
                echo "<select name=\"buildlist_prod_percent[" . $building->id . "]\">\n";
                $prod_percent = $buildlist->prodPercent;
                for ($x = 0; $x < 1; $x += 0.1) {
                    if ($x > 0.9) {
                        $vx = 0;
                    } else {
                        $vx = 1 - $x;
                    }
                    $perc = $vx * 100;
                    echo "<option value=\"" . $vx . "\"";
                    if (doubleval($vx) >= doubleval($prod_percent)) {
                        echo " selected=\"selected\"";
                    }
                    echo ">" . $perc . " %</option>\n";
                }
                echo "</select>";
                echo "&nbsp; <img src=\"misc/progress.image.php?w=50&p=" . ($buildlist->prodPercent * 100) . "\" alt=\"progress\" />";
            } elseif ($building->id == BUILD_MISSILE_ID || $building->id == BUILD_CRYPTO_ID) {
                echo "<select name=\"buildlist_prod_percent[" . $building->id . "]\">\n";
                echo "<option value=\"1\"";
                if ($buildlist->prodPercent == 1) echo " selected=\"selected\"";
                echo ">100 %</option>\n";
                echo "<option value=\"0\"";
                if ($buildlist->prodPercent == 0) echo " selected=\"selected\"";
                echo ">0 %</option>\n";
                echo "</select>";
                echo "&nbsp; <img src=\"misc/progress.image.php?w=50&p=" . ($buildlist->prodPercent * 100) . "\" alt=\"progress\" />";
            } else {
                echo "&nbsp;";
            }
            echo "</td>";
            echo "</tr>";
            $pwrcnt += $building_power_use * $buildlist->prodPercent;
        }
        $pwrcnt = floor($pwrcnt);

        // Boost system
        if ($config->getBoolean('boost_system_enable')) {
            $bonusProd = [];
            foreach ($resourceKeys as $resourceKey) {
                $bonusProd[$resourceKey] = $baseResourceProd[$resourceKey] * $cu->boostBonusProduction;
            }

            echo "<tr><th style=\"height:2px;\" colspan=\"8\"></td></tr>";

            echo "<tr><th>TOTAL Produktion</th>";
            foreach ($resourceKeys as $resourceKey) {
                echo "<td style=\"color:#0f0\"" . tm("Grundproduktion ohne Boni", nf(floor($baseResourceProd[$resourceKey])) . " t/h") . ">" . nf($cnt[$resourceKey]) . "</td>";
            }
            echo "<td style=\"color:#f00\">" . nf($pwrcnt) . "</td>";
            echo "<td></td>";
            echo "</tr>";

            echo "<tr><th>Boost (" . $cu->boostBonusProduction . ")</th>";
            foreach ($resourceKeys as $resourceKey) {
                echo "<td style=\"color:#0f0\">" . nf($bonusProd[$resourceKey]) . "</td>";
            }
            echo "<td style=\"color:#f00\">-</td>";
            echo "<td></td>";
            echo "</tr>";
            foreach ($resourceKeys as $resourceKey) {
                $cnt[$resourceKey] += $bonusProd[$resourceKey];
            }
        }

        // Anzeigen der Gesamtproduktion
        echo "<tr><th style=\"height:2px;\" colspan=\"8\"></td></tr>";

        echo "<tr><th>TOTAL pro Stunde</th>";
        foreach ($resourceKeys as $resourceKey) {
            echo "<td style=\"color:#0f0\">" . nf($cnt[$resourceKey]) . "</td>";
        }
        echo "<td style=\"color:#f00\">" . nf($pwrcnt) . "</td>";
        echo "<td rowspan=\"3\" style=\"color:#f00;vertical-align:middle;\">
                <input type=\"submit\" name=\"submitpercent\" class=\"button\" style=\"font-size:8pt;\" value=\"Speichern\" />
            </td>";
        echo "</tr>";

        echo "<tr><th>TOTAL pro Tag</th>";
        $fact = 24;
        foreach ($resourceKeys as $resourceKey) {
            echo "<td style=\"color:#0f0\">" . nf($fact * $cnt[$resourceKey]) . "</td>";
        }
        echo "<td style=\"color:#f00\">-</td>";
        echo "</tr>";

        $fact = 168;
        echo "<tr><th>TOTAL pro Woche</th>";
        foreach ($resourceKeys as $resourceKey) {
            echo "<td style=\"color:#0f0\">" . nf($fact * $cnt[$resourceKey]) . "</td>";
        }
        echo "<td style=\"color:#f00\">-</td>";
        echo "</tr>";


        $powerUsed = $pwrcnt;

        // Bei zuwenig Strom Warnmessage
        if ($pwrcnt > $planet->prodPower) {
            echo "<tr><td colspan=\"8\" style=\"color:#f00; text-align:center;\">Zuwenig Energie! " . nf(floor($pwrcnt)) . " ben&ouml;tigt, " . nf(floor($planet->prodPower)) . " verf&uuml;gbar. Gesamtproduktion wird auf " . (round($planet->prodPower / $pwrcnt, 3) * 100) . "% gesenkt!</td></tr>";

            foreach ($resourceKeys as $resourceKey) {
                $cnt[$resourceKey] = floor($cnt[$resourceKey] * $planet->prodPower / $pwrcnt);
            }

            echo "<tr><th>TOTAL</th>";
            foreach ($resourceKeys as $resourceKey) {
                echo "<td>" . nf($cnt[$resourceKey]) . "</td>";
            }

            echo "<td colspan=\"2\">" . nf(floor($planet->prodPower)) . "</td>";
            echo "</tr>";
        }
        tableEnd();
    } else {
        error_msg("Es wurden noch keine Produktionsgeb&auml;ude gebaut!");
    }
    echo "</form>";

    echo "<div>
        <input type=\"button\" onclick=\"document.location='?page=specialists'\" value=\"Spezialisten\" /> &nbsp; ";
    echo "<input type=\"button\" onclick=\"document.location='?page=planetstats'\" value=\"Ressourcen aller Planeten anzeigen\" /> &nbsp;
        <input type=\"button\" onclick=\"document.location='?page=economy&action=update'\" value=\"Neu Berechnen\" />
        </div>";

    //
    // Resource Bunker
    //
    $blvl = $buildingRepository->getBuildingLevel($cu->getId(), RES_BUNKER_ID, $planet->id);
    $bunkerBuilding = $buildingDataRepository->getBuilding(RES_BUNKER_ID);
    if ($blvl > 0) {
        iBoxStart("Rohstoffbunker");
        echo "In deinem <b>" . $bunkerBuilding->name . "</b> der Stufe <b>$blvl</b> werden bei einem
            Angriff <b>" . nf($bunkerBuilding->calculateBunkerResources($blvl)) . "</b> Resourcen gesichert!";
        iBoxEnd();
    }


    //
    // Energie
    //
    tableStart("Energieproduktion");
    echo "<tr><th style=\"width:230px;\">Gebäude</th>
        <th colspan=\"3\">" . RES_ICON_POWER . "Energie</th></tr>";

    // Energy technology bonus
    $energyTechPowerBonusFactor = 1;

    /** @var TechnologyRepository $technologyRepository */
    $technologyRepository = $app[TechnologyRepository::class];
    $energyTechLevel = $technologyRepository->getTechnologyLevel($cu->getId(), ENERGY_TECH_ID);

    $energyTechPowerBonusRequiredLevel = $config->getInt('energy_tech_power_bonus_required_level');
    if ($energyTechLevel > $energyTechPowerBonusRequiredLevel) {
        $percentPerLevel = $config->getInt('energy_tech_power_bonus_percent_per_level');
        $percent = $percentPerLevel * ($energyTechLevel - $energyTechPowerBonusRequiredLevel);
        $energyTechPowerBonusFactor = (100 + $percent) / 100;
    }

    // Summarize all bonus factors
    $bonusFactor = 1 + ($cp->typePower + $cu->race->power + $cp->starPower + $cu->specialist->power + $energyTechPowerBonusFactor - 5);

    $cnt['power'] = 0;

    // Power produced by buildings
    $producingProducingBuildings = array_intersect_key($buildingDataRepository->searchBuildings(BuildingSearch::create()->withPowerProduction()), $buildlistMap);
    if (count($producingProducingBuildings) > 0) {
        foreach ($producingProducingBuildings as $building) {
            // Calculate power production
            $buildlist = $buildlistMap[$building->id];
            $prodIncludingBoni['power'] = round($building->prodPower * pow($building->productionFactor, $buildlist->currentLevel - 1));

            // Add bonus
            $prodIncludingBoni['power'] *= $bonusFactor;

            echo "<tr><th>" . $building->name . " (" . $buildlist->currentLevel . ")</th>";
            echo "<td colspan=\"3\">" . nf(floor($prodIncludingBoni['power'])) . "</td></tr>";

            // Zum Total hinzufügen
            $cnt['power'] += $prodIncludingBoni['power'];
        }
    }

    // Power produced by ships
    $shipCounts = $shipRepository->getEntityShipCounts($cu->getId(), $planet->id);
    $ships = array_intersect_key($shipDataRepository->searchShips(ShipSearch::create()->producesPower()), $shipCounts);
    if (count($ships) > 0) {
        $solarProdBonus = $planet->solarPowerBonus();
        $color = $solarProdBonus >= 0 ? '#0f0' : '#f00';
        $solarTempString = "<span style=\"color:" . $color . "\">" . ($solarProdBonus > 0 ? '+' : '') . $solarProdBonus . "</span>";
        foreach ($ships as $ship) {
            $pwr = ($ship->powerProduction + $solarProdBonus);
            $pwr *= $bonusFactor;
            $pwrt = $pwr * $shipCounts[$ship->id];
            echo "<tr><th>" . $ship->name . " (" . nf($shipCounts[$ship->id]) . ")</th>";
            echo "<td colspan=\"3\">" . nf($pwrt) . "
                (Energie pro Satellit: " . (($pwr)) . " = " . $ship->powerProduction . " Basis, " . $solarTempString . " bedingt durch Entfernung zur Sonne, " . get_percent_string($bonusFactor, 1) . " durch Energiebonus)</td>";
            echo "</tr>";
            $cnt['power'] += $pwrt;
        }
    }

    // Totals
    $powerProduced = $cnt['power'];
    echo "<tr><th style=\"height:2px;\" colspan=\"4\"></th></tr>";
    echo "<tr><th>TOTAL produziert</td><td colspan=\"3\">" . nf($powerProduced) . "</th></tr>";
    if ($powerProduced != 0) {
        $powerFree = $powerProduced - $powerUsed;
        echo "<tr><th>Benutzt</td><td";
        echo ">" . nf($powerUsed) . "</td><td>" . round($powerUsed / $powerProduced * 100, 2) . "%</th>
            <td>
            <img src=\"misc/progress.image.php?r=1&w=100&p=" . round($powerUsed / $powerProduced * 100, 2) . "\" alt=\"progress\" />
            </td>
            </tr>";
        if ($powerFree < 0)
            $style = " style=\"color:#f00\"";
        else
            $style = " style=\"color:#0f0\"";
        echo "<tr><th>Verfügbar</td><td $style>
            " . nf($powerFree) . "
            </td>
            <td $style>
            " . round($powerFree / $powerProduced * 100, 2) . "%</td>
            <td>
            <img src=\"misc/progress.image.php?w=100&p=" . round($powerFree / $powerProduced * 100, 2) . "\" alt=\"progress\" />
            </td></tr>";
    }
    tableEnd();

    //
    // Lager
    //
    $storageBuildings = array_intersect_key($buildingDataRepository->searchBuildings(BuildingSearch::create()->storage()), $buildlistMap);
    if (count($storageBuildings) > 0) {
        tableStart("Lagerkapazit&auml;t");
        echo "<tr><th style=\"width:160px\">Geb&auml;ude</th>";
        echo "<th>" . RES_ICON_METAL . "" . RES_METAL . "</th>";
        echo "<th>" . RES_ICON_CRYSTAL . "" . RES_CRYSTAL . "</th>";
        echo "<th>" . RES_ICON_PLASTIC . "" . RES_PLASTIC . "</th>";
        echo "<th>" . RES_ICON_FUEL . "" . RES_FUEL . "</th>";
        echo "<th>" . RES_ICON_FOOD . "" . RES_FOOD . "</th>";
        echo "</tr>";

        echo "<tr><th>Grundkapazit&auml;t</th>";
        $storetotal = [];
        for ($x = 0; $x < 5; $x++) {
            echo "<td>" . nf($config->getInt('def_store_capacity')) . "</td>";
            $storetotal[$x] = $config->getInt('def_store_capacity');
        }
        echo "</tr>";
        foreach ($storageBuildings as $building) {
            $buildlist = $buildlistMap[$building->id];

            echo "<tr><th>" . $building->name . " (" . $buildlist->currentLevel . ")</th>";
            $level = $buildlist->currentLevel - 1;
            $store = [];
            $store[0] = round($building->storeMetal * pow($building->storeFactor, $level));
            $store[1] = round($building->storeCrystal * pow($building->storeFactor, $level));
            $store[2] = round($building->storePlastic * pow($building->storeFactor, $level));
            $store[3] = round($building->storeFuel * pow($building->storeFactor, $level));
            $store[4] = round($building->storeFood * pow($building->storeFactor, $level));
            foreach ($store as $id => $sd) {
                $storetotal[$id] += $sd;
                echo "<td>" . nf($sd) . "</td>";
            }
            echo "</tr>";
        }
        echo "<tr><th style=\"height:2px;\" colspan=\"6\"></th></tr>";
        echo "<tr><th>TOTAL</th>";
        foreach ($storetotal as $id => $sd) {
            echo "<td>" . nf($sd, 1) . "</td>";
        }
        echo "</tr>";
        echo "<tr><th>Benuzt</th>";
        $percent_metal_storage = $planet->storeMetal > 0 ? round($planet->resMetal / $planet->storeMetal * 100) : 0;
        $percent_crystal_storage = $planet->storeCrystal > 0 ? round($planet->resCrystal / $planet->storeCrystal * 100) : 0;
        $percent_plastic_storage = $planet->storePlastic > 0 ? round($planet->resPlastic / $planet->storePlastic * 100) : 0;
        $percent_fuel_storage = $planet->storeFuel > 0 ? round($planet->resFuel / $planet->storeFuel * 100) : 0;
        $percent_food_storage = $planet->storeFood > 0 ? round($planet->resFood / $planet->storeFood * 100) : 0;
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_metal_storage . "\" alt=\"progress\" /></td>";
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_crystal_storage . "\" alt=\"progress\" /></td>";
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_plastic_storage . "\" alt=\"progress\" /></td>";
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_fuel_storage . "\" alt=\"progress\" /></td>";
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_food_storage . "\" alt=\"progress\" /></td>";
        echo "</tr>";


        tableEnd();
    }

    //
    // Boni
    //

    tableStart("Boni");

    echo "<tr><th>Rohstoff</th>
        <th>" . $cp->typeName . "</th>";
    echo "<th>" . $cu->race->name . "</th>";
    echo "<th>" . $cp->starTypeName . "</th>";
    echo "<th>" . $cu->specialist->name . "</th>";
    echo "<th>Technologie</th>";
    echo "<th>TOTAL</th></tr>";

    echo "<tr><td>" . RES_ICON_METAL . "Produktion " . RES_METAL . "</td>";
    echo "<td>" . get_percent_string($cp->typeMetal, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->metal, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starMetal, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->prodMetal, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cp->typeMetal, $cu->race->metal, $cp->starMetal, $cu->specialist->prodMetal), 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_CRYSTAL . "Produktion " . RES_CRYSTAL . "</td>";
    echo "<td>" . get_percent_string($cp->typeCrystal, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->crystal, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starCrystal, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->prodCrystal, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cp->typeCrystal, $cu->race->crystal, $cp->starCrystal, $cu->specialist->prodCrystal), 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_PLASTIC . "Produktion " . RES_PLASTIC . "</td>";
    echo "<td>" . get_percent_string($cp->typePlastic, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->plastic, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starPlastic, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->prodPlastic, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cp->typePlastic, $cu->race->plastic, $cp->starPlastic, $cu->specialist->prodPlastic), 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_FUEL . "Produktion " . RES_FUEL . "</td>";
    echo "<td>" . get_percent_string($cp->typeFuel, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->fuel, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starFuel, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->prodFuel, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cp->typeFuel, $cu->race->fuel, $cp->starFuel, $cu->specialist->prodFuel), 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_FOOD . "Produktion " . RES_FOOD . "</td>";
    echo "<td>" . get_percent_string($cp->typeFood, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->food, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starFood, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->prodFood, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cp->typeFood, $cu->race->food, $cp->starFood, $cu->specialist->prodFood), 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_POWER . "Produktion Energie</td>";
    echo "<td>" . get_percent_string($cp->typePower, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->power, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starPower, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->power, 1) . "</td>";
    echo "<td>" . get_percent_string($energyTechPowerBonusFactor, 1) . "</td>";
    echo "<td>" . get_percent_string(array($cp->typePower, $cu->race->power, $cp->starPower, $cu->specialist->power, $energyTechPowerBonusFactor), 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_PEOPLE . "Bev&ouml;lkerungswachstum</td>";
    echo "<td>" . get_percent_string($cp->typePopulation, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->population, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starPopulation, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->population, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cp->typePopulation, $cu->race->population, $cp->starPopulation, $cu->specialist->population), 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_TIME . "Forschungszeit</td>";
    echo "<td>" . get_percent_string($cp->typeResearchtime, 1, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->researchTime, 1, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starResearchtime, 1, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->researchTime, 1, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cp->typeResearchtime, $cu->race->researchTime, $cp->starResearchtime, $cu->specialist->researchTime), 1, 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_TIME . "Bauzeit (Geb&auml;ude)</td>";
    echo "<td>" . get_percent_string($cp->typeBuildtime, 1, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->race->buildTime, 1, 1) . "</td>";
    echo "<td>" . get_percent_string($cp->starBuildtime, 1, 1) . "</td>";
    echo "<td>" . get_percent_string($cu->specialist->buildTime, 1, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cp->typeBuildtime, $cu->race->buildTime, $cp->starBuildtime, $cu->specialist->buildTime), 1, 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_TIME . "Bauzeit (Schiffe)</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string($cu->specialist->shipTime, 1, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string($cu->specialist->shipTime, 1, 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_TIME . "Bauzeit (Verteidigung)</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string($cu->specialist->defenseTime, 1, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string($cu->specialist->defenseTime, 1, 1) . "</td></tr>";

    echo "<tr><td>" . RES_ICON_TIME . "Fluggeschwindigkeit</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string($cu->race->fleetSpeedFactor, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string($cu->specialist->fleetSpeedFactor, 1) . "</td>";
    echo "<td>-</td>";
    echo "<td>" . get_percent_string(array($cu->race->fleetSpeedFactor, $cu->specialist->fleetSpeedFactor), 1) . "</td></tr>";

    tableEnd();
} else {
    error_msg("Dieser Planet existiert nicht oder er geh&ouml;rt nicht dir!");
}
