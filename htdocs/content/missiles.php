<?PHP

use EtoA\Bookmark\BookmarkRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileFlightRepository;
use EtoA\Missile\MissileFlightSearch;
use EtoA\Missile\MissileRepository;
use EtoA\Missile\MissileRequirement;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserPropertiesRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var MissileFlightRepository $missileFlightRepository */
$missileFlightRepository = $app[MissileFlightRepository::class];

/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];
/** @var BookmarkRepository $bookmarkRepository */
$bookmarkRepository = $app[BookmarkRepository::class];
/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

// Info-Link
define("HELP_URL", "?page=help&site=missiles");

// Raketen, die pro Stufe im Silo gelagert werden können (NEU: dient als Vorfaktor zur Basis)
define("MISSILE_SILO_MISSILES_PER_LEVEL", $config->getInt('missile_silo_missiles_per_level'));

/* Neue Konstante fuer eine exponentiell steigende Raketenzahl (by river) */
// Basis des neuen Algorithmus _PER_LEVEL * _ALGO_BASE^(SILO_LEVEL -1)
define("MISSILE_SILO_MISSILES_ALGO_BASE", $config->getFloat('missile_silo_missiles_algo_base'));

// Anzahl gleichzeitiger Flüge pro Silostufe
define("MISSILE_SILO_FLIGHTS_PER_LEVEL", $config->getInt('missile_silo_flights_per_level'));

$planet = $planetRepo->find($cp->id);

echo "<form action=\"?page=$page\" method=\"post\">";

// Gebäude Level und Arbeiter laden
$missileBuilding = $buildingRepository->getEntityBuilding($cu->getId(), $planet->id, BuildingId::MISSILE);

// Prüfen ob Gebäude gebaut ist
if ($missileBuilding !== null && $missileBuilding->currentLevel > 0) {
    // New exponential missile number algorithm by river
    // $max_space = per_level * algo_base ^ (silo_level - 1)
    $max_space = ceil(MISSILE_SILO_MISSILES_PER_LEVEL * pow(MISSILE_SILO_MISSILES_ALGO_BASE, $missileBuilding->currentLevel - 1));
    $max_flights = $missileBuilding->currentLevel * MISSILE_SILO_FLIGHTS_PER_LEVEL;

    // Titel
    echo "<h1>Raketensilo (Stufe " . $missileBuilding->currentLevel . ") des Planeten " . $planet->name . "</h1>";

    // Ressourcen anzeigen
    echo $resourceBoxDrawer->getHTML($planet);

    if ($planet->prodPower - $planet->usePower >= 0 && $planet->prodPower > 0 && $missileBuilding->prodPercent === 1) {
        if ($missileBuilding->isDeactivated()) {

            // Requirements
            /** @var MissileRequirementRepository $missileRequirementRepository */
            $missileRequirementRepository = $app[MissileRequirementRepository::class];
            $requirements = $missileRequirementRepository->getAll();
            $builing_something = false;

            // Gebäudeliste laden
            $buildlist = $buildingRepository->getBuildingLevels($planet->id);

            // Technologieliste laden
            /** @var TechnologyRepository $technologyRepository */
            $technologyRepository = $app[TechnologyRepository::class];
            $techlist = $technologyRepository->getTechnologyLevels($cu->getId());

            // Self destruct flight
            if (isset($_GET['selfdestruct']) && $_GET['selfdestruct'] > 0) {
                if ($missileFlightRepository->deleteFlight((int) $_GET['selfdestruct'], $planet->id)) {
                    success_msg("Die Raketen haben sich selbst zerstört!");
                }
            }

            // Load missiles
            /** @var MissileDataRepository $missileDataRepository */
            $missileDataRepository = $app[MissileDataRepository::class];
            $missiles = $missileDataRepository->getMissiles();

            // Load list
            /** @var MissileRepository $missileRepository */
            $missileRepository = $app[MissileRepository::class];
            $missilelist = $missileRepository->getMissilesCounts($cu->getId(), $planet->id);
            $cnt = 0;
            foreach ($missilelist as $count) {
                $cnt += $count;
            }

            // Launch missiles
            if (isset($_POST['launch']) && checker_verify() && $cnt > 0) {
                // Load missiles
                $launch = array();
                $lcnt = 0;
                foreach ($_POST['count'] as $k => $v) {
                    $v = intval(StringUtils::parseFormattedNumber($v));
                    $k = intval($k);

                    if ($v > 0) {
                        if (isset($missilelist[$k])) {
                            $t = min($missilelist[$k], $v);
                            if ($t > 0) {
                                $launch[$k] = $t;
                            }
                        }
                    }
                }

                if (count($launch) > 0) {
                    // Save flight
                    $missileFlightRepository->startFlight($planet->id, (int) $_POST['targetplanet'], (int) $_POST['timeforflight'], $launch); // TODO: timeforflight comes from client? srsly?

                    foreach ($launch as $missileId => $count) {
                        // Update list
                        $missileRepository->addMissile($missileId, -$count, $cu->getId(), $planet->id);
                        $missilelist[$missileId] -= $count;
                        $lcnt += $count;
                    }

                    $cnt -= $lcnt;
                    success_msg("Raketen gestartet!");
                    $app['dispatcher']->dispatch(new \EtoA\Missile\Event\MissileLaunch($launch), \EtoA\Missile\Event\MissileLaunch::LAUNCH_SUCCESS);
                } else {
                    error_msg("Raketen konnten nicht gestartet werden, keine Raketen gewählt!");
                }
            }

            // Load flights
            $flights = $missileFlightRepository->getFlights(MissileFlightSearch::create()->entityFrom($planet->id));
            $fcnt = count($flights);

            // Kaufen
            if (isset($_POST['buy']) && checker_verify()) {
                if (count($_POST['missile_count']) > 0) {
                    $buy = 0;
                    $valid = false;
                    $buymissiles = array();
                    foreach ($_POST['missile_count'] as $k => $v) {
                        $v = intval($v);
                        $k = intval($k);
                        if ($v > 0) {
                            $valid = true;
                            if ($v + $cnt <= $max_space) {
                                $bc = $v;
                            } else {
                                $bc = $max_space - $cnt;
                            }
                            $bc = max($bc, 0);
                            if ($bc > 0) {
                                $buymissiles[$k] = $bc;
                            }
                            $cnt += $bc;
                        }
                    }

                    if ($valid) {
                        $bc = 0;
                        foreach ($buymissiles as $k => $v) {
                            $bc += $v;

                            $mcosts = [];
                            $mcosts[0] = $missiles[$k]->costsMetal * $v;
                            $mcosts[1] = $missiles[$k]->costsCrystal * $v;
                            $mcosts[2] = $missiles[$k]->costsPlastic * $v;
                            $mcosts[3] = $missiles[$k]->costsFuel * $v;
                            $mcosts[4] = $missiles[$k]->costsFood * $v;

                            if (
                                $planet->resMetal >= $mcosts[0] &&
                                $planet->resCrystal >= $mcosts[1] &&
                                $planet->resPlastic >= $mcosts[2] &&
                                $planet->resFuel >= $mcosts[3] &&
                                $planet->resFood >= $mcosts[4]
                            ) {
                                $missileRepository->addMissile($k, $v, $cu->getId(), $planet->id);
                                $missilelist[$k] = $v + ($missilelist[$k] ?? 0);

                                $planetRepo->addResources($planet->id, -$mcosts[0], -$mcosts[1], -$mcosts[2], -$mcosts[3], -$mcosts[4]);
                                success_msg($v . " " . $missiles[$k]->name . " wurden gekauft!");

                                $app['dispatcher']->dispatch(new \EtoA\Missile\Event\MissileBuy($k, $v), \EtoA\Missile\Event\MissileBuy::BUY_SUCCESS);
                            } else {
                                error_msg("Konnte " . $missiles[$k]->name . " nicht kaufen, zu wenig Ressourcen!");
                            }
                        }
                        if ($bc == 0) {
                            error_msg("Es konten keine Raketen gekauft werden, zuwenig Platz!");
                        }
                    } else {
                        error_msg("Keine oder ungültige Anzahl gewählt!");
                    }
                } else {
                    error_msg("Keine Raketen gewählt!");
                }
            }

            // Remove
            if (isset($_POST['scrap']) && checker_verify()) {
                if (count($_POST['missile_count']) > 0) {
                    $buy = 0;
                    $valid = false;
                    foreach ($_POST['missile_count'] as $k => $v) {

                        $v = StringUtils::parseFormattedNumber($v);
                        $k = intval($k);

                        if ($v > 0) {
                            $valid = true;
                            $bc = min($v, $missilelist[$k]);

                            $missileRepository->addMissile($k, -$bc, $cu->getId(), $planet->id);
                            $missilelist[$k] -= $bc;
                            $cnt -= $bc;
                            success_msg($bc . " " . $missiles[$k]->name . " wurden verschrottet!");
                        }
                    }
                    if (!$valid) {
                        error_msg("Keine oder ungültige Anzahl gewählt!");
                    }
                } else {
                    error_msg("Keine Raketen gewählt!");
                }
            }


            $cstr = checker_init();
            // Flüge anzeigen
            if ($fcnt > 0) {
                $missileNames = $missileDataRepository->getMissileNames(true);
                $time = time();
                tableStart("Abgefeuerte Raketen");
                echo "<tr><th>Ziel</th><th>Flugdauer</th><th>Ankunftszeit</th><th>Raketen</th><th>Optionen</th></tr>";
                foreach ($flights as $flight) {
                    $countdown = ($flight->landTime - $time >= 0) ? StringUtils::formatTimespan($flight->landTime - $time) : 'Im Ziel';
                    echo '<tr><td>' . $flight->targetPlanetName . '</td>
                    <td>' . $countdown . '</td>
                    <td>' . StringUtils::formatDate($flight->landTime) . '</td>
                    <td>';
                    foreach ($flight->missiles as $missileId => $count) {
                        echo StringUtils::formatNumber($count) . ' ' . $missileNames[$missileId] . '<br/>';
                    }
                    echo '</td>
                    <td><a href="?page=' . $page . '&amp;selfdestruct=' . $flight->id . '" onclick="return confirm(\'Sollen die gewählten Raketen wirklich selbstzerstört werden?\')">Selbstzerstörung</a></td></tr>';
                }
                tableEnd();
            }


            // Raketen anzeigen
            if (count($missiles) > 0) {
                if ($max_space > 0) {
                    $bar1_red = min(ceil($cnt / $max_space * 200), 200);
                } else {
                    $bar1_red = 0;
                }

                echo '<form action="?page=' . $page . '" method="post">';
                echo $cstr;

                // Rechnet %-Werte für Tabelle
                $store_width = ceil($cnt / $max_space * 100);

                tableStart("Silobelegung");
                echo '<tr>
                                <tdstyle="padding:0px;height:10px;"><img src="images/poll3.jpg" style="height:10px;width:' . $store_width . '%;" alt="poll" />
                            </tr>
                            <tr>
                                <td style="text-align:center;">
                                    ' . StringUtils::formatNumber($cnt) . ' von ' . StringUtils::formatNumber($max_space) . ', ' . round($cnt / $max_space * 100, 0) . '%
                            </tr>';
                tableEnd();

                tableStart("Raketen verwalten");

                $cnt2 = 0;
                foreach ($missiles as $missile) {

                    // Check requirements for this building
                    $requirements_passed = true;
                    foreach ($requirements->getBuildingRequirements($missile->id) as $requirement) {
                        if (!isset($buildlist[$requirement->requiredBuildingId]) || $buildlist[$requirement->requiredBuildingId] < $requirement->requiredLevel) {
                            $requirements_passed = false;
                        }
                    }

                    foreach ($requirements->getTechnologyRequirements($missile->id) as $requirement) {
                        if (!isset($techlist[$requirement->requiredTechnologyId]) || $techlist[$requirement->requiredTechnologyId] < $requirement->requiredLevel) {
                            $requirements_passed = false;
                        }
                    }

                    if ($requirements_passed) {
                        //Errechnet wie viele Raketen von diesem Typ maximal gekauft werden können mit den aktuellen Rohstoffen

                        // Silokapazität
                        $store = $max_space - $cnt;

                        //Titan
                        if ($missile->costsMetal > 0) {
                            $build_cnt_metal = floor($planet->resMetal / $missile->costsMetal);
                        } else {
                            $build_cnt_metal = 99999999999;
                        }

                        //Silizium
                        if ($missile->costsCrystal > 0) {
                            $build_cnt_crystal = floor($planet->resCrystal / $missile->costsCrystal);
                        } else {
                            $build_cnt_crystal = 99999999999;
                        }

                        //PVC
                        if ($missile->costsPlastic > 0) {
                            $build_cnt_plastic = floor($planet->resPlastic / $missile->costsPlastic);
                        } else {
                            $build_cnt_plastic = 99999999999;
                        }

                        //Tritium
                        if ($missile->costsFuel > 0) {
                            $build_cnt_fuel = floor($planet->resFuel / $missile->costsFuel);
                        } else {
                            $build_cnt_fuel = 99999999999;
                        }

                        //Nahrung
                        if ($missile->costsFood > 0) {
                            $build_cnt_food = floor($planet->resFood / $missile->costsFood);
                        } else {
                            $build_cnt_food = 99999999999;
                        }

                        //Effetiv max. kaufbare Raketen in Betrachtung der Rohstoffe und der Silokapazität
                        $missile_max_build = min($build_cnt_metal, $build_cnt_crystal, $build_cnt_plastic, $build_cnt_fuel, $build_cnt_food, $store);

                        // Grösste Zahl die eingegeben werden kann (Da man auch verschrotten kann)
                        $available_missles = isset($missilelist[$missile->id]) ? $missilelist[$missile->id] : 0;
                        $missile_max_number = max($missile_max_build, $available_missles);

                        //Tippbox Nachricht generieren
                        //X Anlagen baubar
                        if ($missile_max_build > 0) {
                            $tm_cnt = "Es k&ouml;nnen maximal " . StringUtils::formatNumber($missile_max_build) . " Raketen gekauft werden.";
                        }
                        //Zu wenig Felder.
                        elseif ($store == 0) {
                            $tm_cnt = "Das Silo ist zu klein für weitere Raketen!";
                        }
                        //Zuwenig Rohstoffe. Wartezeit errechnen
                        elseif ($missile_max_build == 0 && $store != 0) {
                            //Wartezeit Titan
                            $bwait = [];
                            if ($planet->prodMetal > 0) {
                                $bwait['metal'] = ceil(($missile->costsMetal - $planet->resMetal) / $planet->prodMetal * 3600);
                            } else {
                                $bwait['metal'] = 0;
                            }

                            //Wartezeit Silizium
                            if ($planet->prodCrystal > 0) {
                                $bwait['crystal'] = ceil(($missile->costsCrystal - $planet->resCrystal) / $planet->prodCrystal * 3600);
                            } else {
                                $bwait['crystal'] = 0;
                            }

                            //Wartezeit PVC
                            if ($planet->prodPlastic > 0) {
                                $bwait['plastic'] = ceil(($missile->costsPlastic - $planet->resPlastic) / $planet->prodPlastic * 3600);
                            } else {
                                $bwait['plastic'] = 0;
                            }

                            //Wartezeit Tritium
                            if ($planet->prodFuel > 0) {
                                $bwait['fuel'] = ceil(($missile->costsFuel - $planet->resFuel) / $planet->prodFuel * 3600);
                            } else {
                                $bwait['fuel'] = 0;
                            }

                            //Wartezeit Nahrung
                            if ($planet->prodFood > 0) {
                                $bwait['food'] = ceil(($missile->costsFood - $planet->resFood) / $planet->prodFood * 3600);
                            } else {
                                $bwait['food'] = 0;
                            }

                            //Maximale Wartezeit ermitteln
                            $bwmax = max($bwait['metal'], $bwait['crystal'], $bwait['plastic'], $bwait['fuel'], $bwait['food']);

                            $tm_cnt = "Rohstoffe verf&uuml;gbar in " . StringUtils::formatTimespan($bwmax) . "";
                        } else {
                            $tm_cnt = "";
                        }

                        //Stellt Rohstoff Rot dar, wenn es von diesem zu wenig auf dem Planeten hat
                        //Titan
                        if ($missile->costsMetal > $planet->resMetal) {
                            $ress_style_metal = "style=\"color:red;\"";
                        } else {
                            $ress_style_metal = "";
                        }

                        //Silizium
                        if ($missile->costsCrystal > $planet->resCrystal) {
                            $ress_style_crystal = "style=\"color:red;\"";
                        } else {
                            $ress_style_crystal = "";
                        }

                        //PVC
                        if ($missile->costsPlastic > $planet->resPlastic) {
                            $ress_style_plastic = "style=\"color:red;\"";
                        } else {
                            $ress_style_plastic = "";
                        }

                        //Tritium
                        if ($missile->costsFuel > $planet->resFuel) {
                            $ress_style_fuel = "style=\"color:red;\"";
                        } else {
                            $ress_style_fuel = "";
                        }

                        //Nahrung
                        if ($missile->costsFood > $planet->resFood) {
                            $ress_style_food = "style=\"color:red;\"";
                        } else {
                            $ress_style_food = "";
                        }

                        // Volle Ansicht
                        if ($properties->itemShow == 'full') {
                            if ($cnt2 > 0) {
                                echo "<tr>
                                        <td colspan=\"5\" style=\"height:5px;\"></td>
                                </tr>";
                            }

                            $d_img = $missile->getImagePath('middle');
                            echo "<tr>
                                <th colspan=\"5\">" . $missile->name . "</th>
                            </tr>
                            <tr>
                                <td width=\"120\" height=\"120\" rowspan=\"5\">";
                            //Bild mit Link zur Hilfe darstellen
                            echo "<a href=\"" . HELP_URL . "&amp;id=" . $missile->id . "\" title=\"Info zu dieser Rakete anzeigen\">
                                <img src=\"" . $d_img . "\" width=\"120\" height=\"120\" border=\"0\" /></a>";
                            echo "</td>
                                <td colspan=\"4\" valign=\"top\">" . $missile->shortDescription . "</td>
                            </tr>
                            <tr>
                                <th>Geschwindigkeit:</th>
                                <td>";
                            if ($missile->speed > 0) {
                                echo "" . StringUtils::formatNumber($missile->speed) . "";
                            } else {
                                echo "-";
                            }
                            echo "</td>
                                <th rowspan=\"2\">Vorhanden:</th>
                                <td rowspan=\"2\">" . StringUtils::formatNumber($available_missles) . "</td>
                            </tr>
                            <tr>
                                <th>Reichweite:</th>
                                <td>";
                            if ($missile->range > 0) {
                                echo "" . StringUtils::formatNumber($missile->range) . " AE";
                            } else {
                                echo "-";
                            }
                            echo "</td>
                            </tr>
                            <tr>
                                <th>";

                            if ($missile->def > 0) {
                                echo "Sprengköpfe";
                            } elseif ($missile->damage > 0) {
                                echo "Schaden";
                            } elseif ($missile->deactivate > 0) {
                                echo "Schaden";
                            }

                            echo "</th>
                                <td>";

                            if ($missile->def > 0) {
                                echo StringUtils::formatNumber($missile->def);
                            } elseif ($missile->damage > 0) {
                                echo StringUtils::formatNumber($missile->damage);
                            } else {
                                echo "0";
                            }

                            echo "</td>
                                <th rowspan=\"2\">Kaufen:</th>
                            <td rowspan=\"2\">
                                        <input type=\"text\" value=\"0\" id=\"missile_count_" . $missile->id . "\" name=\"missile_count[" . $missile->id . "]\" size=\"5\" maxlength=\"9\" " . tm("", $tm_cnt) . " onkeyup=\"FormatNumber(this.id,this.value, " . $missile_max_number . ", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('missile_count_" . $missile->id . "').value=" . $missile_max_build . ";\">max</a>
                            </td>";
                            echo "<tr>
                            <th>EMP:</th>
                            <td>";
                            if ($missile->deactivate > 0) {
                                echo StringUtils::formatTimespan($missile->deactivate);
                            } else {
                                echo "Nein";
                            }
                            echo "</td>
                                        </tr>";

                            echo "</tr>";
                            echo "<tr>
                                <th height=\"20\" width=\"110\">" . ResourceNames::METAL . ":</th>
                                <th height=\"20\" width=\"97\">" . ResourceNames::CRYSTAL . ":</th>
                                <th height=\"20\" width=\"98\">" . ResourceNames::PLASTIC . ":</th>
                                <th height=\"20\" width=\"97\">" . ResourceNames::FUEL . ":</th>
                                <th height=\"20\" width=\"98\">" . ResourceNames::FOOD . "</th></tr>";
                            echo "<tr>
                                <td height=\"20\" width=\"110\" " . $ress_style_metal . ">
                                    " . StringUtils::formatNumber($missile->costsMetal) . "
                                </td>
                                <td height=\"20\" width=\"25%\" " . $ress_style_crystal . ">
                                    " . StringUtils::formatNumber($missile->costsCrystal) . "
                                </td>
                                <td height=\"20\" width=\"25%\" " . $ress_style_plastic . ">
                                    " . StringUtils::formatNumber($missile->costsPlastic) . "
                                </td>
                                <td height=\"20\" width=\"25%\" " . $ress_style_fuel . ">
                                    " . StringUtils::formatNumber($missile->costsFuel) . "
                                </td>
                                <td height=\"20\" width=\"25%\" " . $ress_style_food . ">
                                    " . StringUtils::formatNumber($missile->costsFood) . "
                                </td>
                            </tr>";
                        }

                        //Einfache Ansicht der Schiffsliste
                        else {
                            $d_img = $missile->getImagePath('middle');
                            echo "<tr>
                                    <td>";
                            //Bild mit Link zur Hilfe darstellen
                            echo "<a href=\"" . HELP_URL . "&amp;id=" . $missile->id . "\"><img src=\"" . $d_img . "\" width=\"40\" height=\"40\" border=\"0\" /></a></td>";
                            echo "<th width=\"40%\">
                                        " . $missile->name . "<br/>
                                        <span class=\"textSmall\" style=\"font-weight:500;\">
                                        <b>Vorhanden:</b> " . StringUtils::formatNumber($missilelist[$missile->id]) . "</span></th>
                                    <td width=\"10%\" " . $ress_style_metal . ">" . StringUtils::formatNumber($missile->costsMetal) . "</td>
                                    <td width=\"10%\" " . $ress_style_crystal . ">" . StringUtils::formatNumber($missile->costsCrystal) . "</td>
                                    <td width=\"10%\" " . $ress_style_plastic . ">" . StringUtils::formatNumber($missile->costsPlastic) . "</td>
                                    <td width=\"10%\" " . $ress_style_fuel . ">" . StringUtils::formatNumber($missile->costsFuel) . "</td>
                                    <td width=\"10%\" " . $ress_style_food . ">" . StringUtils::formatNumber($missile->costsFood) . "</td>
                                    <td>
                                        <input type=\"text\" value=\"0\" id=\"missile_count_" . $missile->id . "\" name=\"missile_count[" . $missile->id . "]\" size=\"5\" maxlength=\"9\" " . tm("", $tm_cnt) . " tabindex=\"0\" onkeyup=\"FormatNumber(this.id,this.value, " . $missile_max_number . ", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('missile_count_" . $missile->id . "').value=" . $missile_max_build . ";\">max</a>
                                    </td>
                                </tr>";
                        }

                        $cnt2++;
                    }
                }
                tableEnd();
                echo '<br/><input type="submit" name="buy" value="Ausgewählte Anzahl kaufen" /> &nbsp; ';
                echo '<input type="submit" name="scrap" value="Ausgewählte Anzahl verschrotten" onclick="return confirm(\'Sollen die gewählten Raketen wirklich verschrottet werden? Es werden keine Ressourcen zurückerstattet!\')" /></form><br/><br><br>';

                if ($cnt > 0) {

                    // Kampfsperre prüfen
                    if ($config->getBoolean('battleban') && $config->param1Int('battleban_time') <= time() && $config->param2Int('battleban_time') > time()) {
                        iBoxStart("Kampfsperre");
                        echo "Es ist momentan nicht m&ouml;glich andere Spieler anzugreifen. Grund: " . BBCodeUtils::toHTML($config->param1('battleban')) . "<br>Die Sperre dauert vom " . date("d.m.Y", $config->param1Int('battleban_time')) . " um " . date("H:i", $config->param1Int('battleban_time')) . " Uhr bis am " . date("d.m.Y", $config->param2Int('battleban_time')) . " um " . date("H:i", $config->param2Int('battleban_time')) . " Uhr!";
                        iBoxEnd();
                    } else {
                        if ($fcnt < $max_flights) {

                            // Bookmarks laden
                            $bookmarks = array();
                            // Gespeicherte Bookmarks
                            $bookmarkedEntities = $bookmarkRepository->getBookmarkedEntities($cu->getId());
                            foreach ($bookmarkedEntities as $bookmarkedEntity) {
                                array_push(
                                    $bookmarks,
                                    array(
                                        "cell_sx" => $bookmarkedEntity->sx,
                                        "cell_sy" => $bookmarkedEntity->sy,
                                        "cell_cx" => $bookmarkedEntity->cx,
                                        "cell_cy" => $bookmarkedEntity->cy,
                                        "planet_solsys_pos" => $bookmarkedEntity->pos,
                                        "planet_name" => $bookmarkedEntity->planetName,
                                        "automatic" => 0,
                                        "bookmark_comment" => $bookmarkedEntity->comment
                                    )
                                );
                            }

                            $entity = $entityRepository->findIncludeCell($planet->id);
                            $coords = [];
                            if (isset($_GET['target'])) {
                                $target = $entityRepository->getEntity((int) $_GET['target']);
                            } else {
                                $target = $entity;
                            }

                            $keyup_command = 'xajax_getFlightTargetInfo(xajax.getFormValues(\'targetForm\'),' . $entity->sx . ',' . $entity->sy . ',' . $entity->cx . ',' . $entity->cy . ',' . $entity->pos . ');';
                            echo '<form action="?page=' . $page . '" method="post" id="targetForm">';
                            echo $cstr;
                            tableStart("Raketen starten");
                            echo '<tr><th style="width:260px;">Raketen wählen</th><th colspan="2" style="width:440px;">Ziel wählen</th></tr>
                            <tr><td rowspan="6">';
                            $lblcnt = 0;
                            foreach ($missilelist as $k => $v) {
                                if ($v > 0 && $missiles[$k]->launchable) {
                                    echo '<input type="hidden" value="' . $missiles[$k]->speed . '" name="speed[' . $k . ']" />';
                                    echo '<input type="hidden" value="' . $missiles[$k]->range . '" name="range[' . $k . ']" />';
                                    echo '<input type="text" value="0" id="missle_' . $k . '" name="count[' . $k . ']" size="4" onkeyup="FormatNumber(this.id,this.value, \'' . $v . '\', \'\', \'\');' . $keyup_command . '"/>
                                    ' . $missiles[$k]->name . ' (' . $v . ' vorhanden)<br/>';
                                    $lblcnt++;
                                }
                            }
                            if ($lblcnt == 0) {
                                echo 'Momentan befinden sich keine startbaren Raketen in deinem Silo!';
                            }
                            echo '</td><th>:</th>
                            <td>
                                <input type="text"  onkeyup="' . $keyup_command . '" name="sx" id="sx" value="' . $target->sx . '" size="2" autocomplete="off" maxlength="2" /> /
                                <input type="text"  onkeyup="' . $keyup_command . '" name="sy" id="sy" value="' . $target->sy . '" size="2" autocomplete="off" maxlength="2" /> :
                                <input type="text"  onkeyup="' . $keyup_command . '" name="cx" id="cx" value="' . $target->cx . '" size="2" autocomplete="off" maxlength="2" /> /
                                <input type="text"  onkeyup="' . $keyup_command . '" name="cy" id="cy" value="' . $target->cy . '" size="2" autocomplete="off" maxlength="2" /> :
                                <input type="text"  onkeyup="' . $keyup_command . '" name="p" id="p" value="' . $target->pos . '" size="2" autocomplete="off" maxlength="2" />
                            </td></tr>';

                            // Bookmarkliste anzeigen
                            echo "<tr><th>Favorit wählen:</th><td><select id=\"bookmarkselect\" onchange=\"applyBookmark();\">";
                            if (count($bookmarks) > 0) {
                                $a = 1;
                                echo "<option value=\"\">W&auml;hlen...</option>";
                                foreach ($bookmarks as $i => $b) {
                                    echo "<option value=\"$i\">";
                                    if ($b['automatic'] == 1) echo "Eigener Planet: ";
                                    echo $b['cell_sx'] . "/" . $b['cell_sy'] . " : " . $b['cell_cx'] . "/" . $b['cell_cy'] . " : " . $b['planet_solsys_pos'] . " " . $b['planet_name'];
                                    if ($b['bookmark_comment'] != "") echo " (" . stripslashes($b['bookmark_comment']) . ")";
                                    echo "</option>";
                                }
                            } else
                                echo "<option value=\"\">(Nichts vorhaden)</option>";
                            echo "</select></td></tr>";

                            echo '<tr><th>Zielinfo:</th><td id="targetinfo">
                            Wähle bitte ein Ziel...
                            </td></tr>
                            <tr><th>Entfernung:</th><td id="distance">
                            -
                            </td></tr>
                            <tr><th>Geschwindigkeit:</th><td id="speed">
                            -
                            </td></tr>
                            <tr><th>Zeit:</th><td id="time">
                            -
                            </td></tr>';
                            tableEnd();
                            echo '<input style="color:#f00" type="submit" name="launch" id="launchbutton" value="Starten" disabled="disabled" />';
                            echo '<input type="hidden" name="timeforflight" value="0" id="timeforflight" />
                            <input type="hidden" name="targetcell" value="0" id="targetcell" />
                            <input type="hidden" name="targetplanet" value="0" id="targetplanet" /></form>';
                            echo '<script type="text/javascript">' . $keyup_command . '</script>';
                            echo "<script type=\"text/javascript\">
                            function applyBookmark()
                            {
                                select_id=document.getElementById('bookmarkselect').selectedIndex;
                                select_val=document.getElementById('bookmarkselect').options[select_id].value;
                                a=1;
                                if (select_val!='')
                                {
                                    switch(select_val)
                                    {
                                        ";
                            foreach ($bookmarks as $i => $b) {
                                echo "case \"$i\":\n";
                                echo "document.getElementById('sx').value='" . $b['cell_sx'] . "';\n";
                                echo "document.getElementById('sy').value='" . $b['cell_sy'] . "';\n";
                                echo "document.getElementById('cx').value='" . $b['cell_cx'] . "';\n";
                                echo "document.getElementById('cy').value='" . $b['cell_cy'] . "';\n";
                                echo "document.getElementById('p').value='" . $b['planet_solsys_pos'] . "';\n";
                                echo "break;\n";
                            }
                            echo "
                                    }

                                }
                                " . $keyup_command . "
                            }
                            </script>";
                        } else {
                            info_msg("Baue zuerst dein Raketensilo aus um mehr Raketen zu starten (" . MISSILE_SILO_FLIGHTS_PER_LEVEL . " Angriff pro Stufe)!");
                        }
                    }
                }
            } else {
                info_msg("Keine Raketen verfügbar!");
            }
        } else {
            info_msg("Dieses Gebäude ist noch bis " . StringUtils::formatDate($missileBuilding->deactivated) . " deaktiviert!");
        }
    } else {
        info_msg("Zu wenig Energie verfügbar! Gebäude ist deaktiviert!");
    }
} else {
    // Titel
    echo "<h1>Raketensilo des Planeten " . $planet->name . "</h1>";

    // Ressourcen anzeigen
    echo $resourceBoxDrawer->getHTML($planet);
    info_msg("Das Raketensilo wurde noch nicht gebaut!");
}
