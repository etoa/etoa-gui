<?php

namespace EtoA\Shipyard;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ship\ShipCategoryRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Ship\ShipSearch;
use EtoA\Ship\ShipSort;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\HttpFoundation\RequestStack;

class ShipyardService
{
    public function __construct(
        private readonly ShipQueueRepository          $shipQueueRepository,
        private readonly PlanetRepository             $planetRepository,
        private readonly RequestStack                 $requestStack,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly ConfigurationService         $config,
        private readonly ShipCategoryRepository       $shipCategoryRepository,
        private readonly ShipDataRepository           $shipDataRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository
    )
    {
    }

    public
    function renderOverview(): bool|string
    {
        ob_start();

        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $shipyard = $this->buildingListItemRepository->findOneBy(['building' => BuildingId::SHIPYARD->value, 'entity' => $cp]);
        $minLevel = $this->config->getInt('shipqueue_cancel_min_level');
        $cancelEnd = $this->config->getFloat('shipqueue_cancel_end');
        $cancelStart = $this->config->getFloat('shipqueue_cancel_start');
        $cancelFactor = $this->config->getFloat('shipqueue_cancel_factor');
        $shipCategories = $this->shipCategoryRepository->getAllCategories();
        $time = time();
        $queue = [];
        $shipsByCategory = [];
        $shipSearch = ShipSearch::create()->buildable()->raceOrNull($cp->getUser()->getRace());
        $shipOrder = ShipSort::specialWithUserSort($cp->getUser()->getProperties()->getItemOrderShip(), $cp->getUser()->getProperties()->getItemOrderWay());
        $specialist = $cp->getUser()->getSpecialist();
        $gen_tech_level = $this->technologyListItemRepository->findOneBy(['tech'=>TechnologyId::GEN,'user'=>$cp->getUser()])?->getCurrentLevel() ?? 0;
        $need_bonus_level = $shipyard->currentLevel - $this->config->param1Int('build_time_boni_schiffswerft');
        if ($need_bonus_level <= 0) {
            $time_boni_factor = 1;
        } else {
            $time_boni_factor = 1 - ($need_bonus_level * ($this->config->getInt('build_time_boni_schiffswerft') / 100));
        }


        $items = $this->shipDataRepository->searchShips($shipSearch, $shipOrder);
        foreach ($items as $ship) {
            $shipsByCategory[$ship->getCat()->getId()][] = $ship;
            $ships[$ship->getId()] = $ship;
            $shipCosts[$ship->getId()] = PreciseResources::createFromBase($ship->getCosts())->multiply($specialist !== null ? $specialist->getCostsShips() : 1);
        }

        // Faktor der zurückerstatteten Ressourcen bei einem Abbruch des Auftrags berechnen
        if ($shipyard->getCurrentLevel() >= $minLevel) {
            $cancel_res_factor = min($cancelEnd, $cancelStart + (($shipyard->getCurrentLevel() - $minLevel) * $cancelFactor));
        } else {
            $cancel_res_factor = 0;
        }


        if ($cancel_res_factor > 0) {
            $cancelable = true;
        } else {
            $cancelable = false;
        }

        $shipQueueItems = $this->shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->entityId($cp)->endAfter($time));


        /*********************************
         * Liste der Bauaufträge anzeigen *
         *********************************/
        if (count($shipQueueItems) > 0) {
            tableStart("Bauliste");
            $first = true;
            $absolute_starttime = 0;
            foreach ($shipQueueItems as $data) {
                if ($first) {
                    $obj_t_remaining = ((($data->getEndTime() - $time) / $data->getObjectTime()) - floor(($data->getEndTime() - $time) / $data->getObjectTime())) * $data->getObjectTime();
                    if ($obj_t_remaining == 0) {
                        $obj_t_remaining = $data->getObjectTime();
                    }
                    $obj_time = $data->getObjectTime();

                    $absolute_starttime = $data->getStartTime();

                    $obj_t_passed = $data->getObjectTime() - $obj_t_remaining;
                    echo "<tr>
                            <th colspan=\"2\">Aktuell</th>
                            <th>Start</th>
                            <th>Ende</th>
                            <th colspan=\"2\">Verbleibend</th>
                        </tr>";
                    echo "<tr>";
                    echo "<td colspan=\"2\">" . $data->getShip()->getName() . "</td>";
                    echo "<td>" . StringUtils::formatDate(time() - $obj_t_passed) . "</td>";
                    echo "<td>" . StringUtils::formatDate(time() + $obj_t_remaining) . "</td>";
                    echo "<td colspan=\"2\">" . StringUtils::formatTimespan($obj_t_remaining) . "</td>
                    </tr>";
                    echo "<tr>
                            <th style=\"width:40px;\">Anzahl</th>
                            <th>Bauauftrag</th>
                            <th style='width:100%'>Bauauftrag</th>
                            <th>Start</th>
                            <th>Ende</th>
                            <th>Verbleibend</th>
                            <th>Aktionen</th>
                        </tr>";
                    $first = false;
                }

                echo "<tr>";
                echo "<td id=\"objcount\">" . $data->getCount() . "</td>";
                echo "<td>" . $data->getShip()->getName() . "</td>";
                echo "<td style='white-space: nowrap;'>" . StringUtils::formatDate($absolute_starttime) . "</td>";
                echo "<td style='white-space: nowrap;'>" . StringUtils::formatDate($absolute_starttime + $data->getEndTime() - $data->getStartTime()) . "</td>";
                echo "<td style='white-space: nowrap;'>" . StringUtils::formatTimespan($data->getEndTime() - time()) . "</td>";
                echo "<td id=\"cancel\">";
                if ($cancelable) {
                    echo "<a href=\"?page=&amp;cancel=" . $data->getId() . "\" onclick=\"return confirm('Soll dieser Auftrag wirklich abgebrochen werden?');\">Abbrechen</a>";
                } else {
                    echo "-";
                }
                echo "</td>
                </tr>";

                //Setzt die Startzeit des nächsten Schiffes, auf die Endzeit des jetztigen Schiffes
                $absolute_starttime = $data->getEndTime();
            }
            tableEnd();
        }


        /***********************
         * Schiffe auflisten    *
         ***********************/

        $cnt = 0;
        if (count($shipCategories) > 0) {
            $compactView = $cp->getUser()->getProperties()->isTtemShow() !== 'full';
            foreach ($shipCategories as $category) {
                if (!isset($shipsByCategory[$category->getId()])) {
                    continue;
                }

                tableStart($category->getName(), 0, "", "", "shipCategory " . ($compactView ? "compact" : ""));
                $ccnt = 0;

                // Auflistung der Schiffe (auch diese, die noch nicht gebaut wurden)
                if (count($shipsByCategory[$category->getId()]) > 0) {
                    //Einfache Ansicht
                    if ($compactView) {
                        echo '<tr>
                                        <th colspan="2" class="tbltitle">Schiff</th>
                                        <th class="tbltitle">Zeit</th>
                                        <th class="tbltitle">' . ResourceNames::METAL . '</th>
                                        <th class="tbltitle">' . ResourceNames::CRYSTAL . '</th>
                                        <th class="tbltitle">' . ResourceNames::PLASTIC . '</th>
                                        <th class="tbltitle">' . ResourceNames::FUEL . '</th>
                                        <th class="tbltitle">' . ResourceNames::FOOD . '</th>
                                        <th class="tbltitle">Anzahl</th>
                                    </tr>';
                    }

                    $buildingLevels = $this->buildingListItemRepository->getBuildingLevels($cp);
                    foreach ($shipsByCategory[$category->getId()] as $shipData) {
                        // Prüfen ob Schiff gebaut werden kann
                        $build_ship = 1;

                        $requirements = $shipData->getObjectRequirements();


                        foreach ($requirements->getBuildingRequirements() as $requirement) {
                            // Gebäude prüfen
                            if ($requirement->getBuilding() && $requirement->getLevel() > $this->buildingListItemRepository->findOneBy(['building' => $requirement->getBuilding(), 'entity' => $cp])?->getCurrentLevel()) {
                                $build_ship = 0;
                            }

                            // Technologien prüfen
                            if ($requirement->getTech() && $requirement->getLevel() > $this->technologyListItemRepository->findOneBy(['user' => $cp->getUser(), 'technology' => $requirement->getTech()])?->getCurrentLevel()) {
                                $build_ship = 0;
                            }
                        }

                        // Schiffdatensatz zeigen wenn die Voraussetzungen erfüllt sind
                        if ($build_ship == 1) {
                            // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
                            $ship_count = 0;
                            // ... auf den Planeten
                            if (isset($shiplist[$shipData->id])) {
                                $ship_count += array_sum($shiplist[$shipData->id]);
                            }
                            // ... im Bunker
                            if (isset($bunkered[$shipData->id])) {
                                $ship_count += $bunkered[$shipData->id];
                            }
                            // ... in der Bauliste
                            if (isset($queue_total[$shipData->id])) {
                                $ship_count += $queue_total[$shipData->id];
                            }
                            // ... in der Luft
                            if (isset($fleet[$shipData->id])) {
                                $ship_count += $fleet[$shipData->id];
                            }

                            // Bauzeit berechnen
                            $btime = ($shipCosts[$shipData->id]->getSum()) / $this->config->getInt('global_time') * $this->config->getFloat('ship_build_time') * $time_boni_factor * ($specialist !== null ? $specialist->timeShips : 1);
                            $btime_min = $btime * (0.1 - ($gen_tech_level / 100));
                            $peopleOptimized = ceil(($btime - $btime_min) / $this->config->getInt('people_work_done'));

                            //Mindest Bauzeit
                            if ($btime_min < $this->config->getInt('shipyard_min_build_time')) {
                                $btime_min = $this->config->getInt('shipyard_min_build_time');
                            }

                            $btime = ceil($btime - $shipyard->peopleWorking * $this->config->getInt('people_work_done'));
                            if ($btime < $btime_min) {
                                $btime = $btime_min;
                            }

                            //Nahrungskosten berechnen
                            $food_costs = $shipyard->peopleWorking * $this->config->getInt('people_food_require');

                            //Nahrungskosten versteckt übermitteln
                            echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"" . $food_costs . "\" />";
                            $food_costs += $shipCosts[$shipData->id]->food;


                            //Errechnet wie viele Schiffe von diesem Typ maximal Gebaut werden können mit den aktuellen Rohstoffen

                            //Titan
                            if ($shipCosts[$shipData->id]->metal > 0) {
                                $build_cnt_metal = floor($cp->getResMetal() / $shipCosts[$shipData->id]->metal);
                            } else {
                                $build_cnt_metal = 99999999999;
                            }

                            //Silizium
                            if ($shipCosts[$shipData->id]->crystal > 0) {
                                $build_cnt_crystal = floor($cp->getResCrystal() / $shipCosts[$shipData->id]->crystal);
                            } else {
                                $build_cnt_crystal = 99999999999;
                            }

                            //PVC
                            if ($shipCosts[$shipData->id]->plastic > 0) {
                                $build_cnt_plastic = floor($cp->getResPlastic() / $shipCosts[$shipData->id]->plastic);
                            } else {
                                $build_cnt_plastic = 99999999999;
                            }

                            //Tritium
                            if ($shipCosts[$shipData->id]->fuel > 0) {
                                $build_cnt_fuel = floor($cp->getResFuel() / $shipCosts[$shipData->id]->fuel);
                            } else {
                                $build_cnt_fuel = 99999999999;
                            }

                            //Nahrung
                            if ($food_costs > 0) {
                                $build_cnt_food = floor($cp->getResFood() / $food_costs);
                            } else {
                                $build_cnt_food = 99999999999;
                            }

                            //Begrente Anzahl baubar
                            if ($shipData->maxCount !== 0) {
                                $max_cnt = $shipData->maxCount - $ship_count;
                            } else {
                                $max_cnt = 99999999999;
                            }

                            //Effetiv max. baubare Schiffe in Betrachtung der Rohstoffe und des Baumaximums
                            $ship_max_build = min($build_cnt_metal, $build_cnt_crystal, $build_cnt_plastic, $build_cnt_fuel, $build_cnt_food, $max_cnt);
                            $bwmsg = [];

                            //Tippbox Nachricht generieren
                            //X Schiffe baubar
                            if ($ship_max_build > 0) {
                                $tm_cnt = "Es k&ouml;nnen maximal " . StringUtils::formatNumber($ship_max_build) . " Schiffe gebaut werden.";
                            } //Zuwenig Rohstoffe. Wartezeit errechnen
                            elseif ($ship_max_build == 0) {
                                $bwait = [];
                                $bwmsg = [];
                                //Wartezeit Titan
                                if ($cp->getProdMetal() > 0) {
                                    $bwait['metal'] = ceil(($shipCosts[$shipData->id]->metal - $cp->getResMetal()) / $cp->getProdMetal() * 3600);
                                    $bwmsg['metal'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($shipCosts[$shipData->id]->metal - $cp->getResMetal()) . " Titan<br />Bereit in " . StringUtils::formatTimespan($bwait['metal']) . "");
                                } else {
                                    $bwait['metal'] = 0;
                                    $bwmsg['metal'] = '';
                                }

                                //Wartezeit Silizium
                                if ($cp->getProdCrystal() > 0) {
                                    $bwait['crystal'] = ceil(($shipCosts[$shipData->id]->crystal - $cp->getResCrystal()) / $cp->getProdCrystal() * 3600);
                                    $bwmsg['crystal'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($shipCosts[$shipData->id]->crystal - $cp->getResCrystal()) . " Silizium<br />Bereit in " . StringUtils::formatTimespan($bwait['crystal']) . "");
                                } else {
                                    $bwait['crystal'] = 0;
                                    $bwmsg['crystal'] = '';
                                }

                                //Wartezeit PVC
                                if ($cp->getProdPlastic() > 0) {
                                    $bwait['plastic'] = ceil(($shipCosts[$shipData->id]->plastic - $cp->getResPlastic()) / $cp->getProdPlastic() * 3600);
                                    $bwmsg['plastic'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($shipCosts[$shipData->id]->plastic - $cp->getResPlastic()) . " PVC<br />Bereit in " . StringUtils::formatTimespan($bwait['plastic']) . "");
                                } else {
                                    $bwait['plastic'] = 0;
                                    $bwmsg['plastic'] = '';
                                }

                                //Wartezeit Tritium
                                if ($cp->getProdFuel() > 0) {
                                    $bwait['fuel'] = ceil(($shipCosts[$shipData->id]->fuel - $cp->getResFuel()) / $cp->getProdFuel() * 3600);
                                    $bwmsg['fuel'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($shipCosts[$shipData->id]->fuel - $cp->getResFuel()) . " Tritium<br />Bereit in " . StringUtils::formatTimespan($bwait['fuel']) . "");
                                } else {
                                    $bwait['fuel'] = 0;
                                    $bwmsg['fuel'] = '';
                                }

                                //Wartezeit Nahrung
                                if ($cp->getProdFood() > 0) {
                                    $bwait['food'] = ceil(($food_costs - $cp->getResFood()) / $cp->getProdFood() * 3600);
                                    $bwmsg['food'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($food_costs - $cp->getResFood()) . " Nahrung<br />Bereit in " . StringUtils::formatTimespan($bwait['food']) . "");
                                } else {
                                    $bwait['food'] = 0;
                                    $bwmsg['food'] = '';
                                }

                                //Maximale Wartezeit ermitteln
                                $bwmax = max($bwait['metal'], $bwait['crystal'], $bwait['plastic'], $bwait['fuel'], $bwait['food']);

                                $tm_cnt = "Rohstoffe verf&uuml;gbar in " . StringUtils::formatTimespan($bwmax) . "";
                            } else {
                                $tm_cnt = "";
                            }

                            //Stellt Rohstoff Rot dar, wenn es von diesem zu wenig auf dem Planeten hat
                            //Titan
                            if ($shipCosts[$shipData->id]->metal > $cp->getResMetal()) {
                                $ress_style_metal = "style=\"color:red;\" " . $bwmsg['metal'] . "";
                            } else {
                                $ress_style_metal = "";
                            }

                            //Silizium
                            if ($shipCosts[$shipData->id]->crystal > $cp->getResCrystal()) {
                                $ress_style_crystal = "style=\"color:red;\" " . $bwmsg['crystal'] . "";
                            } else {
                                $ress_style_crystal = "";
                            }

                            //PVC
                            if ($shipCosts[$shipData->id]->plastic > $cp->getResPlastic()) {
                                $ress_style_plastic = "style=\"color:red;\" " . $bwmsg['plastic'] . "";
                            } else {
                                $ress_style_plastic = "";
                            }

                            //Tritium
                            if ($shipCosts[$shipData->id]->fuel > $cp->getResFuel()) {
                                $ress_style_fuel = "style=\"color:red;\" " . $bwmsg['fuel'] . "";
                            } else {
                                $ress_style_fuel = "";
                            }

                            //Nahrung
                            if ($food_costs > $cp->getResFood()) {
                                $ress_style_food = "style=\"color:red;\" " . $bwmsg['food'] . "";
                            } else {
                                $ress_style_food = "";
                            }

                            // Sicherstellen dass epische Spezialschiffe nur auf dem Hauptplanet gebaut werden
                            if (!$shipData->isSpecial() || $cp->isMainPlanet()) {
                                // Speichert die Anzahl gebauter Schiffe in eine Variable
                                if (isset($shiplist[$shipData->id][$planet->id])) {
                                    $shiplist_count = $shiplist[$shipData->id][$cp->getEntity()->getId()];
                                } else {
                                    $shiplist_count = 0;
                                }

                                // Volle Ansicht
                                if (!$compactView) {
                                    if ($ccnt > 0) {
                                        echo "<tr>
                                                    <td colspan=\"5\" style=\"height:5px;\"></td>
                                            </tr>";
                                    }
                                    $s_img = $shipData->getImagePath('medium');

                                    echo "<tr class='shipRowName'>
                                        <th colspan=\"5\" height=\"20\">" . $shipData->name . "</th>
                                    </tr>
                                    <tr>
                                        <td class='shipCellImage' width=\"120\" height=\"120\" rowspan=\"3\">";

                                    //Bei Spezialschiffen nur Bild ohne Link darstellen
                                    if ($shipData->special) {
                                        echo "<img src=\"" . $s_img . "\" width=\"120\" height=\"120\" border=\"0\" />";
                                    } //Bei normalen Schiffen mit Hilfe verlinken
                                    else {
                                        echo "<a href=\"" . HELP_URL . "&amp;id=" . $shipData->id . "\" title=\"Info zu diesem Schiff anzeigen\">
                                    <img src=\"" . $s_img . "\" width=\"120\" height=\"120\" border=\"0\" /></a>";
                                    }
                                    echo "</td>
                                        <td class='shipCellDescription' colspan=\"4\" valign=\"top\">" . $shipData->shortComment . "</td>
                                    </tr>
                                    <tr>
                                        <th  height=\"30\">Vorhanden:</th>
                                        <td colspan=\"3\">" . StringUtils::formatNumber($shiplist_count) . "</td>
                                    </tr>
                                    <tr>
                                        <th height=\"30\">Bauzeit</th>
                                        <td>" . StringUtils::formatTimespan($btime) . "</td>";

                                    //Maximale Anzahl erreicht
                                    if ($ship_count >= $shipData->maxCount && $shipData->maxCount !== 0) {
                                        echo "<th height=\"30\" colspan=\"2\"><i>Maximalanzahl erreicht</i></th>";
                                    } else {


                                        echo "<th height=\"30\">In Aufrag geben:</th>
                                                <td><input type=\"text\" value=\"0\" name=\"build_count[" . $shipData->id . "]\" id=\"build_count_" . $shipData->id . "\" size=\"4\" maxlength=\"9\" " . tm("", $tm_cnt) . " tabindex=\"" . $tabulator . "\" onkeyup=\"FormatNumber(this.id,this.value, " . $ship_max_build . ", '', '');\"/> St&uuml;ck<br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_" . $shipData->id . "').value=" . $ship_max_build . ";\">max</a>";
                                        if (count($queue) === 0) {
                                            echo '&nbsp;<a href="#changePeople" onclick="javascript:if(document.getElementById(\'changePeople\').style.display==\'none\') {toggleBox(\'changePeople\')};updatePeopleWorkingBox(\'' . $peopleOptimized . '\',\'-1\',\'^-1\');">optimieren</a>';
                                        }


                                        echo "</td>";
                                    }
                                    echo "</tr>";
                                    echo "<tr>
                                    <th height=\"20\" width=\"110\">" . ResourceNames::METAL . ":</th>
                                    <th height=\"20\" width=\"97\">" . ResourceNames::CRYSTAL . ":</th>
                                    <th height=\"20\" width=\"98\">" . ResourceNames::PLASTIC . ":</th>
                                    <th height=\"20\" width=\"97\">" . ResourceNames::FUEL . ":</th>
                                    <th height=\"20\" width=\"98\">" . ResourceNames::FOOD . "</th></tr>";
                                    echo "<tr>
                                    <td height=\"20\" width=\"110\" " . $ress_style_metal . ">
                                        " . StringUtils::formatNumber($shipCosts[$shipData->id]->metal) . "
                                    </td>
                                    <td height=\"20\" width=\"25%\" " . $ress_style_crystal . ">
                                        " . StringUtils::formatNumber($shipCosts[$shipData->id]->crystal) . "
                                    </td>
                                    <td height=\"20\" width=\"25%\" " . $ress_style_plastic . ">
                                        " . StringUtils::formatNumber($shipCosts[$shipData->id]->plastic) . "
                                    </td>
                                    <td height=\"20\" width=\"25%\" " . $ress_style_fuel . ">
                                        " . StringUtils::formatNumber($shipCosts[$shipData->id]->fuel) . "
                                    </td>
                                    <td height=\"20\" width=\"25%\" " . $ress_style_food . ">
                                        " . StringUtils::formatNumber($food_costs) . "
                                    </td>
                                </tr>";
                                } //Einfache Ansicht der Schiffsliste
                                else {
                                    $s_img = $shipData->getImagePath('small');

                                    echo "<tr>
                                        <td class='shipCellImage'>";

                                    //Spezialschiffe ohne Link darstellen
                                    if ($shipData->special) {
                                        echo "<img class='shipImageSmall' src=\"$s_img\" border=\"0\" /></td>";
                                    } //Normale Schiffe mit Link zur Hilfe darstellen
                                    else {
                                        echo "<a href=\"" . HELP_URL . "&amp;id=" . $shipData->id . "\"><img src=\"" . $s_img . "\" class='shipImageSmall' border=\"0\" /></a></td>";
                                    }

                                    echo "<th class='shipCellName' width=\"30%\">
                                            <span style=\"font-weight:500\">" . $shipData->name . "<br/>
                                            Gebaut:</span> " . StringUtils::formatNumber($shiplist_count) . "
                                        </th>
                                        <td width=\"13%\">" . StringUtils::formatTimespan($btime) . "</td>
                                        <td width=\"10%\" " . $ress_style_metal . ">" . StringUtils::formatNumber($shipCosts[$shipData->id]->metal) . "</td>
                                        <td width=\"10%\" " . $ress_style_crystal . ">" . StringUtils::formatNumber($shipCosts[$shipData->id]->crystal) . "</td>
                                        <td width=\"10%\" " . $ress_style_plastic . ">" . StringUtils::formatNumber($shipCosts[$shipData->id]->plastic) . "</td>
                                        <td width=\"10%\" " . $ress_style_fuel . ">" . StringUtils::formatNumber($shipCosts[$shipData->id]->fuel) . "</td>
                                        <td width=\"10%\" " . $ress_style_food . ">" . StringUtils::formatNumber($food_costs) . "</td>";

                                    //Maximale Anzahl erreicht
                                    if ($ship_count >= $shipData->maxCount && $shipData->maxCount !== 0) {
                                        echo "<td>Max</td></tr>";
                                    } else {
                                        echo "<td><input type=\"text\" value=\"0\" id=\"build_count_" . $shipData->id . "\" name=\"build_count[" . $shipData->id . "]\" size=\"5\" maxlength=\"9\" " . tm("", $tm_cnt) . " tabindex=\"" . $tabulator . "\" onkeyup=\"FormatNumber(this.id,this.value, " . $ship_max_build . ", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_" . $shipData->id . "').value=" . $ship_max_build . ";\">max</a></td></tr>";
                                    }
                                }
                                $tabulator++;
                                $cnt++;
                                $ccnt++;
                            }
                        }
                    }

                    // Es können keine Schiffe gebaut werden
                    if ($ccnt == 0) {
                        echo "<tr>
                                        <td colspan=\"9\" height=\"30\" align=\"center\">
                                            Es k&ouml;nnen noch keine Schiffe gebaut werden!<br>
                                            Baue zuerst die ben&ouml;tigten Geb&auml;ude und erforsche die erforderlichen Technologien!
                                        </td>
                                </tr>";
                    }
                } // Es gibt noch keine Schiffe
                else {
                    echo "<tr><td align=\"center\" colspan=\"3\">Es gibt noch keine Schiffe!</td></tr>";
                }

                tableEnd();
            }
            // Baubutton anzeigen
            if ($cnt > 0) {
                echo "<input type=\"submit\" name=\"submit\" value=\"Bauauftr&auml;ge &uuml;bernehmen\"/><br/><br/>";
            }
        }

        return ob_get_clean();
    }
}