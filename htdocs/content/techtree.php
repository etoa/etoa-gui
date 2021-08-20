<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Building\BuildingRequirementRepository;
use EtoA\Building\BuildingSearch;
use EtoA\Building\BuildingSort;
use EtoA\Building\BuildingTypeDataRepository;
use EtoA\Defense\DefenseCategoryRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Defense\DefenseSearch;
use EtoA\Defense\DefenseSort;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipCategoryRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Ship\ShipSort;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologyRequirementRepository;
use EtoA\Technology\TechnologyTypeRepository;

/** @var BuildingDataRepository $buildRepository */
$buildRepository = $app[BuildingDataRepository::class];
/** @var BuildingDataRepository $buildingDataRepository */
$buildingDataRepository = $app[BuildingDataRepository::class];
/** @var TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[TechnologyDataRepository::class];

/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];

/** @var DefenseDataRepository $defenseRepository */
$defenseRepository = $app[DefenseDataRepository::class];

/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];

// Definitionen

if (isset($_GET['mode']) && $_GET['mode'] != "")
    $mode = $_GET['mode'];
else
    $mode = "";


$typeNames = [];
$raceSpecific = [];
$itemNames = [];
if ($mode == "tech") {
    define('NO_ITEMS_MSG', "In dieser Kategorie gibt es keine Technologien!");
    define('HELP_URL', "research");

    /** @var TechnologyRequirementRepository $requirementsRepository */
    $requirementsRepository = $app[TechnologyRequirementRepository::class];
    /** @var TechnologyTypeRepository $technologyTypeRepository */
    $technologyTypeRepository = $app[TechnologyTypeRepository::class];
    $typeNames = $technologyTypeRepository->getTypeNames();
    $groupedNames = [];
    /** @var TechnologyDataRepository $technologyDataRepository */
    $technologyDataRepository = $app[TechnologyDataRepository::class];
    $technologies = $technologyDataRepository->getTechnologies();
    foreach ($technologies as $technology) {
        $groupedNames[$technology->typeId][$technology->id] = $technology->name;
    }
} elseif ($mode == "ships") {
    define('NO_ITEMS_MSG', "In dieser Kategorie gibt es keine Schiffe!");
    define('HELP_URL', "shipyard");

    /** @var ShipRequirementRepository $requirementsRepository */
    $requirementsRepository = $app[ShipRequirementRepository::class];
    /** @var ShipCategoryRepository $shipCategoryRepository */
    $shipCategoryRepository = $app[ShipCategoryRepository::class];
    $typeNames = $shipCategoryRepository->getCategoryNames();
    $groupedNames = [];
    /** @var ShipDataRepository $shipDataRepository */
    $shipDataRepository = $app[ShipDataRepository::class];
    $ships = $shipDataRepository->searchShips(ShipSearch::create()->raceOrNull($cu->raceId)->buildable(), ShipSort::name());
    foreach ($ships as $ship) {
        $groupedNames[$ship->catId][$ship->id] = $ship->name;
        if ($ship->raceId > 0) {
            $raceSpecific[$ship->id] = $ship->raceId;
        }
    }
} elseif ($mode == "defense") {
    define('NO_ITEMS_MSG', "In dieser Kategorie gibt es keine Verteidigungsanlagen!");
    define('HELP_URL', "defense");

    /** @var DefenseRequirementRepository $requirementsRepository */
    $requirementsRepository = $app[DefenseRequirementRepository::class];
    /** @var DefenseCategoryRepository $defenseCategoryRepository */
    $defenseCategoryRepository = $app[DefenseCategoryRepository::class];
    $typeNames = $defenseCategoryRepository->getCategoryNames();
    $groupedNames = [];
    /** @var DefenseDataRepository $defenseDataRepository */
    $defenseDataRepository = $app[DefenseDataRepository::class];
    $defenses = $defenseDataRepository->searchDefense(DefenseSearch::create()->raceOrNull($cu->raceId)->buildable(), DefenseSort::category());
    foreach ($defenses as $defense) {
        $groupedNames[$defense->catId][$defense->id] = $defense->name;
        if ($defense->raceId > 0) {
            $raceSpecific[$defense->id] = $defense->raceId;
        }
    }
} elseif ($mode == "missiles") {
    define('NO_ITEMS_MSG', "In dieser Kategorie gibt es keine Raketen!");
    define('HELP_URL', "missiles");

    /** @var MissileDataRepository $missileDataRepository */
    $missileDataRepository = $app[MissileDataRepository::class];
    $itemNames = $missileDataRepository->getMissileNames();

    /** @var MissileRequirementRepository $requirementsRepository */
    $requirementsRepository = $app[MissileRequirementRepository::class];
} elseif ($mode == "buildings") {
    define('NO_ITEMS_MSG', "In dieser Kategorie gibt es keine Geb&auml;ude!");
    define('HELP_URL', "buildings");

    /** @var BuildingRequirementRepository $requirementsRepository */
    $requirementsRepository = $app[BuildingRequirementRepository::class];
    /** @var BuildingTypeDataRepository $buildingTypeRepository */
    $buildingTypeRepository = $app[BuildingTypeDataRepository::class];
    $typeNames = $buildingTypeRepository->getTypeNames();

    $groupedNames = [];
    $buildings = $buildingDataRepository->searchBuildings(BuildingSearch::create()->show(), BuildingSort::type());
    foreach ($buildings as $building) {
        $groupedNames[$building->typeId][$building->id] = $building->name;
    }
} else {
    throw new \RuntimeException('unknown category');
}

if (isset($cp)) {

    // Daten anzeigen
    echo "<h1>Technikbaum des Planeten " . $cp->name() . "</h1>";

    // Tab-Navigation anzeigen
    show_tab_menu("mode", array(
        "" => "Grafik",
        "buildings" => "Geb&auml;ude",
        "tech" => "Technologien",
        "ships" => "Schiffe",
        "defense" => "Verteidigung",
        "missiles" => "Raketen"
    ));
    echo "<br>";

    if ($mode != "") {

        //
        // Läd alle benötigten Daten
        //

        // Lade Rassennamen
        /** @var RaceDataRepository $raceRepository */
        $raceRepository = $app[RaceDataRepository::class];

        $raceNames = $raceRepository->getRaceNames();

        // Lade Gebäudelistenlevel
        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        $buildlist = $buildingRepository->getBuildingLevels((int) $cp->id);

        // Lade Techlistenlevel
        /** @var TechnologyRepository $technologyRepository */
        $technologyRepository = $app[TechnologyRepository::class];
        $techlist = $technologyRepository->getTechnologyLevels($cu->getId());

        $buildingNames = $buildingDataRepository->getBuildingNames();
        $technologyNames = $technologyDataRepository->getTechnologyNames();

        // Lade Anforderungen
        $b_req = array();
        $requirements = $requirementsRepository->getAll();
        foreach ($requirements->getForAllObjects() as $requirement) {
            if ($requirement->requiredBuildingId > 0) $b_req[$requirement->objectId]['b'][$requirement->requiredBuildingId] = $requirement->requiredLevel;
            if ($requirement->requiredTechnologyId > 0) $b_req[$requirement->objectId]['t'][$requirement->requiredTechnologyId] = $requirement->requiredLevel;
        }

        // Wenn Kategorien vorhanden sind (Gebäude, Forschungen)
        if (count($typeNames) > 0) {
            foreach ($typeNames as $typeId => $typeName) {
                tableStart($typeName);

                $cntr = 0;
                $items = $groupedNames[$typeId] ?? [];
                if (count($items) > 0) {
                    $shipCounts = $shipRepository->getEntityShipCounts($cu->getId(), (int) $cp->id);
                    foreach ($items as $itemId => $itemName) {
                        // Make sure epic special ships are only shown when already built
                        $show = true;
                        if ($mode == "ships" && $typeId === 3) {
                            if (!isset($shipCounts[$itemId])) {
                                $show = false;
                            }
                        }

                        if ($show) {
                            if (isset($b_req[$itemId]['b'])) {
                                $b_cnt = count($b_req[$itemId]['b']);
                            } else {
                                $b_cnt = 0;
                            }

                            if (isset($b_req[$itemId]['t'])) {
                                $t_cnt = count($b_req[$itemId]['t']);
                            } else {
                                $t_cnt = 0;
                            }

                            if ($b_cnt + $t_cnt > 0) {
                                echo "<tr><td width=\"200\" rowspan=\"" . ($b_cnt + $t_cnt) . "\"><b>" . $itemName . "</b> " . helpLink(HELP_URL . "&amp;id=" . $itemId) . "";
                            } else {
                                echo "<tr><td width=\"200\"><b>" . $itemName . "</b> " . helpLink(HELP_URL . "&amp;id=" . $itemId) . "";
                            }

                            if (isset($raceSpecific[$itemId])) {
                                echo "<br/>" . $raceNames[$raceSpecific[$itemId]] . "</td>";
                            } else {
                                echo "</td>";
                            }

                            $using_something = 0;
                            if (isset($b_req[$itemId]['b']) && count($b_req[$itemId]['b']) > 0) {
                                $cnt = 0;
                                foreach ($b_req[$itemId]['b'] as $b => $l) {
                                    if ($cnt == 0 && count($b_req[$itemId]['b']) > 1) {
                                        $bstyle = "border-bottom:none;";
                                    } elseif (
                                        ($cnt > 0
                                            && $cnt < count($b_req[$itemId]['b']) - 1)
                                        ||
                                        (isset($b_req[$itemId]['t'])
                                            && count($b_req[$itemId]['t']) > 0)
                                    ) {
                                        $bstyle = "border-top:none;border-bottom:none;";
                                    } elseif ($cnt != 0) {
                                        $bstyle = "border-top:none;";
                                    } else {
                                        $bstyle = "";
                                    }

                                    if (!isset($buildlist[$b]) || $buildlist[$b] < $l) {
                                        echo "<td style=\"color:#f00;border-right:none;" . $bstyle . "\" width=\"130\">" . $buildingNames[$b] . "</td><td style=\"color:#f00;border-left:none;" . $bstyle . "\" width=\"70\">Stufe " . $l . "</td></tr>";
                                    } else {
                                        echo "<td style=\"color:#0f0;border-right:none;" . $bstyle . "\" width=\"130\">" . $buildingNames[$b] . "</td><td style=\"color:#0f0;border-left:none;" . $bstyle . "\" width=\"70\">Stufe $l</td></tr>";
                                    }
                                    $cnt++;
                                }
                                $using_something = 1;
                            }

                            if (isset($b_req[$itemId]['t']) && count($b_req[$itemId]['t']) > 0) {
                                $cnt = 0;
                                foreach ($b_req[$itemId]['t'] as $b => $l) {
                                    if ($cnt == 0 && count($b_req[$itemId]['t']) > 1 && isset($b_req[$itemId]['b']) && count($b_req[$itemId]['b']) > 0) {
                                        $bstyle = "border-top:none;border-bottom:none;";
                                    } elseif ($cnt == 0 && count($b_req[$itemId]['t']) > 1) {
                                        $bstyle = "border-bottom:none;";
                                    } elseif (($cnt > 0 && $cnt < count($b_req[$itemId]['t']) - 1)) {
                                        $bstyle = "border-top:none;border-bottom:none;";
                                    } elseif ($cnt != 0) {
                                        $bstyle = "border-top:none;";
                                    } elseif (count($b_req[$itemId]['b']) > 0) {
                                        $bstyle = "border-top:none;";
                                    } else {
                                        $bstyle = "";
                                    }


                                    if (!isset($techlist[$b]) || $techlist[$b] < $l) {
                                        echo "<td style=\"color:#f00;border-right:none;" . $bstyle . "\" width=\"130\">" . $technologyNames[$b] . "</td><td style=\"color:#f00;border-left:none;" . $bstyle . "\" width=\"70\">Stufe " . $l . "</td></tr>";
                                    } else {
                                        echo "<td style=\"color:#0f0;border-right:none;" . $bstyle . "\" width=\"130\">" . $technologyNames[$b] . "</td><td style=\"color:#0f0;border-left:none;" . $bstyle . "\" width=\"70\">Stufe " . $l . "</td></tr>";
                                    }
                                    $cnt++;
                                }
                                $using_something = 1;
                            }

                            if ($using_something == 0) {
                                echo "<td colspan=\"2\"><i>Keine Voraussetzungen n&ouml;tig</i></td></tr>";
                            }
                            $cntr++;
                        }
                    }
                    if ($cntr == 0) {
                        echo "<tr><td colspan=\"2\">Keine Infos vorhanden!</td></tr>";
                    }
                } else
                    echo "<tr><td align=\"center\" colspan=\"3\">" . NO_ITEMS_MSG . "</td></tr>";

                tableEnd();
            }
        }
        // Wenn keine Kategorien vorhanden sind (Raketen)
        else {
            tableStart();
            if (count($itemNames) > 0) {
                foreach ($itemNames as $itemId => $itemName) {
                    if (count($b_req[$itemId]['b']) + count($b_req[$itemId]['t']) > 0) {
                        echo "<tr><td width=\"200\" rowspan=\"" . (count($b_req[$itemId]['b']) + count($b_req[$itemId]['t'])) . "\"><b>" . $itemName . "</b> " . helpLink(HELP_URL . "&amp;id=" . $itemId) . "</td>";
                    } else {
                        echo "<tr><td width=\"200\"><b>" . $itemName . "</b> " . helpLink(HELP_URL . "&amp;id=" . $itemId) . "</td>";
                    }
                    $using_something = 0;

                    if (isset($b_req[$itemId]['b']) && count($b_req[$itemId]['b']) > 0) {
                        $cnt = 0;
                        foreach ($b_req[$itemId]['b'] as $b => $l) {
                            if ($cnt == 0 && count($b_req[$itemId]['b']) > 1) {
                                $bstyle = "border-bottom:none;";
                            } elseif (($cnt > 0 && $cnt < count($b_req[$itemId]['b']) - 1) || count($b_req[$itemId]['t']) > 0) {
                                $bstyle = "border-top:none;border-bottom:none;";
                            } elseif ($cnt != 0) {
                                $bstyle = "border-top:none;";
                            } else {
                                $bstyle = "";
                            }

                            if (!isset($buildlist[$b]) || $buildlist[$b] < $l) {
                                echo "<td style=\"color:#f00;border-right:none;$bstyle\" width=\"130\">" . $buildingNames[$b] . "</td><td style=\"color:#f00;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
                            } else {
                                echo "<td style=\"color:#0f0;border-right:none;$bstyle\" width=\"130\">" . $buildingNames[$b] . "</td><td style=\"color:#0f0;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
                            }
                            $cnt++;
                        }
                        $using_something = 1;
                    }

                    if (isset($b_req[$itemId]['t']) && count($b_req[$itemId]['t']) > 0) {
                        $cnt = 0;
                        foreach ($b_req[$itemId]['t'] as $b => $l) {
                            if ($cnt == 0 && count($b_req[$itemId]['t']) > 1 && count($b_req[$itemId]['b']) > 0) {
                                $bstyle = "border-top:none;border-bottom:none;";
                            } elseif ($cnt == 0 && count($b_req[$itemId]['t']) > 1) {
                                $bstyle = "border-bottom:none;";
                            } elseif (($cnt > 0 && $cnt < count($b_req[$itemId]['t']) - 1)) {
                                $bstyle = "border-top:none;border-bottom:none;";
                            } elseif ($cnt != 0) {
                                $bstyle = "border-top:none;";
                            } elseif (count($b_req[$itemId]['b']) > 0) {
                                $bstyle = "border-top:none;";
                            } else {
                                $bstyle = "";
                            }


                            if (!isset($techlist[$b]) || $techlist[$b] < $l) {
                                echo "<td style=\"color:#f00;border-right:none;$bstyle\" width=\"130\">" . $technologyNames[$b] . "</td><td style=\"color:#f00;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
                            } else {
                                echo "<td style=\"color:#0f0;border-right:none;$bstyle\" width=\"130\">" . $technologyNames[$b] . "</td><td style=\"color:#0f0;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
                            }
                            $cnt++;
                        }
                        $using_something = 1;
                    }
                    if ($using_something == 0)
                        echo "<td colspan=\"2\"><i>Keine Voraussetzungen n&ouml;tig</i></td></tr>";
                }
            } else
                echo "<tr><td align=\"center\" colspan=\"3\">" . NO_ITEMS_MSG . "</td></tr>";
            tableEnd();
        }
    } else {
        $startItem = 6;

        echo "<select onchange=\"xajax_reqInfo(this.value,'b')\">
        <option value=\"0\">Gebäude wählen...</option>";
        $buildingNames = $buildingDataRepository->getBuildingNames();
        foreach ($buildingNames as $buildingId => $buildingName) {
            echo "<option value=\"" . $buildingId . "\">" . $buildingName . "</option>";
        }
        echo "</select> ";


        echo "<select onchange=\"xajax_reqInfo(this.value,'t')\">
        <option value=\"0\">Technologie wählen...</option>";
        $technologyNames = $technologyDataRepository->getTechnologyNames();
        foreach ($technologyNames as $technologyId => $technologyName) {
            echo "<option value=\"" . $technologyId . "\">" . $technologyName . "</option>";
        }
        echo "</select> ";

        echo "<select onchange=\"xajax_reqInfo(this.value,'s')\">
        <option value=\"0\">Schiff wählen...</option>";
        $shipNames = $shipDataRepository->getShipNames();
        foreach ($shipNames as $shipId => $shipName) {
            echo "<option value=\"" . $shipId . "\">" . $shipName . "</option>";
        }
        echo "</select> ";

        echo "<select onchange=\"xajax_reqInfo(this.value,'d')\">
        <option value=\"0\">Verteidigung wählen...</option>";
        $defenseNames = $defenseRepository->getDefenseNames();
        foreach ($defenseNames as $defenseId => $defenseName) {
            echo "<option value=\"" . $defenseId . "\">" . $defenseName . "</option>";
        }
        echo "</select><br/><br/>";

        iBoxStart("Grafische Darstellung");
        showTechTree("b", $startItem);
        iBoxEnd();
    }
}
