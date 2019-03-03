<?PHP
//////////////////////////////////////////////////
//		 	 ____    __           ______       			//
//			/\  _`\ /\ \__       /\  _  \      			//
//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
//																					 		//
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame				 		//
// Ein Massive-Multiplayer-Online-Spiel			 		//
// Programmiert von Nicolas Perrenoud				 		//
// als Maturaarbeit '04 am Gymnasium Oberaargau	//
// www.etoa.ch | mail@etoa.ch								 		//
//////////////////////////////////////////////////
//
//

/**
 * Displays economy information
 *
 * @author MrCage <mrcage@etoa.ch>
 * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
 */

$tabsEnable = false;

if ($cp) {
    //
    // Poduktionsrate umstellen
    //
    if (isset($_POST['submitpercent']) && $_POST['submitpercent'] != "") {
        if (count($_POST['buildlist_prod_percent']) > 0) {
            foreach ($_POST['buildlist_prod_percent'] as $id => $val) {
                $val = floatval($val);
                if ($val > 1) $val = 1;
                if ($val < 0) $val = 0;
                dbquery("
					UPDATE 
						buildlist 
					SET 
						buildlist_prod_percent=$val 
					WHERE 
						buildlist_user_id=" . $cu->id . " 
						AND buildlist_entity_id=" . $cp->id . " 
						AND buildlist_building_id='" . intval($id) . "'
					;");
            }
            success_msg("Änderungen gespeichert!");

            // Send
            BackendMessage::updatePlanet($cp->id);
        }
    }


    echo "<h1>Wirtschaft des Planeten " . $cp->name . "</h1>";
    echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);

    $bl = new BuildList($cp->id, $cp->id);


    if ($tabsEnable) {
        $tc = new TabControl("ecoTab", array("Produktion", "Energie", "Lager", "Bonus/Malus"));
        $tc->open();
    }

    if (isset($_GET['action']) && $_GET['action'] == "update") {
        BackendMessage::updatePlanet($cp->id);
        success_msg("Planet wird neu berechnet!");
    }

    //
    // Produktion pro Stunde und Energieverbrauch
    //

    echo "<form action=\"?page=$page\" method=\"post\">";
    $bres = dbquery("
		SELECT 
			b.building_id,
			b.building_name,
			b.building_type_id,
			b.building_prod_metal,
			b.building_prod_crystal,
			b.building_prod_plastic,
			b.building_prod_fuel,
			b.building_prod_food,
			b.building_power_use,
			b.building_production_factor,
			l.buildlist_current_level,
			l.buildlist_prod_percent
		FROM 
		buildings AS b
		INNER JOIN
    	buildlist AS l
			ON	b.building_id=l.buildlist_building_id
	    AND l.buildlist_user_id=" . $cu->id . "
	    AND l.buildlist_entity_id=" . $cp->id() . "
	    AND l.buildlist_current_level>0
	    AND (b.building_prod_metal>0
	        OR b.building_prod_crystal>0
	        OR b.building_prod_plastic>0
	        OR b.building_prod_fuel>0
	        OR b.building_prod_food>0
	        OR b.building_power_use>0)
		ORDER BY 
			b.building_type_id,
			b.building_order;");
    if (mysql_num_rows($bres) > 0) {
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

        $cnt = array(
            "metal" => 0,
            "crystal" => 0,
            "plastic" => 0,
            "fuel" => 0,
            "food" => 0
        );
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

        while ($barr = mysql_fetch_array($bres)) {
            // Ist das gebäudelevel > 0
            if ($barr['buildlist_current_level'] > 0) {
                // Errechnen der Produktion pro Gebäude
                echo "<tr>
					<td>
						" . $barr['building_name'] . " (" . $barr['buildlist_current_level'] . ")";
                if ($barr['buildlist_prod_percent'] == 0) {
                    echo "<br/><span style=\"color:red;font-size:8pt;\">Produktion ausgeschaltet!</span>";
                }
                echo "</td>";

                $bareBuildingProduction['metal'] = $prodIncludingBoni['metal'] = $barr['building_prod_metal'] * pow($barr['building_production_factor'], $barr['buildlist_current_level'] - 1);
                $bareBuildingProduction['crystal'] = $prodIncludingBoni['crystal'] = $barr['building_prod_crystal'] * pow($barr['building_production_factor'], $barr['buildlist_current_level'] - 1);
                $bareBuildingProduction['plastic'] = $prodIncludingBoni['plastic'] = $barr['building_prod_plastic'] * pow($barr['building_production_factor'], $barr['buildlist_current_level'] - 1);
                $bareBuildingProduction['fuel'] = $prodIncludingBoni['fuel'] = $barr['building_prod_fuel'] * pow($barr['building_production_factor'], $barr['buildlist_current_level'] - 1);
                $bareBuildingProduction['food'] = $prodIncludingBoni['food'] = $barr['building_prod_food'] * pow($barr['building_production_factor'], $barr['buildlist_current_level'] - 1);

                // update base resource production, used later for bost calculation.
                foreach ($resourceKeys as $resourceKey) {
                    $baseResourceProd[$resourceKey] += $bareBuildingProduction[$resourceKey] * $barr['buildlist_prod_percent'];
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
                    $boni = $cp->typeFuel - 1 + $cu->race->fuel - 1 + $cp->starFuel - 1 + $cu->specialist->prodFuel - 1  + $cp->getFuelProductionBonus() * -1;
                    $prodIncludingBoni['fuel'] += $bareBuildingProduction['fuel'] * $boni;
                }
                if ($bareBuildingProduction['food'] != "") {
                    $boni = $cp->typeFood - 1 + $cu->race->food - 1 + $cp->starFood - 1 + $cu->specialist->prodFood - 1;
                    $prodIncludingBoni['food'] += $bareBuildingProduction['food'] * $boni;
                }


                foreach ($resourceKeys as $resourceKey) {
                    // apply production percent
                    $prodIncludingBoni[$resourceKey] *= $barr['buildlist_prod_percent'];
                    // add to total
                    $cnt[$resourceKey] += floor($prodIncludingBoni[$resourceKey]);
                }

                $building_power_use = floor($barr['building_power_use'] * pow($barr['building_production_factor'], $barr['buildlist_current_level'] - 1));

                //KälteBonusString
                $fuelBonus = "Kältebonus: ";
                $spw = $cp->fuelProductionBonus();
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
                echo ">" . nf(ceil($building_power_use * $barr['buildlist_prod_percent'])) . "</td>";
                echo "<td>";

                if ($barr['building_type_id'] == RES_BUILDING_CAT) {
                    echo "<select name=\"buildlist_prod_percent[" . $barr['building_id'] . "]\">\n";
                    $prod_percent = $barr['buildlist_prod_percent'];
                    for ($x = 0; $x < 1; $x += 0.1) {
                        if ($x > 0.9) {
                            $vx = 0;
                        }
                        else {
                            $vx = 1 - $x;
                        }
                        $perc = $vx * 100;
                        echo "<option value=\"" . $vx . "\"";
                        if (doubleval($vx) >= doubleval($prod_percent)){
                            echo " selected=\"selected\"";
                        }
                        echo ">" . $perc . " %</option>\n";
                    }
                    echo "</select>";
                    echo "&nbsp; <img src=\"misc/progress.image.php?w=50&p=" . ($barr['buildlist_prod_percent'] * 100) . "\" alt=\"progress\" />";
                } elseif ($barr['building_id'] == BUILD_MISSILE_ID || $barr['building_id'] == BUILD_CRYPTO_ID) {
                    echo "<select name=\"buildlist_prod_percent[" . $barr['building_id'] . "]\">\n";
                    echo "<option value=\"1\"";
                    if ($barr['buildlist_prod_percent'] == 1) echo " selected=\"selected\"";
                    echo ">100 %</option>\n";
                    echo "<option value=\"0\"";
                    if ($barr['buildlist_prod_percent'] == 0) echo " selected=\"selected\"";
                    echo ">0 %</option>\n";
                    echo "</select>";
                    echo "&nbsp; <img src=\"misc/progress.image.php?w=50&p=" . ($barr['buildlist_prod_percent'] * 100) . "\" alt=\"progress\" />";
                } else {
                    echo "&nbsp;";
                }
                echo "</td>";
                echo "</tr>";
                $pwrcnt += $building_power_use * $barr['buildlist_prod_percent'];
            }
        }
        $pwrcnt = floor($pwrcnt);

        // Boost system
        if ($cfg->value('boost_system_enable') == 1) {
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
        if ($pwrcnt > $cp->prodPower) {
            echo "<tr><td colspan=\"8\" style=\"color:#f00; text-align:center;\">Zuwenig Energie! " . nf(floor($pwrcnt)) . " ben&ouml;tigt, " . nf(floor($cp->prodPower)) . " verf&uuml;gbar. Gesamtproduktion wird auf " . (round($cp->prodPower / $pwrcnt, 3) * 100) . "% gesenkt!</td></tr>";

            foreach ($resourceKeys as $resourceKey) {
                $cnt[$resourceKey] = floor($cnt[$resourceKey] * $cp->prodPower / $pwrcnt);
            }

            echo "<tr><th>TOTAL</th>";
            foreach ($resourceKeys as $resourceKey) {
                echo "<td>" . nf($cnt[$resourceKey]) . "</td>";
            }

            echo "<td colspan=\"2\">" . nf(floor($cp->prodPower)) . "</td>";
            echo "</tr>";
        }
        tableEnd();
    } else {
        error_msg("Es wurden noch keine Produktionsgeb&auml;ude gebaut!");
    }
    echo "</form>";

    if ($tabsEnable) {
        $tc->close();
        $tc->open();
    }

    echo "<div>
		<input type=\"button\" onclick=\"document.location='?page=specialists'\" value=\"Spezialisten\" /> &nbsp; ";
    echo "<input type=\"button\" onclick=\"document.location='?page=planetstats'\" value=\"Ressourcen aller Planeten anzeigen\" /> &nbsp; 
		<input type=\"button\" onclick=\"document.location='?page=economy&action=update'\" value=\"Neu Berechnen\" />
		</div>";

    //
    // Resource Bunker
    //
    $blvl = $bl->getLevel(RES_BUNKER_ID);
    if ($blvl > 0) {
        iBoxStart("Rohstoffbunker");
        echo "In deinem <b>" . $bl->item(RES_BUNKER_ID) . "</b> der Stufe <b>$blvl</b> werden bei einem 
			Angriff <b>" . nf($bl->getBunkerRes()) . "</b> Resourcen gesichert!";
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
    $tl = new TechList($cu->id);
    $energyTechLevel = $tl->getLevel(ENERGY_TECH_ID);
    $energyTechPowerBonusRequiredLevel = $cfg->value('energy_tech_power_bonus_required_level');
    if ($energyTechLevel > $energyTechPowerBonusRequiredLevel) {
        $percentPerLevel = $cfg->value('energy_tech_power_bonus_percent_per_level');
        $percent = $percentPerLevel * ($energyTechLevel - $energyTechPowerBonusRequiredLevel);
        $energyTechPowerBonusFactor = (100 + $percent) / 100;
    }

    // Summarize all bonus factors
    $bonusFactor = 1 + ($cp->typePower + $cu->race->power + $cp->starPower + $cu->specialist->power + $energyTechPowerBonusFactor - 5);

    $cnt['power'] = 0;

    // Power produced by buildings
    $pres = dbquery("
		SELECT 
			b.building_id,
			b.building_name,
			b.building_type_id,
			b.building_prod_power,
			b.building_power_use,
			b.building_production_factor,
			l.buildlist_current_level,
			l.buildlist_prod_percent
		FROM 
      buildings AS b
      INNER JOIN
      buildlist AS l
      ON b.building_id=l.buildlist_building_id
      AND l.buildlist_user_id='" . $cu->id . "'
      AND l.buildlist_entity_id='" . $cp->id . "'
      AND l.buildlist_current_level>'0'
      AND b.building_prod_power>'0'
		ORDER BY 
			b.building_order,
			b.building_name;");
    if (mysql_num_rows($pres) > 0) {
        while ($parr = mysql_fetch_array($pres)) {
            // Calculate power production
            $prodIncludingBoni['power'] = round($parr['building_prod_power'] * pow($parr['building_production_factor'], $parr['buildlist_current_level'] - 1));

            // Add bonus
            $prodIncludingBoni['power'] *= $bonusFactor;

            echo "<tr><th>" . $parr['building_name'] . " (" . $parr['buildlist_current_level'] . ")</th>";
            echo "<td colspan=\"3\">" . nf(floor($prodIncludingBoni['power'])) . "</td></tr>";

            // Zum Total hinzufügen
            $cnt['power'] += $prodIncludingBoni['power'];
        }
    }

    // Power produced by ships
    $sres = dbquery("
		SELECT
			shiplist_count,
			ship_prod_power,
			ship_name
		FROM
			shiplist
		INNER JOIN
			ships
			ON shiplist_ship_id=ship_id
			AND shiplist_entity_id=" . $cp->id . "
			AND shiplist_user_id=" . $cu->id . "
			AND ship_prod_power>0
		");
    if (mysql_num_rows($sres) > 0) {
        $dtemp = $cp->solarPowerBonus();
        if ($dtemp < 0)
            $dtempstr = "<span style=\"color:#f00\">" . $dtemp . "</span>";
        else
            $dtempstr = "<span style=\"color:#0f0\">+" . $dtemp . "</span>";

        while ($sarr = mysql_fetch_array($sres)) {
            $pwr = ($sarr['ship_prod_power'] + $dtemp);
            $pwr *= $bonusFactor;
            $pwrt = $pwr * $sarr['shiplist_count'];
            echo "<tr><th>" . $sarr['ship_name'] . " (" . nf($sarr['shiplist_count']) . ")</th>";
            echo "<td colspan=\"3\">" . nf($pwrt) . " 
				(Energie pro Satellit: " . (($pwr)) . " = " . $sarr['ship_prod_power'] . " Basis, " . $dtempstr . " bedingt durch Entfernung zur Sonne, " . get_percent_string($bonusFactor, 1) . " durch Energiebonus)</td>";
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

    if ($tabsEnable) {
        $tc->close();
        $tc->open();
    }

    //
    // Lager
    //

    $bres = dbquery("
		SELECT
            b.building_name,
            b.building_store_metal,
            b.building_store_crystal,
            b.building_store_plastic,
            b.building_store_fuel,
            b.building_store_food,
            b.building_store_factor,
            l.buildlist_current_level
		FROM
            buildings AS b,
            buildlist AS l
		WHERE
            b.building_id = l.buildlist_building_id
            AND l.buildlist_entity_id=" . $cp->id . "
            AND l.buildlist_current_level>0
            AND 
                (b.building_store_metal>0 
                OR b.building_store_crystal>0 
                OR b.building_store_plastic>0 
                OR b.building_store_fuel>0 
                OR b.building_store_food>0);");
    if (mysql_num_rows($bres) > 0) {
        tableStart("Lagerkapazit&auml;t");
        echo "<tr><th style=\"width:160px\">Geb&auml;ude</th>";
        echo "<th>" . RES_ICON_METAL . "" . RES_METAL . "</th>";
        echo "<th>" . RES_ICON_CRYSTAL . "" . RES_CRYSTAL . "</th>";
        echo "<th>" . RES_ICON_PLASTIC . "" . RES_PLASTIC . "</th>";
        echo "<th>" . RES_ICON_FUEL . "" . RES_FUEL . "</th>";
        echo "<th>" . RES_ICON_FOOD . "" . RES_FOOD . "</th>";
        echo "</tr>";

        echo "<tr><th>Grundkapazit&auml;t</th>";
        for ($x = 0; $x < 5; $x++) {
            echo "<td>" . nf($conf['def_store_capacity']['v']) . "</td>";
            $storetotal[$x] = $conf['def_store_capacity']['v'];
        }
        echo "</tr>";
        while ($barr = mysql_fetch_array($bres)) {
            echo "<tr><th>" . $barr['building_name'] . " (" . $barr['buildlist_current_level'] . ")</th>";
            $level = $barr['buildlist_current_level'] - 1;
            $store[0] = round($barr['building_store_metal'] * pow($barr['building_store_factor'], $level));
            $store[1] = round($barr['building_store_crystal'] * pow($barr['building_store_factor'], $level));
            $store[2] = round($barr['building_store_plastic'] * pow($barr['building_store_factor'], $level));
            $store[3] = round($barr['building_store_fuel'] * pow($barr['building_store_factor'], $level));
            $store[4] = round($barr['building_store_food'] * pow($barr['building_store_factor'], $level));
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
        $percent_metal_storage = $cp->storeMetal > 0 ? round($cp->resMetal / $cp->storeMetal * 100) : 0;
        $percent_crystal_storage = $cp->storeCrystal > 0 ? round($cp->resCrystal / $cp->storeCrystal * 100) : 0;
        $percent_plastic_storage = $cp->storePlastic > 0 ? round($cp->resPlastic / $cp->storePlastic * 100) : 0;
        $percent_fuel_storage = $cp->storeFuel > 0 ? round($cp->resFuel / $cp->storeFuel * 100) : 0;
        $percent_food_storage = $cp->storeFood > 0 ? round($cp->resFood / $cp->storeFood * 100) : 0;
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_metal_storage . "\" alt=\"progress\" /></td>";
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_crystal_storage . "\" alt=\"progress\" /></td>";
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_plastic_storage . "\" alt=\"progress\" /></td>";
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_fuel_storage . "\" alt=\"progress\" /></td>";
        echo "<td><img src=\"misc/progress.image.php?r=1&w=100&p=" . $percent_food_storage . "\" alt=\"progress\" /></td>";
        echo "</tr>";


        tableEnd();
    }

    if ($tabsEnable) {
        $tc->close();
        $tc->open();
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

    if ($tabsEnable) {
        $tc->close();
        $tc->end();
    }
} else
    error_msg("Dieser Planet existiert nicht oder er geh&ouml;rt nicht dir!");

?>

