<?PHP

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipCategoryRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueItem;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Ship\ShipSort;
use EtoA\Technology\TechnologyRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\User\UserPropertiesRepository;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

/** @var ShipQueueRepository $shipQueueRepository */
$shipQueueRepository = $app[ShipQueueRepository::class];
/** @var ShipRepository $shipRepositroy */
$shipRepositroy = $app[ShipRepository::class];
/** @var FleetRepository $fleetRepository */
$fleetRepository = $app[FleetRepository::class];

/** @var ShipRequirementRepository $shipRequirementRepository */
$shipRequirementRepository = $app[ShipRequirementRepository::class];

/** @var ShipCategoryRepository $shipCategoryRepository */
$shipCategoryRepository = $app[ShipCategoryRepository::class];

/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

//Definition für "Info" Link
define('ITEMS_TBL', "ships");
define('REQ_TBL', "ship_requirements");
define('REQ_ITEM_FLD', "req_ship_id");
define('ITEM_ID_FLD', "ship_id");
define('ITEM_NAME_FLD', "ship_name");
define('RACE_TO_ADD', " AND (ship_race_id=0 OR ship_race_id='" . $cu->raceId . "')");
define('ITEM_SHOW_FLD', "ship_show");
define('ITEM_ORDER_FLD', "ship_order");
define('NO_ITEMS_MSG', "In dieser Kategorie gibt es keine Schiffe!");
define('HELP_URL', "?page=help&site=shipyard");

// Absolute minimal Bauzeit in Sekunden
define("SHIPYARD_MIN_BUILD_TIME", $config->getInt('shipyard_min_build_time'));

// Ben. Level für Autragsabbruch
define("SHIPQUEUE_CANCEL_MIN_LEVEL", $config->getInt('shipqueue_cancel_min_level'));

define("SHIPQUEUE_CANCEL_START", $config->getFloat('shipqueue_cancel_start'));

define("SHIPQUEUE_CANCEL_FACTOR", $config->getFloat('shipqueue_cancel_factor'));

define("SHIPQUEUE_CANCEL_END", $config->getFloat('shipqueue_cancel_end'));

$planet = $planetRepo->find($cp->id);

/** @var TechnologyRepository $technologyRepository */
$technologyRepository = $app[TechnologyRepository::class];
$techlist = $technologyRepository->getTechnologyLevels($cu->getId());

$shipyard = $buildingRepository->getEntityBuilding($cu->getId(), $planet->id, SHIP_BUILDING_ID);

//Tabulator var setzten (für das fortbewegen des cursors im forumular)
$tabulator = 1;

// Prüfen ob Werft gebaut ist
if ($shipyard !== null && $shipyard->currentLevel > 0) {
    // Titel
    echo "<h1>Raumschiffswerft (Stufe " . $shipyard->currentLevel . ") des Planeten " . $planet->name . "</h1>";

    // Ressourcen anzeigen
    echo $resourceBoxDrawer->getHTML($planet);

    // Prüfen ob dieses Gebäude deaktiviert wurde
    if ($shipyard->isDeactivated()) {
        iBoxStart("Geb&auml;ude nicht bereit");
        echo "Diese Schiffswerft ist bis " . date("d.m.Y H:i", $shipyard->deactivated) . " deaktiviert.";
        iBoxEnd();
    }
    // Werft anzeigen
    else {
        /****************************
         *  Sortiereingaben speichern *
         ****************************/
        if (
            count($_POST) > 0 && isset($_POST['sort_submit'])
            && ctype_aldotsc($_POST['sort_value']) && ctype_aldotsc($_POST['sort_way'])
        ) {
            $properties->itemOrderShip = $_POST['sort_value'];
            $properties->itemOrderWay = $_POST['sort_way'];
            $userPropertiesRepository->storeProperties($cu->id, $properties);
        }

        //
        // Läd alle benötigten Daten in PHP-Arrays
        //Gentechnologie:

        // Vorausetzungen laden
        $requirements = $shipRequirementRepository->getAll();

        //Gentechlevel definieren
        $gen_tech_level = $techlist[GEN_TECH_ID] ?? 0;

        // Gebaute Schiffe laden
        /** @var array<int, array<int, int>> $shiplist */
        $shiplist = [];
        /** @var array<int, int> $bunkered */
        $bunkered = [];
        $userShiplist = $shipRepositroy->findForUser($cu->getId());
        foreach ($userShiplist as $entry) {
            $shiplist[$entry->shipId][$entry->entityId] = $entry->count;
            if (!isset($bunkered[$entry->shipId])) {
                $bunkered[$entry->shipId] = 0;
            }
            $bunkered[$entry->shipId] += $entry->bunkered;
        }

        // Bauliste vom aktuellen Planeten laden (wird nach "Abbrechen" nochmals geladen)
        /** @var array<int, ShipQueueItem> $queue */
        $queue = [];
        $shipQueueItems = $shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->entityId($planet->id)->endAfter($time));
        foreach ($shipQueueItems as $item) {
            $queue[$item->id] = $item;
        }

        // Bauliste vom allen Planeten laden und nach Schiffe zusammenfassen
        $queue_total = $shipQueueRepository->getUserQueuedShipCounts($cu->getId());

        // Flotten laden
        $fleet = $fleetRepository->getUserFleetShipCounts($cu->getId());

        // Alle Schiffe laden
        //Schiffsordnung des Users beachten
        $shipCategories = $shipCategoryRepository->getAllCategories();
        /** @var \EtoA\Ship\Ship[] $ships */
        $ships = [];
        /** @var \EtoA\Ship\Ship[][] $shipsByCategory */
        $shipsByCategory = [];
        /** @var PreciseResources[] $shipCosts */
        $shipCosts = [];
        $shipSearch = ShipSearch::create()->buildable()->raceOrNull($cu->raceId);
        $shipOrder = ShipSort::specialWithUserSort($properties->itemOrderShip, $properties->itemOrderWay);
        $items = $shipDataRepository->searchShips($shipSearch, $shipOrder);
        foreach ($items as $ship) {
            $shipsByCategory[$ship->catId][] = $ship;
            $ships[$ship->id] = $ship;
            $shipCosts[$ship->id] = PreciseResources::createFromBase($ship->getCosts())->multiply($cu->specialist->costsShip);
        }

        // level zählen welches die schiffswerft über dem angegeben level ist und faktor berechnen
        $need_bonus_level = $shipyard->currentLevel - $config->param1Int('build_time_boni_schiffswerft');
        if ($need_bonus_level <= 0) {
            $time_boni_factor = 1;
        } else {
            $time_boni_factor = 1 - ($need_bonus_level * ($config->getInt('build_time_boni_schiffswerft') / 100));
        }

        // Faktor der zurückerstatteten Ressourcen bei einem Abbruch des Auftrags berechnen
        if ($shipyard->currentLevel >= SHIPQUEUE_CANCEL_MIN_LEVEL) {
            $cancel_res_factor = min(SHIPQUEUE_CANCEL_END, SHIPQUEUE_CANCEL_START + (($shipyard->currentLevel - SHIPQUEUE_CANCEL_MIN_LEVEL) * SHIPQUEUE_CANCEL_FACTOR));
        } else {
            $cancel_res_factor = 0;
        }

        // Infos anzeigen
        tableStart("Werft-Infos");
        echo '<colgroup><col style="width:400px;"/><col/></colgroup>';
        if ($cu->specialist->costsShip != 1) {
            echo "<tr><td>Kostenreduktion durch " . $cu->specialist->name . ":</td><td>" . get_percent_string($cu->specialist->costsShip) . "</td></tr>";
        }
        if ($cu->specialist->shipTime != 1) {
            echo "<tr><td>Bauzeitverringerung durch " . $cu->specialist->name . ":</td><td>" . get_percent_string($cu->specialist->shipTime) . "</td></tr>";
        }
        echo "<tr><td>Eingestellte Arbeiter:</td><td>" . nf($shipyard->peopleWorking);
        if (count($queue) === 0) {
            echo '&nbsp;<a href="javascript:;" onclick="toggleBox(\'changePeople\');">[&Auml;ndern]</a>';
        }
        echo "</td></tr>";
        if ($shipyard->peopleWorking > 0) {
            echo '<tr><td>Zeitreduktion durch Arbeiter pro Auftrag:</td><td><span id="people_work_done">' . tf($config->getInt('people_work_done') * $shipyard->peopleWorking) . '</span></td></tr>';
            echo '<tr><td>Nahrungsverbrauch durch Arbeiter pro Auftrag:</td><td><span id="people_food_require">' . nf($config->getInt('people_food_require') * $shipyard->peopleWorking) . '</span></td></tr>';
        }
        if ($gen_tech_level  > 0) {
            echo '<tr><td>Gentechnologie:</td><td>' . $gen_tech_level . '</td></tr>';
            echo '<tr><td>Minimale Bauzeit (mit Arbeiter):</td><td>Bauzeit * ' . (0.1 - ($gen_tech_level / 100)) . '</td></tr>';
        }
        echo "<tr><td>Bauzeitverringerung:</td><td>";
        if ($need_bonus_level >= 0) {
            echo get_percent_string($time_boni_factor) . " durch Stufe " . $shipyard->currentLevel;
        } else {
            echo "Stufe " . $config->getInt('build_time_boni_schiffswerft') . " erforderlich!";
        }
        echo "</td></tr>";
        if ($cancel_res_factor > 0) {
            echo "<tr><td>Ressourcenrückgabe bei Abbruch:</td><td>" . ($cancel_res_factor * 100) . "% (ohne " . RES_FOOD . ", " . (SHIPQUEUE_CANCEL_END * 100) . "% maximal)</td></tr>";
            $cancelable = true;
        } else {
            echo "<tr><td>Abbruchmöglichkeit:</td><td>Stufe " . SHIPQUEUE_CANCEL_MIN_LEVEL . " erforderlich!</td></tr>";
            $cancelable = false;
        }
        tableEnd();
        $peopleWorking = $buildingRepository->getPeopleWorking($planet->id);
        $peopleFree = floor($planet->people) - $peopleWorking->total + $peopleWorking->shipyard;
        $box =  '
                <input type="hidden" name="workDone" id="workDone" value="' . $config->getInt('people_work_done') . '" />
                <input type="hidden" name="foodRequired" id="foodRequired" value="' . $config->getInt('people_food_require') . '" />
                <input type="hidden" name="peopleFree" id="peopleFree" value="' . $peopleFree . '" />
                <input type="hidden" name="foodAvaiable" id="foodAvaiable" value="' . $planet->resFood . '" />
                <input type="hidden" name="peopleOptimized" id="peopleOptimized" value="0" />';

        $box .= '   <tr>
                        <th>Eingestellte Arbeiter</th>
                        <td>
                            <input  type="text"
                                    name="peopleWorking"
                                    id="peopleWorking"
                                    value="' . nf($shipyard->peopleWorking) . '"
                                    onkeyup="updatePeopleWorkingBox(this.value,\'-1\',\'-1\');"/>
                    </td>
                    </tr>
                    <tr>
                        <th>Zeitreduktion</th>
                        <td><input  type="text"
                                    name="timeReduction"
                                    id="timeReduction"
                                    value="' . tf($config->getInt('people_work_done') * $shipyard->peopleWorking) . '"
                                    onkeyup="updatePeopleWorkingBox(\'-1\',this.value,\'-1\');" /></td>
                    </tr>
                        <th>Nahrungsverbrauch</th>
                        <td><input  type="text"
                                    name="foodUsing"
                                    id="foodUsing"
                                    value="' . nf($config->getInt('people_food_require') * $shipyard->peopleWorking) . '"
                                    onkeyup="updatePeopleWorkingBox(\'-1\',\'-1\',this.value);" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <div id="changeWorkingPeopleError" style="display:none;">&nbsp;</div>
                            <input type="submit" value="Speichern" name="submit_people_form" id="submit_people_form" />&nbsp;';
        echo '<div id="changePeople" style="display:none;">';
        tableStart("Arbeiter der Raumschiffswerft zuteilen");
        echo '<form id="changeWorkingPeople" method="post" action="?page=' . $page . '">
        ' . $box . '</form>';
        tableEnd();
        echo '</div>';

        // people working changed
        if (isset($_POST['submit_people_form'])) {
            if (count($queue) === 0) {
                $buildingRepository->setPeopleWorking($planet->id, SHIP_BUILDING_ID, (int) nf_back($_POST['peopleWorking']));
                //success_msg("Arbeiter zugeteilt!");
            } else
                error_msg('Arbeiter konnten nicht zugeteilt werden!');
            header("Refresh:0");
        }


        /*************
         * Sortierbox *
         *************/

        echo "<form action=\"?page=$page\" method=\"post\">";
        iBoxStart("Filter");

        echo "<div style=\"text-align:center;\">
                            <select name=\"sort_value\">";
        foreach (ShipSort::USER_SORT_VALUES as $value => $name) {
            echo "<option value=\"" . $value . "\"";
            if ($properties->itemOrderShip === $value) {
                echo " selected=\"selected\"";
            }
            echo ">" . $name . "</option>";
        }
        echo "</select>

                            <select name=\"sort_way\">";

        //Aufsteigend
        echo "<option value=\"ASC\"";
        if ($properties->itemOrderWay == 'ASC') echo " selected=\"selected\"";
        echo ">Aufsteigend</option>";

        //Absteigend
        echo "<option value=\"DESC\"";
        if ($properties->itemOrderWay == 'DESC') echo " selected=\"selected\"";
        echo ">Absteigend</option>";

        echo "</select>

                            <input type=\"submit\" class=\"button\" name=\"sort_submit\" value=\"Sortieren\"/>
                        </div>";
        iBoxEnd();
        echo "</form>";

        echo "<form action=\"?page=" . $page . "\" method=\"post\" style=\"clear:both;\">";


        /****************************
         *  Schiffe in Auftrag geben *
         ****************************/

        if (count($_POST) > 0 && isset($_POST['submit']) && checker_verify()) {
            tableStart();
            echo "<tr><th>Ergebnisse des Bauauftrags</th></tr>";

            //Log variablen setzten
            $log_ships = "";

            $totalMetal = 0;
            $totalCrystal = 0;
            $totalPlastic = 0;
            $totalFuel = 0;
            $totalFood = 0;

            // Endzeit bereits laufender Aufträge laden
            $end_time = time();
            if (count($queue) > 0) {
                // Speichert die letzte Endzeit, da das Array $queue nach queue_starttime (und somit auch endtime) sortiert ist
                foreach ($queue as $data) {
                    $end_time = $data->endTime;
                }
            }

            //
            // Bauaufträge speichern
            //
            $counter = 0;
            foreach ($_POST['build_count'] as $ship_id => $build_cnt) {
                $ship_id = intval($ship_id);

                $build_cnt = nf_back($build_cnt);

                if ($build_cnt > 0 && isset($ships[$ship_id])) {
                    $buildCountOriginal = $build_cnt;

                    // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
                    $ship_count = 0;
                    // ... auf den Planeten
                    if (isset($shiplist[$ship_id])) {
                        $ship_count += array_sum($shiplist[$ship_id]);
                    }
                    // ... im Bunker
                    if (isset($bunkered[$ship_id])) {
                        $ship_count += $bunkered[$ship_id];
                    }
                    // ... in der Bauliste
                    if (isset($queue_total[$ship_id])) {
                        $ship_count += $queue_total[$ship_id];
                    }
                    // ... in der Luft
                    if (isset($fleet[$ship_id])) {
                        $ship_count += $fleet[$ship_id];
                    }

                    //Anzahl überprüfen, ob diese die maximalzahl übersteigt, gegebenenfalls ändern
                    if ($build_cnt + $ship_count > $ships[$ship_id]->maxCount && $ships[$ship_id]->maxCount != 0) {
                        $build_cnt = max(0, $ships[$ship_id]->maxCount - $ship_count);
                    }

                    // TODO: Überprüfen
                    //Wenn der User nicht genug Ress hat, die Anzahl Schiffe drosseln
                    $bf = [];
                    $bc = [];

                    //Titan
                    if ($shipCosts[$ship_id]->metal > 0) {
                        $bf['metal'] = $planet->resMetal / $shipCosts[$ship_id]->metal;
                    } else {
                        $bc['metal'] = 0;
                    }
                    //Silizium
                    if ($shipCosts[$ship_id]->crystal > 0) {
                        $bf['crystal'] = $planet->resCrystal / $shipCosts[$ship_id]->crystal;
                    } else {
                        $bc['crystal'] = 0;
                    }
                    //PVC
                    if ($shipCosts[$ship_id]->plastic > 0) {
                        $bf['plastic'] = $planet->resPlastic / $shipCosts[$ship_id]->plastic;
                    } else {
                        $bc['plastic'] = 0;
                    }
                    //Tritium
                    if ($shipCosts[$ship_id]->fuel > 0) {
                        $bf['fuel'] = $planet->resFuel / $shipCosts[$ship_id]->fuel;
                    } else {
                        $bc['fuel'] = 0;
                    }
                    //Nahrung
                    if (intval($_POST['additional_food_costs']) > 0 || $shipCosts[$ship_id]->food > 0) {
                        $bf['food'] = $planet->resFood / (intval($_POST['additional_food_costs']) + $shipCosts[$ship_id]->food);
                    } else {
                        $bc['food'] = 0;
                    }

                    //Anzahl Drosseln ???
                    if ($build_cnt > floor(min($bf))) {
                        $build_cnt = floor(min($bf));
                    }

                    //Check for Rene-Bug
                    $additional_food_costs = $shipyard->peopleWorking * $config->getInt('people_food_require');
                    if ($additional_food_costs != intval($_POST['additional_food_costs']) || intval($_POST['additional_food_costs']) < 0) {
                        $build_cnt = 0;
                    }

                    //Anzahl muss grösser als 0 sein
                    if ($build_cnt > 0) {
                        //Errechne Kosten pro auftrag schiffe
                        $bc['metal'] = $shipCosts[$ship_id]->metal * $build_cnt;
                        $bc['crystal'] = $shipCosts[$ship_id]->crystal * $build_cnt;
                        $bc['plastic'] = $shipCosts[$ship_id]->plastic * $build_cnt;
                        $bc['fuel'] = $shipCosts[$ship_id]->fuel * $build_cnt;
                        $bc['food'] = (intval($_POST['additional_food_costs']) + $shipCosts[$ship_id]->food) * $build_cnt;

                        // Bauzeit pro Schiff berechnen
                        $btime = $shipCosts[$ship_id]->sum()
                            / $config->getInt('global_time') * $config->getFloat('ship_build_time')
                            * $time_boni_factor
                            * $cu->specialist->shipTime;

                        // TODO: Überprüfen
                        //Rechnet zeit wenn arbeiter eingeteilt sind
                        $btime_min = $btime * (0.1 - ($gen_tech_level / 100));
                        if ($btime_min < SHIPYARD_MIN_BUILD_TIME) $btime_min = SHIPYARD_MIN_BUILD_TIME;
                        $btime = ceil($btime - $shipyard->peopleWorking * $config->getInt('people_work_done'));
                        if ($btime < $btime_min) $btime = $btime_min;
                        $obj_time = $btime;

                        // Gesamte Bauzeit berechnen
                        $duration = $build_cnt * $obj_time;

                        // Setzt Starzeit des Auftrages, direkt nach dem letzten Auftrag
                        $start_time = $end_time;
                        $end_time = $start_time + $duration;

                        // Auftrag speichern
                        $shiplist_id = $shipQueueRepository->add($cu->getId(), $ship_id, $planet->id, $build_cnt, $start_time, (int) $end_time, (int) $obj_time);

                        $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, BuildingId::SHIPYARD, true);

                        // Queue Array aktualisieren
                        $queue[$shiplist_id] = new ShipQueueItem([
                            'queue_id' => $shiplist_id,
                            'queue_ship_id' => $ship_id,
                            'queue_cnt' => $build_cnt,
                            'queue_user_id' => $cu->getId(),
                            'queue_entity_id' => $planet->id,
                            'queue_starttime' => $start_time,
                            'queue_endtime' => $end_time,
                            'queue_objtime' => $obj_time,
                            'queue_build_type' => 0,
                        ]);

                        echo "<tr><td>" . nf($build_cnt) . " " . $ships[$ship_id]->name . " in Auftrag gegeben!</td></tr>";

                        //Rohstoffe summieren, diese werden nach der Schleife abgezogen
                        $totalMetal += $bc['metal'];
                        $totalCrystal += $bc['crystal'];
                        $totalPlastic += $bc['plastic'];
                        $totalFuel += $bc['fuel'];
                        $totalFood += $bc['food'];

                        //Log schreiben
                        $log_text = "[b]Schiffsauftrag Bauen[/b]

                        [b]Start:[/b] " . date("d.m.Y H:i:s", $end_time) . "
                        [b]Ende:[/b] " . date("d.m.Y H:i:s", $end_time) . "
                        [b]Dauer:[/b] " . tf($duration) . "
                        [b]Dauer pro Einheit:[/b] " . tf($obj_time) . "
                        [b]Schiffswerft Level:[/b] " . $shipyard->currentLevel . "
                        [b]Eingesetzte Bewohner:[/b] " . nf($shipyard->peopleWorking) . "
                        [b]Gen-Tech Level:[/b] " . $gen_tech_level . "
                        [b]Eingesetzter Spezialist:[/b] " . $cu->specialist->name . "

                        [b]Kosten[/b]
                        [b]" . RES_METAL . ":[/b] " . nf($bc['metal']) . "
                        [b]" . RES_CRYSTAL . ":[/b] " . nf($bc['crystal']) . "
                        [b]" . RES_PLASTIC . ":[/b] " . nf($bc['plastic']) . "
                        [b]" . RES_FUEL . ":[/b] " . nf($bc['fuel']) . "
                        [b]" . RES_FOOD . ":[/b] " . nf($bc['food']) . "

                        [b]Rohstoffe auf dem Planeten[/b]
                        [b]" . RES_METAL . ":[/b] " . nf($planet->resMetal - $totalMetal) . "
                        [b]" . RES_CRYSTAL . ":[/b] " . nf($planet->resCrystal - $totalCrystal) . "
                        [b]" . RES_PLASTIC . ":[/b] " . nf($planet->resPlastic - $totalPlastic) . "
                        [b]" . RES_FUEL . ":[/b] " . nf($planet->resFuel - $totalFuel) . "
                        [b]" . RES_FOOD . ":[/b] " . nf($planet->resFood - $totalFood);

                        GameLog::add(GameLog::F_SHIP, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $planet->id, $ship_id, 1, $build_cnt);

                        //Daten für Log speichern
                        $log_ships .= "<b>" . $ships[$ship_id]->name . "</b>: " . nf($build_cnt) . " (" . tf($duration) . ")<br>";
                    } else {
                        echo "<tr><td>" . $ships[$ship_id]->name . ": Zu wenig Rohstoffe für diese Anzahl ($buildCountOriginal)!</td></tr>";
                    }
                    $counter++;
                }
            }

            //Rohstoffe vom Planeten abziehen und aktualisieren
            $planetRepo->addResources($planet->id, -$totalMetal, -$totalCrystal, -$totalPlastic, -$totalFuel, -$totalFood);

            if ($counter == 0) {
                echo "<tr><td>Keine Schiffe gew&auml;hlt!</td></tr>";
            }
            tableEnd();
            header("Refresh:0");
        }

        checker_init();

        /*********************
         * Auftrag abbrechen  *
         *********************/
        if (isset($_GET['cancel']) && intval($_GET['cancel']) > 0 && $cancelable) {
            $id = intval($_GET['cancel']);
            if (isset($queue[$id])) {
                $ship_id = $queue[$id]->shipId;

                //Zu erhaltende Rohstoffe errechnen
                $obj_cnt = min(ceil(($queue[$id]->endTime - max($time, $queue[$id]->startTime)) / $queue[$id]->objectTime), $queue[$id]->count);
                echo "Breche den Bau von " . $obj_cnt . " " . $ships[$ship_id]->name . " ab...<br/>";

                $ret = [];
                $ret['metal'] = $shipCosts[$ship_id]->metal * $obj_cnt * $cancel_res_factor;
                $ret['crystal'] = $shipCosts[$ship_id]->crystal * $obj_cnt * $cancel_res_factor;
                $ret['plastic'] = $shipCosts[$ship_id]->plastic * $obj_cnt * $cancel_res_factor;
                $ret['fuel'] = $shipCosts[$ship_id]->fuel * $obj_cnt * $cancel_res_factor;
                $ret['food'] = $shipCosts[$ship_id]->food * $obj_cnt * $cancel_res_factor;

                // Daten für Log speichern
                $ship_name = $ships[$ship_id]->name;
                $queue_count = $queue[$id]->count;
                $queue_objtime = $queue[$id]->objectTime;
                $start_time = $queue[$id]->startTime;
                $end_time = $queue[$id]->endTime;

                //Auftrag löschen
                $shipQueueRepository->deleteQueueItem($id);

                $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, BuildingId::SHIPYARD, false);

                // Nachkommende Aufträge werden Zeitlich nach vorne verschoben
                $queueItems = $shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->entityId($planet->id)->startEqualAfter($end_time));
                if (count($queueItems) > 0) {
                    $new_starttime = max($start_time, time());
                    foreach ($queueItems as $item) {
                        $item->startTime = $new_starttime;
                        $item->endTime = $new_starttime + $item->endTime - $item->startTime;
                        $shipQueueRepository->saveQueueItem($item);

                        // Aktualisiert das Queue-Array
                        $queue[$item->id] = $item;

                        $new_starttime = $item->endTime;
                    }
                }

                // Auftrag aus Array löschen
                unset($queue[$id]);

                //Rohstoffe dem Planeten gutschreiben und aktualisieren
                $planetRepo->addResources($planet->id, $ret['metal'], $ret['crystal'], $ret['plastic'], $ret['fuel'], $ret['food']);

                echo "Der Auftrag wurde abgebrochen!<br/><br/>";

                //Log schreiben
                $log_text = "[b]Schiffsauftrag Abbruch[/b]

                [b]Auftragsdauer:[/b] " . tf($queue_objtime * $queue_count) . "

                [b]Erhaltene Rohstoffe[/b]
                [b]Faktor:[/b] " . $cancel_res_factor . "
                [b]" . RES_METAL . ":[/b] " . nf($ret['metal']) . "
                [b]" . RES_CRYSTAL . ":[/b] " . nf($ret['crystal']) . "
                [b]" . RES_PLASTIC . ":[/b] " . nf($ret['plastic']) . "
                [b]" . RES_FUEL . ":[/b] " . nf($ret['fuel']) . "
                [b]" . RES_FOOD . ":[/b] " . nf($ret['food']) . "

                [b]Rohstoffe auf dem Planeten[/b]
                [b]" . RES_METAL . ":[/b] " . nf($planet->resMetal + $ret['metal']) . "
                [b]" . RES_CRYSTAL . ":[/b] " . nf($planet->resCrystal + $ret['crystal']) . "
                [b]" . RES_PLASTIC . ":[/b] " . nf($planet->resPlastic + $ret['plastic']) . "
                [b]" . RES_FUEL . ":[/b] " . nf($planet->resFuel + $ret['fuel']) . "
                [b]" . RES_FOOD . ":[/b] " . nf($planet->resFood + $ret['food']);

                //Log Speichern
                GameLog::add(GameLog::F_SHIP, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $planet->id, $ship_id, 0, $queue_count);
                header("Refresh:0");
            }
        }

        /*********************************
         * Liste der Bauaufträge anzeigen *
         *********************************/
        if (count($queue) > 0) {
            tableStart("Bauliste");
            $first = true;
            $absolute_starttime = 0;
            foreach ($queue as $data) {
                if ($first) {
                    $obj_t_remaining = ((($data->endTime - $time) / $data->objectTime) - floor(($data->endTime - $time) / $data->objectTime)) * $data->objectTime;
                    if ($obj_t_remaining == 0) {
                        $obj_t_remaining = $data->objectTime;
                    }
                    $obj_time = $data->objectTime;

                    $absolute_starttime = $data->startTime;

                    $obj_t_passed = $data->objectTime - $obj_t_remaining;
                    echo "<tr>
                            <th colspan=\"2\">Aktuell</th>
                            <th style=\"width:150px;\">Start</th>
                            <th style=\"width:150px;\">Ende</th>
                            <th style=\"width:80px;\" colspan=\"2\">Verbleibend</th>
                        </tr>";
                    echo "<tr>";
                    echo "<td colspan=\"2\">" . $ships[$data->shipId]->name . "</td>";
                    echo "<td>" . df(time() - $obj_t_passed, 1) . "</td>";
                    echo "<td>" . df(time() + $obj_t_remaining, 1) . "</td>";
                    echo "<td colspan=\"2\">" . tf($obj_t_remaining) . "</td>
                    </tr>";
                    echo "<tr>
                            <th style=\"width:40px;\">Anzahl</th>
                            <th>Bauauftrag</th>
                            <th style=\"width:150px;\">Start</th>
                            <th style=\"width:150px;\">Ende</th>
                            <th style=\"width:150px;\">Verbleibend</th>
                            <th style=\"width:80px;\">Aktionen</th>
                        </tr>";
                    $first = false;
                }

                echo "<tr>";
                echo "<td id=\"objcount\">" . $data->count . "</td>";
                echo "<td>" . $ships[$data->shipId]->name . "</td>";
                echo "<td>" . df($absolute_starttime, 1) . "</td>";
                echo "<td>" . df($absolute_starttime + $data->endTime - $data->startTime, 1) . "</td>";
                echo "<td>" . tf($data->endTime - time()) . "</td>";
                echo "<td id=\"cancel\">";
                if ($cancelable) {
                    echo "<a href=\"?page=$page&amp;cancel=" . $data->id . "\" onclick=\"return confirm('Soll dieser Auftrag wirklich abgebrochen werden?');\">Abbrechen</a>";
                } else {
                    echo "-";
                }
                echo "</td>
                </tr>";

                //Setzt die Startzeit des nächsten Schiffes, auf die Endzeit des jetztigen Schiffes
                $absolute_starttime = $data->endTime;
            }
            tableEnd();
        }



        /***********************
         * Schiffe auflisten    *
         ***********************/

        $cnt = 0;
        if (count($shipCategories) > 0) {
            foreach ($shipCategories as $category) {
                if (!isset($shipsByCategory[$category->id])) {
                    continue;
                }

                tableStart($category->name);
                $ccnt = 0;

                // Auflistung der Schiffe (auch diese, die noch nicht gebaut wurden)
                if (isset($shipsByCategory[$category->id]) && count($shipsByCategory[$category->id]) > 0) {
                    //Einfache Ansicht
                    if ($properties->itemShow != 'full') {
                        echo '<tr>
                                        <th colspan="2" class="tbltitle">Schiff</th>
                                        <th class="tbltitle">Zeit</th>
                                        <th class="tbltitle">' . RES_METAL . '</th>
                                        <th class="tbltitle">' . RES_CRYSTAL . '</th>
                                        <th class="tbltitle">' . RES_PLASTIC . '</th>
                                        <th class="tbltitle">' . RES_FUEL . '</th>
                                        <th class="tbltitle">' . RES_FOOD . '</th>
                                        <th class="tbltitle">Anzahl</th>
                                    </tr>';
                    }

                    $buildingLevels = $buildingRepository->getBuildingLevels($planet->id);
                    foreach ($shipsByCategory[$category->id] as $shipData) {
                        // Prüfen ob Schiff gebaut werden kann
                        $build_ship = 1;
                        // Gebäude prüfen
                        foreach ($requirements->getBuildingRequirements($shipData->id) as $requirement) {
                            if (($buildingLevels[$requirement->requiredBuildingId] ?? 0) < $requirement->requiredLevel) {
                                $build_ship = 0;
                            }
                        }
                        // Technologien prüfen
                        foreach ($requirements->getTechnologyRequirements($shipData->id) as $requirement) {
                            if (($techlist[$requirement->requiredTechnologyId] ?? 0) < $requirement->requiredLevel) {
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
                            $btime = ($shipCosts[$shipData->id]->sum()) / $config->getInt('global_time') * $config->getFloat('ship_build_time') * $time_boni_factor * $cu->specialist->shipTime;
                            $btime_min = $btime * (0.1 - ($gen_tech_level / 100));
                            $peopleOptimized = ceil(($btime - $btime_min) / $config->getInt('people_work_done'));

                            //Mindest Bauzeit
                            if ($btime_min < SHIPYARD_MIN_BUILD_TIME) {
                                $btime_min = SHIPYARD_MIN_BUILD_TIME;
                            }

                            $btime = ceil($btime - $shipyard->peopleWorking * $config->getInt('people_work_done'));
                            if ($btime < $btime_min) {
                                $btime = $btime_min;
                            }

                            //Nahrungskosten berechnen
                            $food_costs = $shipyard->peopleWorking * $config->getInt('people_food_require');

                            //Nahrungskosten versteckt übermitteln
                            echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"" . $food_costs . "\" />";
                            $food_costs += $shipCosts[$shipData->id]->food;



                            //Errechnet wie viele Schiffe von diesem Typ maximal Gebaut werden können mit den aktuellen Rohstoffen

                            //Titan
                            if ($shipCosts[$shipData->id]->metal > 0) {
                                $build_cnt_metal = floor($planet->resMetal / $shipCosts[$shipData->id]->metal);
                            } else {
                                $build_cnt_metal = 99999999999;
                            }

                            //Silizium
                            if ($shipCosts[$shipData->id]->crystal > 0) {
                                $build_cnt_crystal = floor($planet->resCrystal / $shipCosts[$shipData->id]->crystal);
                            } else {
                                $build_cnt_crystal = 99999999999;
                            }

                            //PVC
                            if ($shipCosts[$shipData->id]->plastic > 0) {
                                $build_cnt_plastic = floor($planet->resPlastic / $shipCosts[$shipData->id]->plastic);
                            } else {
                                $build_cnt_plastic = 99999999999;
                            }

                            //Tritium
                            if ($shipCosts[$shipData->id]->fuel > 0) {
                                $build_cnt_fuel = floor($planet->resFuel / $shipCosts[$shipData->id]->fuel);
                            } else {
                                $build_cnt_fuel = 99999999999;
                            }

                            //Nahrung
                            if ($food_costs > 0) {
                                $build_cnt_food = floor($planet->resFood / $food_costs);
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
                                $tm_cnt = "Es k&ouml;nnen maximal " . nf($ship_max_build) . " Schiffe gebaut werden.";
                            }
                            //Zuwenig Rohstoffe. Wartezeit errechnen
                            elseif ($ship_max_build == 0) {
                                $bwait = [];
                                $bwmsg = [];
                                //Wartezeit Titan
                                if ($planet->prodMetal > 0) {
                                    $bwait['metal'] = ceil(($shipCosts[$shipData->id]->metal - $planet->resMetal) / $planet->prodMetal * 3600);
                                    $bwmsg['metal'] = tm("Fehlender Rohstoff", nf($shipCosts[$shipData->id]->metal - $planet->resMetal) . " Titan<br />Bereit in " . tf($bwait['metal']) . "");
                                } else {
                                    $bwait['metal'] = 0;
                                    $bwmsg['metal'] = '';
                                }

                                //Wartezeit Silizium
                                if ($planet->prodCrystal > 0) {
                                    $bwait['crystal'] = ceil(($shipCosts[$shipData->id]->crystal - $planet->resCrystal) / $planet->prodCrystal * 3600);
                                    $bwmsg['crystal'] = tm("Fehlender Rohstoff", nf($shipCosts[$shipData->id]->crystal - $planet->resCrystal) . " Silizium<br />Bereit in " . tf($bwait['crystal']) . "");
                                } else {
                                    $bwait['crystal'] = 0;
                                    $bwmsg['crystal'] = '';
                                }

                                //Wartezeit PVC
                                if ($planet->prodPlastic > 0) {
                                    $bwait['plastic'] = ceil(($shipCosts[$shipData->id]->plastic - $planet->resPlastic) / $planet->prodPlastic * 3600);
                                    $bwmsg['plastic'] = tm("Fehlender Rohstoff", nf($shipCosts[$shipData->id]->plastic - $planet->resPlastic) . " PVC<br />Bereit in " . tf($bwait['plastic']) . "");
                                } else {
                                    $bwait['plastic'] = 0;
                                    $bwmsg['plastic'] = '';
                                }

                                //Wartezeit Tritium
                                if ($planet->prodFuel > 0) {
                                    $bwait['fuel'] = ceil(($shipCosts[$shipData->id]->fuel - $planet->resFuel) / $planet->prodFuel * 3600);
                                    $bwmsg['fuel'] = tm("Fehlender Rohstoff", nf($shipCosts[$shipData->id]->fuel - $planet->resFuel) . " Tritium<br />Bereit in " . tf($bwait['fuel']) . "");
                                } else {
                                    $bwait['fuel'] = 0;
                                    $bwmsg['fuel'] = '';
                                }

                                //Wartezeit Nahrung
                                if ($planet->prodFood > 0) {
                                    $bwait['food'] = ceil(($food_costs - $planet->resFood) / $planet->prodFood * 3600);
                                    $bwmsg['food'] = tm("Fehlender Rohstoff", nf($food_costs - $planet->resFood) . " Nahrung<br />Bereit in " . tf($bwait['food']) . "");
                                } else {
                                    $bwait['food'] = 0;
                                    $bwmsg['food'] = '';
                                }

                                //Maximale Wartezeit ermitteln
                                $bwmax = max($bwait['metal'], $bwait['crystal'], $bwait['plastic'], $bwait['fuel'], $bwait['food']);

                                $tm_cnt = "Rohstoffe verf&uuml;gbar in " . tf($bwmax) . "";
                            } else {
                                $tm_cnt = "";
                            }

                            //Stellt Rohstoff Rot dar, wenn es von diesem zu wenig auf dem Planeten hat
                            //Titan
                            if ($shipCosts[$shipData->id]->metal > $planet->resMetal) {
                                $ress_style_metal = "style=\"color:red;\" " . $bwmsg['metal'] . "";
                            } else {
                                $ress_style_metal = "";
                            }

                            //Silizium
                            if ($shipCosts[$shipData->id]->crystal > $planet->resCrystal) {
                                $ress_style_crystal = "style=\"color:red;\" " . $bwmsg['crystal'] . "";
                            } else {
                                $ress_style_crystal = "";
                            }

                            //PVC
                            if ($shipCosts[$shipData->id]->plastic > $planet->resPlastic) {
                                $ress_style_plastic = "style=\"color:red;\" " . $bwmsg['plastic'] . "";
                            } else {
                                $ress_style_plastic = "";
                            }

                            //Tritium
                            if ($shipCosts[$shipData->id]->fuel > $planet->resFuel) {
                                $ress_style_fuel = "style=\"color:red;\" " . $bwmsg['fuel'] . "";
                            } else {
                                $ress_style_fuel = "";
                            }

                            //Nahrung
                            if ($food_costs > $planet->resFood) {
                                $ress_style_food = "style=\"color:red;\" " . $bwmsg['food'] . "";
                            } else {
                                $ress_style_food = "";
                            }

                            // Sicherstellen dass epische Spezialschiffe nur auf dem Hauptplanet gebaut werden
                            if (!$shipData->special || $planet->mainPlanet) {
                                // Speichert die Anzahl gebauter Schiffe in eine Variable
                                if (isset($shiplist[$shipData->id][$planet->id])) {
                                    $shiplist_count = $shiplist[$shipData->id][$planet->id];
                                } else {
                                    $shiplist_count = 0;
                                }

                                // Volle Ansicht
                                if ($properties->itemShow == 'full') {
                                    if ($ccnt > 0) {
                                        echo "<tr>
                                                    <td colspan=\"5\" style=\"height:5px;\"></td>
                                            </tr>";
                                    }
                                    $s_img = IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipData->id . "_middle." . IMAGE_EXT;

                                    echo "<tr>
                                        <th colspan=\"5\" height=\"20\">" . $shipData->name . "</th>
                                    </tr>
                                    <tr>
                                        <td width=\"120\" height=\"120\" rowspan=\"3\">";

                                    //Bei Spezialschiffen nur Bild ohne Link darstellen
                                    if ($shipData->special) {
                                        echo "<img src=\"" . $s_img . "\" width=\"120\" height=\"120\" border=\"0\" />";
                                    }
                                    //Bei normalen Schiffen mit Hilfe verlinken
                                    else {
                                        echo "<a href=\"" . HELP_URL . "&amp;id=" . $shipData->id . "\" title=\"Info zu diesem Schiff anzeigen\">
                                    <img src=\"" . $s_img . "\" width=\"120\" height=\"120\" border=\"0\" /></a>";
                                    }
                                    echo "</td>
                                        <td colspan=\"4\" valign=\"top\">" . $shipData->shortComment . "</td>
                                    </tr>
                                    <tr>
                                        <th  height=\"30\">Vorhanden:</th>
                                        <td colspan=\"3\">" . nf($shiplist_count) . "</td>
                                    </tr>
                                    <tr>
                                        <th height=\"30\">Bauzeit</th>
                                        <td>" . tf($btime) . "</td>";

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
                                    <th height=\"20\" width=\"110\">" . RES_METAL . ":</th>
                                    <th height=\"20\" width=\"97\">" . RES_CRYSTAL . ":</th>
                                    <th height=\"20\" width=\"98\">" . RES_PLASTIC . ":</th>
                                    <th height=\"20\" width=\"97\">" . RES_FUEL . ":</th>
                                    <th height=\"20\" width=\"98\">" . RES_FOOD . "</th></tr>";
                                    echo "<tr>
                                    <td height=\"20\" width=\"110\" " . $ress_style_metal . ">
                                        " . nf($shipCosts[$shipData->id]->metal) . "
                                    </td>
                                    <td height=\"20\" width=\"25%\" " . $ress_style_crystal . ">
                                        " . nf($shipCosts[$shipData->id]->crystal) . "
                                    </td>
                                    <td height=\"20\" width=\"25%\" " . $ress_style_plastic . ">
                                        " . nf($shipCosts[$shipData->id]->plastic) . "
                                    </td>
                                    <td height=\"20\" width=\"25%\" " . $ress_style_fuel . ">
                                        " . nf($shipCosts[$shipData->id]->fuel) . "
                                    </td>
                                    <td height=\"20\" width=\"25%\" " . $ress_style_food . ">
                                        " . nf($food_costs) . "
                                    </td>
                                </tr>";
                                }
                                //Einfache Ansicht der Schiffsliste
                                else {
                                    $s_img = IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipData->id . "_small." . IMAGE_EXT;

                                    echo "<tr>
                                        <td>";

                                    //Spezialschiffe ohne Link darstellen
                                    if ($shipData->special) {
                                        echo "<img src=\"$s_img\" width=\"40\" height=\"40\" border=\"0\" /></td>";
                                    }
                                    //Normale Schiffe mit Link zur Hilfe darstellen
                                    else {
                                        echo "<a href=\"" . HELP_URL . "&amp;id=" . $shipData->id . "\"><img src=\"" . $s_img . "\" width=\"40\" height=\"40\" border=\"0\" /></a></td>";
                                    }

                                    echo "<th width=\"30%\">
                                            <span style=\"font-weight:500\">" . $shipData->name . "<br/>
                                            Gebaut:</span> " . nf($shiplist_count) . "
                                        </th>
                                        <td width=\"13%\">" . tf($btime) . "</td>
                                        <td width=\"10%\" " . $ress_style_metal . ">" . nf($shipCosts[$shipData->id]->metal) . "</td>
                                        <td width=\"10%\" " . $ress_style_crystal . ">" . nf($shipCosts[$shipData->id]->crystal) . "</td>
                                        <td width=\"10%\" " . $ress_style_plastic . ">" . nf($shipCosts[$shipData->id]->plastic) . "</td>
                                        <td width=\"10%\" " . $ress_style_fuel . ">" . nf($shipCosts[$shipData->id]->fuel) . "</td>
                                        <td width=\"10%\" " . $ress_style_food . ">" . nf($food_costs) . "</td>";

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
                }
                // Es gibt noch keine Schiffe
                else {
                    echo "<tr><td align=\"center\" colspan=\"3\">Es gibt noch keine Schiffe!</td></tr>";
                }

                tableEnd();
            }
            // Baubutton anzeigen
            if ($cnt > 0) {
                echo "<input type=\"submit\" name=\"submit\" value=\"Bauauftr&auml;ge &uuml;bernehmen\"/><br/><br/>";
            }
        } else {
            echo "<br>Noch keine Kategorien definiert!<br>";
        }
    }
} else {
    // Titel
    echo "<h1>Raumschiffswerft des Planeten " . $planet->name . "</h1>";

    // Ressourcen anzeigen
    echo $resourceBoxDrawer->getHTML($planet);
    info_msg("Die Raumschiffswerft wurde noch nicht gebaut!");
}
echo "</form>";
