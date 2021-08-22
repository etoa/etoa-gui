<?PHP

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceSpendRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Alliance\Base\AllianceBase;
use EtoA\Alliance\Base\AllianceItemBuildStatus;
use EtoA\Alliance\Base\AllianceItemRequirementStatus;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetStatus;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListItemCount;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\StringUtils;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];
/** @var AllianceSpendRepository $allianceSpendRepository */
$allianceSpendRepository = $app[AllianceSpendRepository::class];
/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];
/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];
/** @var ShipQueueRepository $shipQueueRepository */
$shipQueueRepository = $app[ShipQueueRepository::class];
/** @var FleetRepository $fleetRepository */
$fleetRepository = $app[FleetRepository::class];
/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var AllianceTechnologyRepository $allianceTechnologyRepository */
$allianceTechnologyRepository = $app[AllianceTechnologyRepository::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var AllianceBase $allianceBase */
$allianceBase = $app[AllianceBase::class];
/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

$planet = $planetRepo->find($cp->id);
$technologies = $allianceTechnologyRepository->findAll();
$buildings = $allianceBuildingRepository->findAll();

// Zeigt eigene Rohstoffe an
echo $resourceBoxDrawer->getHTML($planet);

echo "<h2><a href=\"?page=" . $page . "&amp;action=" . $_GET['action'] . "\">Allianzbasis</a></h2>";

// Schiffswerft gebaut?
$allianceShipyardLevel = $allianceBuildingRepository->getLevel($cu->allianceId(), AllianceBuildingId::SHIPYARD);
$allianceResearchLevel = $allianceBuildingRepository->getLevel($cu->allianceId(), AllianceBuildingId::RESEARCH);

//
// Navigation
//

// Speichert Tab
if (isset($_GET['action2'])) {
    $action2 = $_GET['action2'];
} else {
    $action2 = "buildings";
}

// Stellt standart Links dar
/*
echo "<a href=\"javascript:;\" onclick=\"showTab('tabBuildings')\">Gebäude</a> |
<a href=\"javascript:;\" onclick=\"showTab('tabResearch')\">Technologien</a> |
<a href=\"javascript:;\" onclick=\"showTab('tabStorage')\">Speicher</a>";
*/

$ddm = new DropdownMenu(1);
$ddm->add('b', 'Gebäude', "showTab('tabBuildings');");
if ($allianceResearchLevel > 0) {
    $ddm->add('r', 'Technologien', "showTab('tabResearch');");
}
$ddm->add('s', 'Speicher', "showTab('tabStorage');");
if ($allianceShipyardLevel > 0) {
    $ddm->add('sw', 'Schiffswerft', "showTab('tabShipyard');");
}
echo $ddm;

echo "<br>";


//
// Funktionen
//

echo "<script type=\"text/javascript\">

// Wechselt zwischen den Verschiedenen Tabs
function showTab(idx)
{
    document.getElementById('tabBuildings').style.display='none';
    document.getElementById('tabResearch').style.display='none';
    document.getElementById('tabStorage').style.display='none';
    document.getElementById('tabShipyard').style.display='none';

    document.getElementById(idx).style.display='';
}

// Schreibt definierte Zahlen in die Einzahlen-Felder und wechselt auf diese Seite
function setSpends(metal, crystal, plastic, fuel, food)
{
    document.getElementById('spend_metal').value=metal;
    document.getElementById('spend_crystal').value=crystal;
    document.getElementById('spend_plastic').value=plastic;
    document.getElementById('spend_fuel').value=fuel;
    document.getElementById('spend_food').value=food;

    // Wechselt Tab
    showTab('tabStorage');

    // Wenn zu wenig Rohstoffe auf dem aktuellen Planeten sind, wird eine Nachricht ausgegeben
    if(" . $cp->resMetal . "<metal
            || " . $cp->resCrystal . "<crystal
            || " . $cp->resPlastic . "<plastic
            || " . $cp->resFuel . "<fuel
            || " . $cp->resFood . "<food)
    {
        alert('Du hast nicht genügend Rohstoffe auf dem aktuellen Planeten!');
    }
}

// Ändert Rohstoff Box Zahlen
function changeResBox(metal, crystal, plastic, fuel, food)
{
    document.getElementById('resBoxMetal').innerHTML=FormatNumber('return',metal,'','','');
    document.getElementById('resBoxCrystal').innerHTML=FormatNumber('return',crystal,'','','');
    document.getElementById('resBoxPlastic').innerHTML=FormatNumber('return',plastic,'','','');
    document.getElementById('resBoxFuel').innerHTML=FormatNumber('return',fuel,'','','');
    document.getElementById('resBoxFood').innerHTML=FormatNumber('return',food,'','','');
}

</script>";


//
// Einzahlen
//

if (isset($_POST['storage_submit']) && checker_verify()) {
    // Formatiert Eingaben
    $resources = new BaseResources();
    $resources->metal = (int) StringUtils::parseFormattedNumber($_POST['spend_metal']);
    $resources->crystal = (int) StringUtils::parseFormattedNumber($_POST['spend_crystal']);
    $resources->plastic = (int) StringUtils::parseFormattedNumber($_POST['spend_plastic']);
    $resources->fuel = (int) StringUtils::parseFormattedNumber($_POST['spend_fuel']);
    $resources->food = (int) StringUtils::parseFormattedNumber($_POST['spend_food']);

    // Prüft, ob Rohstoffe angegeben wurden
    if ($resources->getSum() > 0) {
        // Prüft, ob Rohstoffe noch vorhanden sind
        if (
            $cp->getRes(1) >= $resources->metal
            && $cp->getRes(2) >= $resources->crystal
            && $cp->getRes(3) >= $resources->plastic
            && $cp->getRes(4) >= $resources->fuel
            && $cp->getRes(5) >= $resources->food
        ) {
            // Rohstoffe vom Planet abziehen
            $planetRepo->removeResources($cp->id(), $resources);
            $cp->reloadRes();

            // Rohstoffe der Allianz gutschreiben
            $cu->alliance->changeRes($resources->metal, $resources->crystal, $resources->plastic, $resources->fuel, $resources->food);

            // Spende speichern
            $allianceSpendRepository->addEntry($cu->allianceId(), $cu->getId(), $resources);
            success_msg("Rohstoffe erfolgreich eingezahlt!");
        } else
            error_msg("Es sind zu wenig Rohstoffe auf dem Planeten!");
    } else
        error_msg("Du hast keine Rohstoffe angegeben!");
}

// Einzahlungs Filter aktivieren

// Default Werte setzen
$sum = false;
$limit = 10;
$user = 0;
if (isset($_POST['filter_submit']) && checker_verify()) {
    // Summierung der Einzahlungen
    if (isset($_POST['output']) && $_POST['output'] == 1) {
        $sum = true;
    }

    // Limit
    if (isset($_POST['limit']) && $_POST['limit'] > 0) {
        $limit = $_POST['limit'];
    }

    // User
    if (isset($_POST['user_spends']) && $_POST['user_spends'] > 0) {
        $user = $_POST['user_spends'];
    }
}

//
// Läd Daten
//

// Allianzschiffe (wenn Schiffswerft gebaut)
/** @var EtoA\Ship\Ship[] $ships */
$ships = [];
if ($allianceShipyardLevel > 0) {
    $allianceShips = $shipDataRepository->getAllianceShips();
    foreach ($allianceShips as $ship) {
        if ($ship->allianceShipyardLevel <= $allianceShipyardLevel) {
            $ships[$ship->id] = $ship;
        }
    }
}

// Userschiffe laden (wenn Schiffswerft gebaut=
// Gebaute Schiffe laden
$shiplist = array_map(fn (ShipListItemCount $count) => $count->sum(), $shipRepository->getUserShipCounts($cu->getId()));

// Bauliste von allen Planeten laden und nach Schiffe zusammenfassen
$queue_total = $shipQueueRepository->getUserQueuedShipCounts($cu->getId());

// Flotten laden und nach Schiffe zusammenfassen
$fleet = $fleetRepository->getUserFleetShipCounts($cu->getId());

//
// Schiffe kaufen
//

$ship_costed = 0;
if (isset($_POST['ship_submit']) && checker_verify()) {
    if ($cu->alliance->checkActionRightsNA(AllianceRights::BUILD_MINISTER) || $cu->id == $_POST['user_buy_ship']) {
        // Prüft, ob ein User gewählt wurde
        if ($_POST['user_buy_ship'] > 0) {
            // Gebaute Schiffe laden
            $shiplist = array_map(fn (ShipListItemCount $count) => $count->sum(), $shipRepository->getUserShipCounts($_POST['user_buy_ship']));

            // Bauliste von allen Planeten laden und nach Schiffe zusammenfassen
            $queue_total = $shipQueueRepository->getUserQueuedShipCounts($_POST['user_buy_ship']);

            // Flotten laden und nach Schiffe zusammenfassen
            $fleet = $fleetRepository->getUserFleetShipCounts($_POST['user_buy_ship']);

            $ship_costs = 0;
            $total_build_cnt = 0;
            $to_much = false;
            foreach ($_POST['buy_ship'] as $ship_id => $build_cnt) {
                // Formatiert die eingegebene Zahl (entfernt z.B. die Trennzeichen)
                $build_cnt = StringUtils::parseFormattedNumber($build_cnt);

                if ($build_cnt > 0) {
                    // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
                    $ship_count = 0;
                    // ... auf den Planeten
                    if (isset($shiplist[$ship_id])) {
                        $ship_count += $shiplist[$ship_id];
                    }
                    // ... in der Bauliste
                    if (isset($queue_total[$ship_id])) {
                        $ship_count += $queue_total[$ship_id];
                    }
                    // ... in der Luft
                    if (isset($fleet[$ship_id])) {
                        $ship_count += $fleet[$ship_id];
                    }

                    // Total Schiffe mit den zu bauenden
                    $total_count = $build_cnt + $ship_count;

                    // Prüft ob Anzahl grösser ist als Schiffsmaximum
                    if ($ships[$ship_id]->maxCount >= $total_count || $ships[$ship_id]->maxCount === 0) {
                        for ($i = $build_cnt - 1; $i >= 0; $i--) {
                            //Kostenfaktor Schiffe
                            $cost_factor = pow($config->getFloat("alliance_shipcosts_factor"), $ship_count + $i);
                            // Berechnet die Kosten
                            $ship_costs += $cost_factor * $ships[$ship_id]->allianceCosts;
                        }
                    }
                    // Die Anzahl übersteigt die Max. Anzahl -> Nachricht wird ausgegeben
                    else {
                        $to_much = true;
                    }
                    $total_build_cnt += $build_cnt;
                }
            }

            // Prüft, ob die Maximalanzahl nicht überschritten wird
            if (!$to_much) {


                if ($total_build_cnt > 0) {
                    // Prüft ob Schiffspunkte noch ausreichend sind
                    if ($cu->alliance->members[$_POST['user_buy_ship']]->allianceShippoints >= $ship_costs) {
                        // Zieht Punkte vom Konto ab
                        $userRepository->markAllianceShipPointsAsUsed($_POST['user_buy_ship'], $ship_costs);
                        $ship_costed = $ship_costs;

                        // Lädt das Allianzentity
                        $allianceMarketId = $entityRepository->getAllianceMarketId();

                        // Speichert Flotte
                        $launchtime = time(); // Startzeit
                        $duration = 3600; // Dauer 1h
                        $landtime = $launchtime + $duration; // Landezeit
                        $fleetId = $fleetRepository->add($_POST['user_buy_ship'], $launchtime, $landtime, $allianceMarketId, $cp->id, \EtoA\Fleet\FleetAction::DELIVERY, FleetStatus::DEPARTURE, new BaseResources());

                        // Speichert Schiffe in der Flotte
                        $sql = "";
                        $log = "";
                        $cnt = 0;
                        foreach ($_POST['buy_ship'] as $ship_id => $build_cnt) {
                            // Formatiert die eingegebene Zahl (entfernt z.B. die Trennzeichen)
                            $build_cnt = (int) StringUtils::parseFormattedNumber($build_cnt);

                            if ($build_cnt > 0) {
                                $fleetRepository->addShipsToFleet($fleetId, $ship_id, $build_cnt);
                                // Stellt SQL-String her
                                if ($cnt == 0) {
                                    $fleet[$ship_id] = ($fleet[$ship_id] ?? 0) + $build_cnt;
                                    // Gibt einmalig eine OK-Medlung aus
                                    success_msg("Schiffe wurden erfolgreich hergestellt!");
                                }

                                // Listet gewählte Schiffe für Log auf
                                $log .= "[b]" . $_POST['ship_name_' . $ship_id . ''] . ":[/b] " . StringUtils::formatNumber($build_cnt) . "\n";

                                $cnt++;
                            }
                        }

                        // Zur Allianzgeschichte hinzufügen
                        /** @var AllianceHistoryRepository $allianceHistoryRepository */
                        $allianceHistoryRepository = $app[AllianceHistoryRepository::class];
                        $allianceHistoryRepository->addEntry((int) $cu->allianceId, "Folgende Schiffe wurden für [b]" . get_user_nick($_POST['user_buy_ship']) . "[/b] hergestellt:\n" . $log . "\n" . StringUtils::formatNumber($ship_costs) . " Teile wurden dafür benötigt.");
                    } else {
                        error_msg("Der gewählte User hat nicht genügend Teile übrig!");
                    }
                } else {
                    error_msg("Keine Schiffe ausgewählt!");
                }
            } else {
                error_msg("Die Maximalanzahl der Schiffe würde mit der eingegebenen Menge überschritten werden!");
            }
        } else {
            error_msg("Es wurde kein User ausgewählt!");
        }
    } else {
        error_msg("Keine Berechtigung!");
    }
}


//
// ResBox
//

$style0 = "";
$style1 = "";
$style2 = "";
$style3 = "";
$style4 = "";

// Negative Rohstoffe farblich hervorben
if ($cu->alliance->resMetal < 0) {
    $style0 = "style=\"color:red;\"";
}
if ($cu->alliance->resCrystal < 0) {
    $style1 = "style=\"color:red;\"";
}
if ($cu->alliance->resPlastic < 0) {
    $style2 = "style=\"color:red;\"";
}
if ($cu->alliance->resFuel < 0) {
    $style3 = "style=\"color:red;\"";
}
if ($cu->alliance->resFood < 0) {
    $style4 = "style=\"color:red;\"";
}


tableStart("Allianz Ressourcen");
echo "<tr>
                <th style=\"width:20%;vertical-align:middle;\">" . RES_ICON_METAL . " " . RES_METAL . "</th>
                <th style=\"width:20%;vertical-align:middle;\">" . RES_ICON_CRYSTAL . " " . RES_CRYSTAL . "</th>
                <th style=\"width:20%;vertical-align:middle;\">" . RES_ICON_PLASTIC . " " . RES_PLASTIC . "</th>
                <th style=\"width:20%;vertical-align:middle;\">" . RES_ICON_FUEL . " " . RES_FUEL . "</th>
                <th style=\"width:20%;vertical-align:middle;\">" . RES_ICON_FOOD . " " . RES_FOOD . "</th>
            </tr>
            <tr>
                <td " . $style0 . " id=\"resBoxMetal\">" . StringUtils::formatNumber($cu->alliance->resMetal) . " t</td>
                <td " . $style1 . " id=\"resBoxCrystal\">" . StringUtils::formatNumber($cu->alliance->resCrystal) . " t</td>
                <td " . $style2 . "id=\"resBoxPlastic\">" . StringUtils::formatNumber($cu->alliance->resPlastic) . " t</td>
                <td " . $style3 . "id=\"resBoxFuel\">" . StringUtils::formatNumber($cu->alliance->resFuel) . " t</td>
                <td " . $style4 . "id=\"resBoxFood\">" . StringUtils::formatNumber($cu->alliance->resFood) . " t</td>
            </tr>";
tableEnd();







//
// Content Laden
//


//
// Datenverarbeitung 2: Muss nach dem Laden der Daten geschehen
// -> Gebäude und Techs speichern
//

// Gebäude in Auftrag geben
if (isset($_POST['building_submit']) && checker_verify()) {
    $allianceUser = $userRepository->getUser($cu->getId());
    if (Alliance::checkActionRights(AllianceRights::BUILD_MINISTER)) {
        if (isset($_POST['building_id']) && $_POST['building_id'] != 0) {
            $buildingId = $request->request->getInt('building_id');
            try {
                $alliance = $allianceRepository->getAlliance($cu->allianceId());
                $building = $buildings[$buildingId];
                $buildingList = $allianceBuildingRepository->getBuildList($alliance->id);
                $allianceBase->buildBuilding($allianceUser, $alliance, $building, $buildingList[$buildingId] ?? null, AllianceItemRequirementStatus::createForBuildings($buildings, $buildingList));
                success_msg("Gebäude wurde erfolgreich in Auftrag gegeben!");
            } catch (\RuntimeException $e) {
                error_msg($e->getMessage());
            }
        }
    }
}


// Technologie in Auftrag geben
if (isset($_POST['research_submit']) && checker_verify()) {
    $allianceUser = $userRepository->getUser($cu->getId());
    if (Alliance::checkActionRights(AllianceRights::BUILD_MINISTER)) {
        if (isset($_POST['research_id']) && $_POST['research_id'] != 0) {
            $technologyId = $request->request->getInt('research_id');
            try {
                $alliance = $allianceRepository->getAlliance($cu->allianceId());
                $technology = $technologies[$technologyId];
                $technologyList = $allianceTechnologyRepository->getTechnologyList($alliance->id);
                $allianceBase->buildTechnology($allianceUser, $alliance, $technology, $technologyList[$technologyId] ?? null, AllianceItemRequirementStatus::createForTechnologies($technologies, $technologyList));
                success_msg("Forschung wurde erfolgreich in Auftrag gegeben!");
            } catch (\RuntimeException $e) {
                error_msg($e->getMessage());
            }
        }
    }
}

$alliance = $allianceRepository->getAlliance($cu->allianceId());
$allianceResources = $alliance->getResources();

$resName = array(
    0 => RES_METAL,
    1 => RES_CRYSTAL,
    2 => RES_PLASTIC,
    3 => RES_FUEL,
    4 => RES_FOOD
);


//
// Gebäude
//

if ($action2 == "buildings") {
    $display = "";
} else {
    $display = "none";
}
echo "<div id=\"tabBuildings\" style=\"display:" . $display . ";\">";
echo "<form action=\"?page=" . $page . "&amp;action=" . $_GET['action'] . "&amp;action2=buildings\" method=\"post\" id=\"alliance_buildings\">\n";
$cstr = checker_init();

// Mit diesem Feld wird die Gebäude ID vor dem Absenden übergeben
echo "<input type=\"hidden\" value=\"0\" name=\"building_id\" id=\"building_id\" />";

// Es sind Gebäude vorhanden
$buildingList = $allianceBuildingRepository->getBuildList($alliance->id);
if (count($buildings) > 0) {
    $requirementStatus = AllianceItemRequirementStatus::createForBuildings($buildings, $buildingList);
    foreach ($buildings as $building) {
        $currentBuildingListItem = $buildingList[$building->id] ?? null;
        $itemStatus = $allianceBase->getBuildingBuildStatus($alliance, $building, $currentBuildingListItem, $requirementStatus);

        if ($itemStatus->status === AllianceItemBuildStatus::STATUS_MISSING_REQUIREMENTS) {
            continue;
        }

        $style_message = '';
        $level = $currentBuildingListItem !== null ? $currentBuildingListItem->level : null;
        tableStart($building->name . ' <span id="buildlevel">' . ($level > 0 ? $level : '') . '</span>');

        echo "<tr>
            <td style=\"width:120px;background:#000;vertical-align:middle;padding:0px;\">
            <img src=\"" . $building->getImagePath() . "\" style=\"width:120px;height:120px;\" alt=\"" . $building->name . "\"/>
            </td>
            <td style=\"vertical-align:top;height:100px;\" colspan=\"6\">
            " . $building->longComment . "
            </td>
                </tr>";
            //
            // Baumenü
            //

        echo "<tr>";
        if ($itemStatus->status === AllianceItemBuildStatus::STATUS_MAX_LEVEL) {
            echo "<td colspan=\"7\" style=\"text-align:center;\">Maximallevel erreicht!</td>";
        } else {
            $costs = $building->calculateCosts($level + 1, $alliance->memberCount, $config->getFloat('alliance_membercosts_factor'));
            $style = array_fill(0, count($resName), '');

            $message = '';
            $style_message = '';
            switch ($itemStatus->status) {
                case AllianceItemBuildStatus::STATUS_ITEM_UNDER_CONSTRUCTION:
                    $style_message = "color: rgb(0, 255, 0);";
                    $message = startTime($currentBuildingListItem->buildEndTime - time(), 'build_message_building_' . $building->id . '', 0, 'Wird ausgebaut auf Stufe ' . ($level + 1) . ' (TIME)');
                    break;
                case AllianceItemBuildStatus::STATUS_UNDER_CONSTRUCTION:
                    $message = "Es wird bereits gebaut!";
                    $style_message = "color: rgb(255, 0, 0);";
                    break;
                case AllianceItemBuildStatus::STATUS_MISSING_RESOURCE:
                    $need = $itemStatus->missingResources;
                    $message = "<input type=\"button\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Fehlende Rohstoffe einzahlen\" " . tm("Nicht genügend Rohstoffe", "Es sind nicht genügend Rohstoffe vorhanden!<br>Klick auf den Button um die fehlenden Rohstoffe einzuzahlen.") . " onclick=\"setSpends(" . $need->metal . ", " . $need->crystal . ", " . $need->plastic . ", " . $need->fuel . ", " . $need->food . ");\"/>";
                    foreach ($resName as $id => $resourceName) {
                        if ($need->get($id) > 0) {
                            $style[$id] = "style=\"color:red;\" " . tm("Fehlender Rohstoff", "" . StringUtils::formatNumber($need->get($id)) . " " . $resourceName . "") . "";
                        }
                    }
                    break;
                case AllianceItemBuildStatus::STATUS_OK;
                    $build_button = $level === 0 ? "Bauen" : "Ausbauen";

                    // Generiert Baubutton, mit welchem vor dem Absenden noch die Objekt ID übergeben wird
                    $message = "<input type=\"submit\" class=\"button\" name=\"building_submit\" id=\"building_submit\" value=\"" . $build_button . "\" onclick=\"document.getElementById('building_id').value=" . $building->id . ";\"/>";
                    break;
            }

            echo "<th width=\"7%\">Stufe</th>
                <th width=\"18%\">Zeit</th>
                <th width=\"15%\">" . RES_METAL . "</th>
                <th width=\"15%\">" . RES_CRYSTAL . "</th>
                <th width=\"15%\">" . RES_PLASTIC . "</th>
                <th width=\"15%\">" . RES_FUEL . "</th>
                <th width=\"15%\">" . RES_FOOD . "</th>
            </tr><tr>
                <td width=\"7%\">" . ($level + 1) . "</th>
                <td width=\"18%\">" . StringUtils::formatTimespan($building->calculateBuildTime($level+ 1)) . "</th>
                <td " . $style[0] . " width=\"15%\">" . StringUtils::formatNumber($costs->metal) . "</td>
                <td " . $style[1] . " width=\"15%\">" . StringUtils::formatNumber($costs->crystal) . "</td>
                <td " . $style[2] . " width=\"15%\">" . StringUtils::formatNumber($costs->plastic) . "</td>
                <td " . $style[3] . " width=\"15%\">" . StringUtils::formatNumber($costs->fuel) . "</td>
                <td " . $style[4] . " width=\"15%\">" . StringUtils::formatNumber($costs->food) . "</td>
            </tr>
            <tr>
                <td colspan=\"7\" style=\"text-align:center;" . $style_message . "\" name=\"build_message_building_" . $building->id . "\" id=\"build_message_building_" . $building->id . "\">" . $message . "</td>";
        }
        echo "</tr>";
        tableEnd();
    }
}
// Es sind noch keine Gebäude vorhanden
else {
    error_msg("Es sind noch keine Gebäude definiert!");
}

echo "</form>";
echo "</div>";



//
// Forschungen
//


if ($action2 == "research") {
    $display = "";
} else {
    $display = "none";
}
echo "<div id=\"tabResearch\" style=\"display:" . $display . ";\">";
echo "<form action=\"?page=" . $page . "&amp;action=" . $_GET['action'] . "&amp;action2=research\" method=\"post\" id=\"alliance_research\">\n";
echo $cstr;

// Mit diesem Feld wird die Tech ID vor dem Absenden übergeben
echo "<input type=\"hidden\" value=\"0\" name=\"research_id\" id=\"research_id\" />";

// Es sind Technologien vorhanden
// Es sind Gebäude vorhanden
$technologyList = $allianceTechnologyRepository->getTechnologyList($alliance->id);
if ($allianceResearchLevel > 0 && count($technologies) > 0) {
    $requirementStatus = AllianceItemRequirementStatus::createForTechnologies($technologies, $technologyList);
    foreach ($technologies as $technology) {
        $currentTechnologyListItem = $technologyList[$technology->id] ?? null;
        $itemStatus = $allianceBase->getTechnologyBuildStatus($alliance, $technology, $currentTechnologyListItem, $requirementStatus);
        if ($itemStatus->status === AllianceItemBuildStatus::STATUS_MISSING_REQUIREMENTS) {
            continue;
        }

        $level = $currentTechnologyListItem !== null ? $technologyList[$technology->id]->level : 0;
        tableStart($technology->name . ' <span id="buildlevel">' . (($level > 0) ? $level : '') . '</span>');

        echo "<tr>
            <td style=\"width:120px;background:#000;vertical-align:middle;padding:0px;\">"
            . '<img src="' . $technology->getImagePath() . '" style="width:120px;height:120px;" alt="' . $technology->name . '"/>
            </td>
            <td style="vertical-align:top;height:100px;" colspan="6">
            ' . $technology->longComment . "
            </td>
                </tr>";

        //
        // Baumenü
        //
        echo "<tr>";
        if ($itemStatus->status === AllianceItemBuildStatus::STATUS_MAX_LEVEL)
            echo "<td colspan=\"7\" style=\"text-align:center;\">Maximallevel erreicht!</td>";
        else {
            $costs = $technology->calculateCosts($level + 1, $alliance->memberCount, $config->getFloat('alliance_membercosts_factor'));
            $style = array_fill(0, count($resName), '');

            $message = '';
            $style_message = '';
            switch ($itemStatus->status) {
                case AllianceItemBuildStatus::STATUS_ITEM_UNDER_CONSTRUCTION:
                    $style_message = "color: rgb(0, 255, 0);";
                    $message = startTime($currentTechnologyListItem->buildEndTime - time(), 'build_message_research_' . $technology->id . '', 0, 'Wird ausgebaut auf Stufe ' . ($level + 1) . ' (TIME)');
                    break;
                case AllianceItemBuildStatus::STATUS_UNDER_CONSTRUCTION:
                    $message = "Es wird bereits gebaut!";
                    $style_message = "color: rgb(255, 0, 0);";
                    break;
                case AllianceItemBuildStatus::STATUS_MISSING_RESOURCE:
                    $need = $itemStatus->missingResources;
                    $message = "<input type=\"button\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Fehlende Rohstoffe einzahlen\" " . tm("Nicht genügend Rohstoffe", "Es sind nicht genügend Rohstoffe vorhanden!<br>Klick auf den Button um die fehlenden Rohstoffe einzuzahlen.") . " onclick=\"setSpends(" . $need->metal . ", " . $need->crystal . ", " . $need->plastic . ", " . $need->fuel . ", " . $need->food . ");\"/>";
                    foreach ($resName as $id => $resourceName) {
                        if ($need->get($id) > 0) {
                            $style[$id] = "style=\"color:red;\" " . tm("Fehlender Rohstoff", "" . StringUtils::formatNumber($need->get($id)) . " " . $resourceName . "") . "";
                        }
                    }
                    break;
                case AllianceItemBuildStatus::STATUS_OK;
                    $message = "<input type=\"submit\" class=\"button\" name=\"research_submit\" id=\"research_submit\" value=\"Erforschen\" onclick=\"document.getElementById('research_id').value=" . $technology->id . ";\"/>";
                    break;
            }

            echo "<th width=\"7%\">Stufe</th>
                <th width=\"18%\">Zeit</th>
                <th width=\"15%\">" . RES_METAL . "</th>
                <th width=\"15%\">" . RES_CRYSTAL . "</th>
                <th width=\"15%\">" . RES_PLASTIC . "</th>
                <th width=\"15%\">" . RES_FUEL . "</th>
                <th width=\"15%\">" . RES_FOOD . "</th>
            </tr><tr>
                <td width=\"7%\">" . ($level + 1) . "</th>
                <td width=\"18%\">" . StringUtils::formatTimespan($technology->calculateBuildTime($level + 1)) . "</th>
                <td " . $style[0] . " width=\"15%\">" . StringUtils::formatNumber($costs->metal) . "</td>
                <td " . $style[1] . " width=\"15%\">" . StringUtils::formatNumber($costs->crystal) . "</td>
                <td " . $style[2] . " width=\"15%\">" . StringUtils::formatNumber($costs->plastic) . "</td>
                <td " . $style[3] . " width=\"15%\">" . StringUtils::formatNumber($costs->fuel) . "</td>
                <td " . $style[4] . " width=\"15%\">" . StringUtils::formatNumber($costs->food) . "</td>
            </tr>
            <tr>
                <td colspan=\"7\" style=\"text-align:center;" . $style_message . "\" name=\"build_message_research_" . $technology->id . "\" id=\"build_message_research_" . $technology->id . "\">" . $message . "</td>";
        }

        echo "</tr>";
        tableEnd();
    }
}
// Es sind noch keine Gebäude vorhanden
else {
    error_msg("Es sind noch keine Technologien definiert!");
}

echo "</form>";
echo "</div>";



//
// Speicher + Einzahlungen
//

if ($action2 == "storage") {
    $display = "";
} else {
    $display = "none";
}
echo "<div id=\"tabStorage\" style=\"display:" . $display . ";\">";

echo "<form action=\"?page=" . $page . "&amp;action=" . $_GET['action'] . "&amp;action2=storage\" method=\"post\" id=\"alliance_storage\">\n";
echo $cstr;

tableStart("Rohstoffe einzahlen");

// Titan
echo "<tr>
                <th style=\"width:100px;\">" . RES_METAL . "</th>
                <td style=\"width:150px;\">
                    <input type=\"text\" value=\"0\" name=\"spend_metal\" id=\"spend_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $cp->resMetal . ",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_metal').value='" . StringUtils::formatNumber($cp->resMetal) . "';\">alles</a>
                </td>
            </tr>";
// Silizium
echo "<tr>
                <th>" . RES_CRYSTAL . "</th>
                <td>
                    <input type=\"text\" value=\"0\" name=\"spend_crystal\" id=\"spend_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $cp->resCrystal . ",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_crystal').value='" . StringUtils::formatNumber($cp->resCrystal) . "';\">alles</a>
                </td>
            </tr>";
// PVC
echo "<tr>
                <th>" . RES_PLASTIC . "</th>
                <td>
                    <input type=\"text\" value=\"0\" name=\"spend_plastic\" id=\"spend_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $cp->resPlastic . ",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_plastic').value='" . StringUtils::formatNumber($cp->resPlastic) . "';\">alles</a>
                </td>
            </tr>";
// Tritium
echo "<tr>
                <th>" . RES_FUEL . "</th>
                <td>
                    <input type=\"text\" value=\"0\" name=\"spend_fuel\" id=\"spend_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $cp->resFuel . ",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_fuel').value='" . StringUtils::formatNumber($cp->resFuel) . "';\">alles</a>
                </td>
            </tr>";
// Nahrung
echo "<tr>
                <th>" . RES_FOOD . "</th>
                <td>
                    <input type=\"text\" value=\"0\" name=\"spend_food\" id=\"spend_food\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $cp->resFood . ",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_food').value='" . StringUtils::formatNumber($cp->resFood) . "';\">alles</a>
                </td>
            </tr>";
tableEnd();

echo "<input type=\"submit\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Einzahlen\"/>";
echo "</form><br><br><br><br>";


//
// Einzahlungen
//

echo "<form action=\"?page=" . $page . "&amp;action=" . $_GET['action'] . "&amp;action2=storage\" method=\"post\" id=\"alliance_spends\">\n";
echo $cstr;

echo "<h1>Einzahlungen / Statistik</h1>";


//
// Filter
//

tableStart("Filter");

// Ausgabe
echo "<tr>
            <th>Ausgabe:</th>
            <td>
                <input type=\"radio\" name=\"output\" id=\"output\" value=\"0\" checked=\"checked\"/> Einzeln / <input type=\"radio\" name=\"output\" id=\"output\" value=\"1\"/> Summiert
            </td>
        </tr>";

// Limit
echo "<tr>
            <th>Einzahlungen:</th>
            <td>
                <select id=\"limit\" name=\"limit\">
                        <option value=\"0\" checked=\"checked\">alle</option>
                        <option value=\"1\">die letzte</option>
                        <option value=\"5\">die letzten 5</option>
                        <option value=\"20\">die letzten 20</option>
                    </select>
                </td>
            </tr>";

// Von User
echo "<tr>
            <th>Von User:</th>
            <td>
                <select id=\"user_spends\" name=\"user_spends\">
                        <option value=\"0\">alle</option>";
// Allianzuser
foreach ($cu->alliance->members as $id => $data) {
    echo "<option value=\"" . $id . "\">" . $data . "</option>";
}
echo "</select>
            </td>
        </tr>";
echo "<tr>
            <td style=\"text-align:center;\" colspan=\"2\">
                <input type=\"submit\" class=\"button\" name=\"filter_submit\" id=\"filter_submit\" value=\"Anzeigen\"\"/>
            </td>
        </tr>";
tableEnd();
echo "</form>";


//
// Ausgabe
//

// Einzahlungen werden summiert und ausgegeben
if ($sum) {
    if ($user > 0) {
        $user_message = "von " . $cu->alliance->members[$user] . " ";
    } else {
        $user_message = "";
    }

    echo "Es werden die bisher eingezahlten Rohstoffe " . $user_message . " angezeigt.<br><br>";

    // Läd Einzahlungen
    $resources = $allianceSpendRepository->getTotalSpent($cu->allianceId(), (int) $user);
    if ($resources->getSum() > 0) {
        tableStart("Total eingezahlte Rohstoffe " . $user_message . "");
        echo "<tr>
                        <th style=\"width:20%\">" . RES_METAL . "</th>
                        <th style=\"width:20%\">" . RES_CRYSTAL . "</th>
                        <th style=\"width:20%\">" . RES_PLASTIC . "</th>
                        <th style=\"width:20%\">" . RES_FUEL . "</th>
                        <th style=\"width:20%\">" . RES_FOOD . "</th>
                    </tr>";
        echo "<tr>
                        <td>" . StringUtils::formatNumber($resources->metal) . "</td>
                        <td>" . StringUtils::formatNumber($resources->crystal) . "</td>
                        <td>" . StringUtils::formatNumber($resources->plastic) . "</td>
                        <td>" . StringUtils::formatNumber($resources->fuel) . "</td>
                        <td>" . StringUtils::formatNumber($resources->food) . "</td>
                    </tr>";
        tableEnd();
    } else {
        iBoxStart("Einzahlungen");
        echo "Es wurden noch keine Rohstoffe eingezahlt!";
        iBoxEnd();
    }
}
// Einzahlungen werden einzelen ausgegeben
else {

    if ($user > 0) {
        $user_message = "von " . $cu->alliance->members[$user] . " ";
    } else {
        $user_message = "";
    }


    if ($limit > 0) {
        if ($limit == 1) {
            echo "Es wird die letzte Einzahlung " . $user_message . "gezeigt.<br><br>";
        } else {
            echo "Es werden die letzten " . $limit . " Einzahlungen " . $user_message . "gezeigt.<br><br>";
        }
    } else {
        echo "Es werden alle bisherigen Einzahlungen " . $user_message . "gezeigt.<br><br>";
    }


    // Läd Einzahlungen
    $spendEntries = $allianceSpendRepository->getSpent($cu->allianceId(), $user, $limit);
    if (count($spendEntries) > 0) {
        foreach ($spendEntries as $entry) {
            tableStart("" . $cu->alliance->members[$entry->userId] . " - " . StringUtils::formatDate($entry->time) . "");
            echo "<tr>
                            <th style=\"width:20%\">" . RES_METAL . "</th>
                            <th style=\"width:20%\">" . RES_CRYSTAL . "</th>
                            <th style=\"width:20%\">" . RES_PLASTIC . "</th>
                            <th style=\"width:20%\">" . RES_FUEL . "</th>
                            <th style=\"width:20%\">" . RES_FOOD . "</th>
                        </tr>";
            echo "<tr>
                            <td>" . StringUtils::formatNumber($entry->metal) . "</td>
                            <td>" . StringUtils::formatNumber($entry->crystal) . "</td>
                            <td>" . StringUtils::formatNumber($entry->plastic) . "</td>
                            <td>" . StringUtils::formatNumber($entry->fuel) . "</td>
                            <td>" . StringUtils::formatNumber($entry->food) . "</td>
                        </tr>";
            tableEnd();
        }
    } else {
        iBoxStart("Einzahlungen");
        echo "Es wurden noch keine Rohstoffe eingezahlt!";
        iBoxEnd();
    }
}

echo "</div>";



//
// Schiffswerft
//

if ($action2 == "shipyard") {
    $display = "";
} else {
    $display = "none";
}
echo "<div id=\"tabShipyard\" style=\"display:" . $display . ";\">";

if ($allianceShipyardLevel > 0) {
    echo "<h1>Schiffswerft</h1>";

    echo "<form action=\"?page=" . $page . "&amp;action=" . $_GET['action'] . "&amp;action2=shipyard\" method=\"post\" id=\"alliance_shipyard\">\n";
    echo $cstr;

    tableStart("Guthaben Übersicht");

    echo "<tr>";
    if ($cu->alliance->resMetal < 0 || $cu->alliance->resCrystal < 0 || $cu->alliance->resPlastic < 0 || $cu->alliance->resFuel < 0 || $cu->alliance->resFood < 0) {
        echo "<td style=\"text-align:center;\"><span " . tm("Produktionsstop", "Die Produktion wurde unterbrochen, da negative Rohstoffe vorhanden sind.") . ">Schiffsteile pro Stunde: 0</span></td>";
    } else {
        // if changed, also change classes/alliance.class.php
        echo "<td style=\"text-align:center;\">Schiffsteile pro Stunde: " . ceil($config->getInt('alliance_shippoints_per_hour') * pow($config->getFloat('alliance_shippoints_base'), ($allianceShipyardLevel - 1))) . "</td>";
    }
    echo "</tr>
    <tr>
        <td style=\"text-align:center;\">Vorhandene Teile: " . ($cu->allianceShippoints - $ship_costed) . "</td>
    </tr>";

    tableEnd();


    // Listet Schiffe auf
    if (count($ships) > 0) {
        foreach ($ships as $ship) {
            // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
            $ship_count = 0;
            // ... auf den Planeten
            if (isset($shiplist[$ship->id])) {
                $ship_count += $shiplist[$ship->id];
            }
            // ... in der Bauliste
            if (isset($queue_total[$ship->id])) {
                $ship_count += $queue_total[$ship->id];
            }
            // ... in der Luft
            if (isset($fleet[$ship->id])) {
                $ship_count += $fleet[$ship->id];
            }


            //Kostenfaktor Schiffe
            $cost_factor = pow($config->getFloat("alliance_shipcosts_factor"), $ship_count);

            $path = IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $ship->id . "_middle." . IMAGE_EXT;
            tableStart($ship->name);
            echo "<tr>
                <td style=\"width:120px;background:#000;vertical-align:middle;padding:0px;\">
                <img src=\"" . $path . "\" style=\"width:120px;height:120px;border:none;margin:0px;\" alt=\"" . $ship->name . "\"/>
                    <input type=\"hidden\" value=\"" . $ship->name . "\" id=\"ship_name_" . $ship->id . "\" name=\"ship_name_" . $ship->id . "\" />
                </td>
                <td style=\"vertical-align:top;height:100px;\" colspan=\"7\">
                    " . $ship->longComment . "
                </td>
                    </tr>
                    <tr>
                            <th style=\"width:13%\">Waffen</th>
                            <th style=\"width:13%\">Struktur</th>
                            <th style=\"width:13%\">Schild</th>
                            <th style=\"width:13%\">Speed</th>
                            <th style=\"width:13%\">Startzeit</th>
                            <th style=\"width:13%\">Landezeit</th>
                            <th style=\"width:12%\">Kosten</th>
                            <th style=\"width:10%\">Anzahl</th>
                        </tr>
                        <tr>
                            <td>" . StringUtils::formatNumber($ship->weapon) . "</td>
                            <td>" . StringUtils::formatNumber($ship->structure) . "</td>
                            <td>" . StringUtils::formatNumber($ship->shield) . "</td>
                            <td>" . StringUtils::formatNumber($ship->speed) . " AE/h</td>
                            <td>" . StringUtils::formatTimespan($ship->timeToStart / FLEET_FACTOR_S) . "</td>
                            <td>" . StringUtils::formatTimespan($ship->timeToLand / FLEET_FACTOR_S) . "</td>";
            if ($ship->maxCount !== 0 && $ship->maxCount <= $ship_count) {
                echo "<td colspan=\"2\"><i>Maximalanzahl erreicht</i></td>";
            } else {
                echo "<td>" . StringUtils::formatNumber($ship->allianceCosts * $cost_factor) . " <input type=\"hidden\" value=\"" . $ship->allianceCosts * $cost_factor . "\" id=\"ship_costs_" . $ship->id . "\" name=\"ship_costs_" . $ship->id . "\" /></td>
                            <td>
                                <input type=\"text\" value=\"0\" name=\"buy_ship[" . $ship->id . "]\" id=\"buy_ship_" . $ship->id . "\" size=\"4\" maxlength=\"6\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/>";
            }
            echo "<input type=\"hidden\" value=\"" . $ship->maxCount . "\" id=\"ship_max_count_" . $ship->id . "\" name=\"ship_max_count_" . $ship->id . "\" />
                                </td>
                        </tr>";


            tableEnd();
        }
    } else {
        iBoxStart("Schiffe");
        echo "Es sind keine Allianzschiffe vorhanden!";
        iBoxEnd();
    }



    tableStart("Fertigung");

    echo "<tr>
                    <td style=\"text-align:center;\">
                        <select id=\"user_buy_ship\" name=\"user_buy_ship\">
                            <option value=\"" . $cu->id . "\">" . $cu . " (" . StringUtils::formatNumber($cu->allianceShippoints - $ship_costed) . ")</option>
                        </select><br/><br/>
                    <input type=\"submit\" class=\"button\" name=\"ship_submit\" id=\"ship_submit\" value=\"Schiffe herstellen\" " . tm("Schiffe herstellen", "Stellt aus den vorhandenen Teilen die gewünschten Schiffe für den ausgewählten User her.") . ">
                    </td>
                </tr>";

    tableEnd();

    echo "</form>";
}

echo "</div>";
