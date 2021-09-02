<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Building\BuildingTypeDataRepository;
use EtoA\Building\BuildingTypeId;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var \Symfony\Component\HttpFoundation\Request $request */

define("SHOWLEVELS", $site != null ? 30 : 5);

echo "<h2>Geb&auml;ude</h2>";

if ($request->query->has('id') && $request->query->getInt('id') > 0) {
    $currentBuildingId = $request->query->getInt('id');

    $b_level = 1;

    /** @var BuildingDataRepository $buildingDataRepository */
    $buildingDataRepository = $app[BuildingDataRepository::class];

    $building = $buildingDataRepository->getBuilding($currentBuildingId);
    if ($building !== null) {
        HelpUtil::breadCrumbs(["Geb&auml;ude", "buildings"], [$building->name, $building->id], 1);
        echo "<select onchange=\"document.location='?$link&amp;site=buildings&id='+this.options[this.selectedIndex].value\">";
        $buildingNames = $buildingDataRepository->getBuildingNames();
        foreach ($buildingNames as $buildingId => $buildingName) {
            echo "<option value=\"" . $buildingId . "\"";
            if ($buildingId === $building->id) {
                echo " selected=\"selected\"";
            }

            echo ">" . $buildingName . "</option>";
        }

        echo "</select><br/><br/>";

        $currentLevel = 0;
        if (isset($cu) && isset($cp)) {
            /** @var BuildingRepository $buildingRepository */
            $buildingRepository = $app[BuildingRepository::class];
            $currentLevel = $buildingRepository->getBuildingLevel((int) $cu->id, $currentBuildingId, (int) $cp->id);
        }

        tableStart($building->name);
        echo "<tr>
            <th style=\"width:220px;background:#000;padding:0px;\" rowspan=\"2\">
                <img src=\"" . IMAGE_PATH . "/" . IMAGE_BUILDING_DIR . "/building" . $building->id . "." . IMAGE_EXT . "\" style=\"width:220px;height:220px;background:#000;margin:0px;\" align=\"top\" alt=\"Bild " . $building->name . "\" />
            </th>
            <td colspan=\"2\">
                <div align=\"justify\">" . BBCodeUtils::toHTML($building->longComment) . "</div>
            </td>
        </tr>
        <tr>
            <th style=\"height:20px;width:120px;\">Maximale Stufe:</th>
            <td style=\"height:20px;\">" . $building->lastLevel . "</td>
        </tr>";
        tableEnd();

        // Metallmine
        if ($building->id === 1) {
            tableStart("Produktion von " . RES_METAL . " (ohne Boni)");
            echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = round($building->prodMetal * pow($building->productionFactor, $level - 1));
                $power_use = round($building->powerUse * pow($building->productionFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td><td class=\"tbldata2\">" . StringUtils::formatNumber($power_use) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td><td>" . StringUtils::formatNumber($power_use) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Siliziummine
        elseif ($building->id === 2) {
            tableStart("Produktion von " . RES_CRYSTAL . " (ohne Boni)");
            echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = round($building->prodCrystal * pow($building->productionFactor, $level - 1));
                $power_use = round($building->powerUse * pow($building->productionFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td><td class=\"tbldata2\">" . StringUtils::formatNumber($power_use) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td><td>" . StringUtils::formatNumber($power_use) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Chemiefabrik
        elseif ($building->id === 3) {
            tableStart("Produktion von " . RES_PLASTIC . " (ohne Boni)");
            echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = round($building->prodPlastic * pow($building->productionFactor, $level - 1));
                $power_use = round($building->powerUse * pow($building->productionFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td><td class=\"tbldata2\">" . StringUtils::formatNumber($power_use) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td><td>" . StringUtils::formatNumber($power_use) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Tritiumsynthetizer
        elseif ($building->id === 4) {
            tableStart("Produktion von " . RES_FUEL . " (ohne Boni)");
            echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = round($building->prodFuel * pow($building->productionFactor, $level - 1));
                $power_use = round($building->powerUse * pow($building->productionFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td><td class=\"tbldata2\">" . StringUtils::formatNumber($power_use) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td><td>" . StringUtils::formatNumber($power_use) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Gewächshaus
        elseif ($building->id === 5) {
            tableStart("Produktion von " . RES_FOOD . " (ohne Boni)");
            echo "<tr><th>Stufe</th><th>Produktion</th><th>Energie</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = round($building->prodFood * pow($building->productionFactor, $level - 1));
                $power_use = round($building->powerUse * pow($building->productionFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td><td class=\"tbldata2\">" . StringUtils::formatNumber($power_use) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td><td>" . StringUtils::formatNumber($power_use) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Planetenbasis
        elseif ($building->id === 6) {
            tableStart("Produktion (ohne Boni)");
            echo "<tr><th>Rohstoff</th><th>Prod.</th><th>Lager</th></tr>";
            echo "<tr><td>" . RES_METAL . "</td><td>" . StringUtils::formatNumber($building->prodMetal) . "</td><td>" . StringUtils::formatNumber($building->storeMetal) . "</td></tr>";
            echo "<tr><td>" . RES_CRYSTAL . "</td><td>" . StringUtils::formatNumber($building->prodCrystal) . "</td><td>" . StringUtils::formatNumber($building->storeCrystal) . "</td></tr>";
            echo "<tr><td>" . RES_PLASTIC . "</td><td>" . StringUtils::formatNumber($building->prodPlastic) . "</td><td>" . StringUtils::formatNumber($building->storePlastic) . "</td></tr>";
            echo "<tr><td>" . RES_FUEL . "</td><td>" . StringUtils::formatNumber($building->prodFuel) . "</td><td>" . StringUtils::formatNumber($building->storeFuel) . "</td></tr>";
            echo "<tr><td>" . RES_FOOD . "</td><td>" . StringUtils::formatNumber($building->prodFood) . "</td><td>" . StringUtils::formatNumber($building->storeFood) . "</td></tr>";
            echo "<tr><td>Bewohner</td><td>-</td><td> " . StringUtils::formatNumber($building->peoplePlace) . " Plätze</td></tr>";
            echo "<tr><td>Energie</td><td>" . StringUtils::formatNumber($building->prodPower) . "</td><td>-</td></tr>";
            tableEnd();
        }

        // Wohnmodul
        elseif ($building->id === 7) {
            $basePeoplePlace = $buildingDataRepository->getBuilding(6)->peoplePlace;
            echo "Beachte das es einen Grundwohnraum für <b>" . StringUtils::formatNumber($config->param1Int('user_start_people')) . "</b> Menschen pro Planet gibt. Ebenfalls bietet die
                <a href=\"?$link&amp;site=buildings&amp;id=6\">Planetenbasis</a> Platz für <b>" . $basePeoplePlace . "</b> Menschen.<br/>";

            tableStart("Platz f&uuml;r Bewohner");
            echo "<tr>
                <th>Stufe</th>
                <th>Wohnplatz</th>
                <th>Wohnplatz mit Grundbonus und Planetenbasis</th>
                </tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = round($building->peoplePlace * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td>
                         <td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td>
                         <td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item + $basePeoplePlace + $config->param1Int('user_start_people')) . "</td>
                         </tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td>
                          <td>" . StringUtils::formatNumber($prod_item + $basePeoplePlace + $config->param1Int('user_start_people')) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Windkraftwerk
        // Solarkaftwerk
        // Fusionskraftwerk
        // Gezeitenkraftwerk
        elseif (in_array($building->id, [12, 13, 14, 15], true)) {
            tableStart("Energieproduktion (ohne Boni)");
            echo "<tr><th>Stufe</th><th>Produktion</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = round($building->prodPower * pow($building->productionFactor, $level - 1));
                $power_use = round($building->powerUse * pow($building->productionFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Titanspeicher
        elseif ($building->id === 16) {
            $baseStoreMetal = $buildingDataRepository->getBuilding(6)->storeMetal;
            tableStart("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (" . StringUtils::formatNumber($baseStoreMetal) . ") und Standardkapazit&auml;t (" . StringUtils::formatNumber($config->getInt("def_store_capacity")) . ") des Planeten)");
            echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = $config->getInt("def_store_capacity") + $baseStoreMetal + round($building->storeMetal * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Siliziumspeicher
        elseif ($building->id === 17) {
            $baseStoreCrystal = $buildingDataRepository->getBuilding(6)->storeCrystal;
            tableStart("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (" . StringUtils::formatNumber($baseStoreCrystal) . ") und Standardkapazit&auml;t (" . StringUtils::formatNumber($config->getInt("def_store_capacity")) . ") des Planeten)");
            echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = $config->getInt("def_store_capacity") + $baseStoreCrystal + round($building->storeCrystal * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Lagerhalle
        elseif ($building->id === 18) {
            $baseStorePlastic = $buildingDataRepository->getBuilding(6)->storePlastic;
            tableStart("Kapazit&auml;t inklusive Planetenbasiskapazit&auml;t (" . StringUtils::formatNumber($baseStorePlastic) . ") und Standardkapazit&auml;t (" . StringUtils::formatNumber($config->getInt("def_store_capacity")) . ")");
            echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = $config->getInt("def_store_capacity") + $baseStorePlastic + round($building->storePlastic * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Nahrungssilos
        elseif ($building->id === 19) {
            $baseStoreFood = $buildingDataRepository->getBuilding(6)->storeFood;
            tableStart("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (" . StringUtils::formatNumber($baseStoreFood) . ") und Standardkapazit&auml;t (" . StringUtils::formatNumber($config->getInt("def_store_capacity")) . ") des Planeten)");
            echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = $config->getInt("def_store_capacity") + $baseStoreFood + round($building->storeFood * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Tritiumsilo
        elseif ($building->id === 20) {
            $baseStoreFuel = $buildingDataRepository->getBuilding(6)->storeFuel;
            tableStart("Lagerkapazit&auml;t (inklusive Planetenbasiskapazit&auml;t (" . StringUtils::formatNumber($baseStoreFuel) . ") und Standardkapazit&auml;t (" . StringUtils::formatNumber($config->getInt("def_store_capacity")) . ") des Planeten)");
            echo "<tr><th>Stufe</th><th>Kapazit&auml;t</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = $config->getInt("def_store_capacity") + $baseStoreFuel + round($building->storeFuel * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Orbitalplatform
        elseif ($building->id === 22) {
            tableStart("Zus&auml;tzliche Felder");
            echo "<tr><th>Stufe</th><th>Felder</th><th>Energieverbrauch</th><th>Speicher " . RES_METAL . "</th><th>Speicher " . RES_CRYSTAL . "</th><th>Speicher " . RES_PLASTIC . "</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $prod_item = round($building->fieldsProvide * pow($building->productionFactor, $level - 1));
                $power_use = round($building->powerUse * pow($building->productionFactor, $level - 1));

                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td><td class=\"tbldata2\">" . StringUtils::formatNumber($power_use) . "</td>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td><td>" . StringUtils::formatNumber($power_use) . "</td>";
                }

                $prod_item = round($building->storeMetal * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td>";
                } else {
                    echo "<td>" . StringUtils::formatNumber($prod_item) . "</td>";
                }

                $prod_item = round($building->storeCrystal * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td>";
                } else {
                    echo "<td>" . StringUtils::formatNumber($prod_item) . "</td>";
                }

                $prod_item = round($building->storePlastic * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td>";
                } else {
                    echo "<td>" . StringUtils::formatNumber($prod_item) . "</td>";
                }
            }

            tableEnd();
        }

        //Raketensilo
        elseif ($building->id === 25) {
            tableStart("Energieverbrauch (ohne Boni)");
            echo "<tr><th>Stufe</th><th>Energie</th></tr>";
            for ($level = $b_level; $level < SHOWLEVELS + $b_level; $level++) {
                $power_use = round($building->powerUse * pow($building->productionFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($power_use) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($power_use) . "</td></tr>";
                }
            }

            tableEnd();
        }
        // Rohstoffbunker
        elseif ($building->id === 26) {
            tableStart("Bunkern von Rohstoffen");
            echo "<tr><th>Stufe</th><th>Kapazität</th></tr>";
            for ($level = $b_level; $level < $building->lastLevel + $b_level; $level++) {
                $prod_item = round($building->bunkerRes * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                }
            }

            tableEnd();
        }

        // Flottenbunker
        elseif ($building->id === 27) {
            tableStart("Bunkern von Schiffen");
            echo "<tr><th>Stufe</th><th>Kapazität Stuktur</th><th>Kapazität Anzahl</th></tr>";
            for ($level = $b_level; $level < $building->lastLevel + $b_level; $level++) {
                $prod_item = round($building->bunkerFleetSpace * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<tr><td class=\"tbldata2\">$level</td><td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td>";
                } else {
                    echo "<tr><td>$level</td><td>" . StringUtils::formatNumber($prod_item) . "</td>";
                }

                $prod_item = round($building->bunkerFleetCount * pow($building->storeFactor, $level - 1));
                if ($level === $currentLevel) {
                    echo "<td class=\"tbldata2\">" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                } else {
                    echo "<td>" . StringUtils::formatNumber($prod_item) . "</td></tr>";
                }
            }

            tableEnd();
        }

        tableStart("Kostenentwicklung (Faktor: " . $building->buildCostsFactor . ")");
        echo "<tr><th style=\"text-align:center;\">Level</th>
                <th>" . RES_ICON_METAL . "" . RES_METAL . "</th>
                <th>" . RES_ICON_CRYSTAL . "" . RES_CRYSTAL . "</th>
                <th>" . RES_ICON_PLASTIC . "" . RES_PLASTIC . "</th>
                <th>" . RES_ICON_FUEL . "" . RES_FUEL . "</th>
            <th>" . RES_ICON_FOOD . "" . RES_FOOD . "</th>
   <!-- 	<th>" . RES_ICON_POWER . "Energie</th>     -->
                <th>Felder</th></tr>";
        for ($x = 0; $x < min(30, $building->lastLevel); $x++) {
            $bc = calcBuildingCosts($building, $x);
            echo '<tr><td>' . ($x + 1) . '</td>
                    <td style="text-align:right;">' . StringUtils::formatNumber($bc['metal']) . '</td>
                    <td style="text-align:right;">' . StringUtils::formatNumber($bc['crystal']) . '</td>
                    <td style="text-align:right;">' . StringUtils::formatNumber($bc['plastic']) . '</td>
                    <td style="text-align:right;">' . StringUtils::formatNumber($bc['fuel']) . '</td>
                    <td style="text-align:right;">' . StringUtils::formatNumber($bc['food']) . '</td>
   <!-- 	  <td style="text-align:right;">' . StringUtils::formatNumber($bc['power']) . '</td>      -->
                    <td style="text-align:right;">' . StringUtils::formatNumber($building->fields * ($x + 1)) . '</td></tr>';
        }
        tableEnd();

        iBoxStart("Technikbaum");
        showTechTree("b", $building->id);
        iBoxEnd();
    } else {
        error_msg("Geb&auml;udeinfodaten nicht gefunden!");
    }

    echo "<input type=\"button\" value=\"Geb&auml;ude&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
    if (!$popup) {
        echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=buildings'\" /> &nbsp; ";
    }
    if (isset($_SESSION['lastpage']) && $_SESSION['lastpage'] == "buildings" && !$popup) {
        echo "<input type=\"button\" value=\"Zur&uuml;ck zum Bauhof\" onclick=\"document.location='?page=buildings'\" /> &nbsp; ";
    }
}

//
// Kategorieinfos
//
elseif (isset($_GET['type_id']) && intval($_GET['type_id']) > 0) {
    $btid = intval($_GET['type_id']);

    if ($btid == BuildingTypeId::STORE) {
        echo "<b>Lagerkapazit&auml;t</b><br>";
        echo "Du kannst auf einem Planeten nicht unendlich viele Rohstoffe lagern. Jeder Planet hat eine Lagerkapazit&auml;t von " . $config->getInt('def_store_capacity') . ". Um die Lagerkapazit&auml;t zu erh&ouml;hen, kannst du eine Planetenbasis und danach verschiedene Speicher, Lagerhallen und Silos bauen, welche die Kapazit&auml;t erh&ouml;hen. Wenn eine Zahl in der Rohstoffanzeige rot gef&auml;rbt ist, bedeutet das, dass dieser Rohstoff die Lagerkapazit&auml;t &uuml;berschreitet. Baue in diesem Fall den Speicher aus. Eine &uuml;berschrittene Lagerkapazit&auml;t bedeutet, dass nichts mehr produziert wird, jedoch werden Rohstoffe, die z.B. mit einer Flotte ankommen, trotzdem auf dem Planeten gespeichert.<br>";
    } elseif ($btid == BuildingTypeId::POWER) {
        echo "<b>Energie</b><br>";
        echo "Wo es eine Produkion hat, braucht es auch Energie. Diese Energie, welche von verschiedenen Anlagen gebraucht wird, spenden uns verschiedene Kraftwerkstypen. Je h&ouml;her diese Ausgebaut sind, desto mehr Leistung erbringen sie und versorgen so die wachsende Wirtschaft.<br>
        Hat es zu wenig Energie, wird die Produktion prozentual gedrosselt, was verheerende Auswirkungen haben kann!";
    } elseif ($btid == BuildingTypeId::GENERAL) {
        echo "<b>Allgemeine Geb&auml;ude</b><br/>";
        echo "Diese Geb&auml;ude werden ben&ouml;tigt um deinen Planeten auszubauen und die Produktion und Forschung zu erm&ouml;glichen.";
    } elseif ($btid == BuildingTypeId::RES) {
        echo "<b>Rohstoffgeb&auml;ude</b><br/>";
        echo "Diese Geb&auml;ude liefern Rohstoffe, welche du f&uuml;r den Aufbau deiner Zivilisation brauchst.";
    } else {
        echo "<i>Zu dieser Kategorie sind keine Informationen vorhanden!</i>";
    }

    echo "<br/><br/><input type=\"button\" value=\"Geb&auml;ude&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
    echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=buildings'\" /> &nbsp; ";
}

//
// übersicht
//
else {
    HelpUtil::breadCrumbs(array("Geb&auml;ude", "buildings"));

    /** @var BuildingTypeDataRepository $buildingTypeRepository */
    $buildingTypeRepository = $app[BuildingTypeDataRepository::class];

    /** @var BuildingDataRepository $buildingDataRepository */
    $buildingDataRepository = $app[BuildingDataRepository::class];

    $buildingTypeNames = $buildingTypeRepository->getTypeNames();

    foreach ($buildingTypeNames as $buildingTypeId => $buildingTypeName) {
        $buildings = $buildingDataRepository->getBuildingsByType($buildingTypeId);
        tableStart("<span>" . $buildingTypeName . "</span>");
        foreach ($buildings as $building) {
            echo "<tr>
                <td style=\"width:40px;padding:0px;background:#000;vertical-align:middle;\">
                    <a href=\"?$link&amp;site=$site&id=" . $building->id . "\">
                        <img src=\"" . IMAGE_PATH . "/" . IMAGE_BUILDING_DIR . "/building" . $building->id . "_small." . IMAGE_EXT . "\" align=\"top\" style=\"width:40px;height:40px;background:#000;margin:0px;\" alt=\"Bild " . $building->name . "\" border=\"0\"/></a></td>";
            echo "<td style=\"width:130px;\">
                <a href=\"?$link&amp;site=$site&amp;id=" . $building->id . "\"><b>" . $building->name . "</a></a>
            </td>";
            echo "<td>" . BBCodeUtils::toHTML($building->shortComment) . "</td>";
            echo "<td style=\"width:90px\">";
            if ($building->fields === 0) {
                echo "<b>Keine Felder</b></td>";
            } elseif ($building->fields === 1) {
                echo "<b>" . $building->fields . " Feld</b></td>";
            } else {
                echo "<b>" . $building->fields . " Felder</b></td>";
            }

            echo "</tr>";
        }
        tableEnd();
    }
}
