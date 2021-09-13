<?PHP

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseCategoryRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseQueueItem;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\Defense\DefenseRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Defense\DefenseSearch;
use EtoA\Defense\DefenseSort;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\StringUtils;
use EtoA\Specialist\SpecialistService;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserPropertiesRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];
/** @var DefenseRepository $defenseRepository */
$defenseRepository = $app[DefenseRepository::class];
/** @var DefenseQueueRepository $defenseQueueRepository */
$defenseQueueRepository = $app[DefenseQueueRepository::class];
/** @var DefenseRequirementRepository $defenseRequirementRepository */
$defenseRequirementRepository = $app[DefenseRequirementRepository::class];
/** @var DefenseCategoryRepository $defenseCategoryRepository */
$defenseCategoryRepository = $app[DefenseCategoryRepository::class];
/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];
/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];
/** @var GameLogRepository $gameLogRepository */
$gameLogRepository = $app[GameLogRepository::class];
$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

/** @var SpecialistService $specialistService */
$specialistService = $app[SpecialistService::class];

//Definition für "Info" Link
define("ITEMS_TBL", "defense");
define("REQ_TBL", "def_requirements");
define("REQ_ITEM_FLD", "obj_id");
define("ITEM_ID_FLD", "def_id");
define("ITEM_NAME_FLD", "def_name");
define("RACE_TO_ADD", " AND (def_race_id=0 OR def_race_id='" . $cu->raceId . "')");
define("ITEM_SHOW_FLD", "def_show");
define("ITEM_ORDER_FLD", "def_order");
define("NO_ITEMS_MSG", "In dieser Kategorie gibt es keine Verteidigungsanlagen!");
define("HELP_URL", "?page=help&site=defense");

// Absolute minimal Bauzeit in Sekunden
define("DEFENSE_MIN_BUILD_TIME", $config->getInt('shipyard_min_build_time'));

// Ben. Level für Autragsabbruch
define("DEFQUEUE_CANCEL_MIN_LEVEL", $config->getInt('defqueue_cancel_min_level'));

define("DEFQUEUE_CANCEL_START", $config->getFloat('defqueue_cancel_start'));

define("DEFQUEUE_CANCEL_FACTOR", $config->getFloat('defqueue_cancel_factor'));

define("DEFQUEUE_CANCEL_END", $config->getFloat('defqueue_cancel_end'));

$planet = $planetRepo->find($cp->id);

// BEGIN SKRIPT //

//Tabulator var setzten (für das fortbewegen des cursors im forumular)
$tabulator = 1;

//Fabrik Level und Arbeiter laden
$factoryBuilding = $buildingRepository->getEntityBuilding($cu->getId(), $planet->id, BuildingId::DEFENSE);

// Prüfen ob Fabrik gebaut ist
if ($factoryBuilding !== null && $factoryBuilding->currentLevel > 0) {
    $peopleWorking = $buildingRepository->getPeopleWorking($planet->id);

    // Titel
    echo "<h1>Waffenfabrik (Stufe " . $factoryBuilding->currentLevel . ") des Planeten " . $planet->name . "</h1>";

    // Ressourcen anzeigen
    echo $resourceBoxDrawer->getHTML($planet);

    // Prüfen ob dieses Gebäude deaktiviert wurde
    if ($factoryBuilding->isDeactivated()) {
        iBoxStart("Geb&auml;ude nicht bereit");
        echo "Diese Waffenfabrik ist bis " . date("d.m.Y H:i", $factoryBuilding->deactivated) . " deaktiviert.";
        iBoxEnd();
    }
    // Werft anzeigen
    else {
        /****************************
         *  Sortiereingaben speichern *
         ****************************/
        if (
            count($_POST) > 0 && isset($_POST['sort_submit'])
            && StringUtils::hasAlphaDotsOrUnderlines($_POST['sort_value']) && StringUtils::hasAlphaDotsOrUnderlines($_POST['sort_way'])
        ) {
            $properties->itemOrderDef = $_POST['sort_value'];
            $properties->itemOrderWay = $_POST['sort_way'];
            $userPropertiesRepository->storeProperties($cu->id, $properties);
        }

        //
        // Läd alle benötigten Daten in PHP-Arrays
        //

        // Voraussetzungen laden
        $requirements = $defenseRequirementRepository->getAll();

        //Technologien laden und Gentechlevel definieren
        $gen_tech_level = 0;
        /** @var TechnologyRepository $technologyRepository */
        $technologyRepository = $app[TechnologyRepository::class];
        $techlist = $technologyRepository->getTechnologyLevels($cu->getId());

        if (isset($techlist[TechnologyId::GEN]) && $techlist[TechnologyId::GEN] > 0) {
            $gen_tech_level = $techlist[TechnologyId::GEN];
        }

        //Gebäude laden
        $buildlist = $buildingRepository->getBuildingLevels($planet->id);

        // Gebaute Verteidigung laden
        $deflist = $defenseRepository->getEntityDefenseCounts($cu->getId(), $planet->id);

        // Bauliste vom aktuellen Planeten laden (wird nach "Abbrechen" nochmals geladen)
        $queueItems = $defenseQueueRepository->searchQueueItems(DefenseQueueSearch::create()->entityId($planet->id)->endAfter($time));
        /** @var array<int, DefenseQueueItem> $queue */
        $queue = [];
        /** @var array<int, int> $queueItemCounts */
        $queueItemCounts = [];
        foreach ($queueItems as $item) {
            $queue[$item->id] = $item;
            if (!isset($queueItemCounts[$item->defenseId])) {
                $queueItemCounts[$item->defenseId] = 0;
            } else {
                $queueItemCounts[$item->defenseId] += $item->count;
            }
        }

        $specialist = $specialistService->getSpecialistOfUser($cu->id);
        $specialistDefenseCostFactor = $specialist !== null ? $specialist->costsDefense : 1;
        $specialistDefenseTimeFactor = $specialist !== null ? $specialist->timeDefense : 1;

        /** @var \EtoA\Defense\Defense[] $defs */
        $defs = [];
        /** @var \EtoA\Defense\Defense[][] $defenseByCategory */
        $defenseByCategory = [];
        /** @var PreciseResources[] $defenseCosts */
        $defenseCosts = [];
        // Alle Verteidigung laden
        $categories = $defenseCategoryRepository->getAllCategories();
        //Verteidigungsordnung des Users beachten
        $items = $defenseDataRepository->searchDefense(DefenseSearch::create()->showOrBuildable()->raceOrNull($cu->raceId), DefenseSort::specialWithUserSort($properties->itemOrderDef, $properties->itemOrderWay));
        foreach ($items as $item) {
            $defs[$item->id] = $item;
            $defenseCosts[$item->id] = PreciseResources::createFromBase($item->getCosts())->multiply($specialistDefenseCostFactor);
            $defenseByCategory[$item->catId][] = $item;
        }

        // Bauliste vom Planeten laden und nach Verteidigung zusammenfassen
        $queueFields = 0;
        foreach ($queueItemCounts as $defenseId => $count) {
            $queueFields += $count * $defs[$defenseId]->fields;
        }

        //Berechnet freie Felder
        $fields_available = $planet->fields + $planet->fieldsExtra - $planet->fieldsUsed - $queueFields;

        // level zählen welches die Waffenfabrik über dem angegeben level ist und faktor berechnen
        $need_bonus_level = $factoryBuilding->currentLevel - $config->param1Int('build_time_boni_waffenfabrik');
        if ($need_bonus_level <= 0) {
            $time_boni_factor = 1;
        } else {
            $time_boni_factor = 1 - ($need_bonus_level * ($config->getInt('build_time_boni_waffenfabrik') / 100));
        }
        $people_working = $factoryBuilding->peopleWorking;

        // Faktor der zurückerstatteten Ressourcen bei einem Abbruch des Auftrags berechnen
        if ($factoryBuilding->currentLevel >= DEFQUEUE_CANCEL_MIN_LEVEL) {
            $cancel_res_factor = min(DEFQUEUE_CANCEL_END, DEFQUEUE_CANCEL_START + (($factoryBuilding->currentLevel - DEFQUEUE_CANCEL_MIN_LEVEL) * DEFQUEUE_CANCEL_FACTOR));
        } else {
            $cancel_res_factor = 0;
        }

        // Infos anzeigen
        tableStart("Fabrik-Infos");
        echo '<colgroup><col style="width:400px;"/><col/></colgroup>';
        if ($specialist !== null && $specialist->costsDefense != 1) {
            echo "<tr><td>Kostenreduktion durch " . $specialist->name . ":</td><td>" . StringUtils::formatPercentString($specialist->costsDefense) . '</td></tr>';
        }
        if ($specialist !== null && $specialist->timeDefense != 1) {
            echo "<tr><td>Bauzeitverringerung durch " . $specialist->name . ":</td><td>" . StringUtils::formatPercentString($specialist->timeDefense) . "</td></tr>";
        }
        echo "<tr><td>Eingestellte Arbeiter:</td><td>" . StringUtils::formatNumber($people_working);
        if (count($queue) === 0) {
            echo '&nbsp;<a href="javascript:;" onclick="toggleBox(\'changePeople\');">[&Auml;ndern]</a>';
        }
        echo "</td></tr>";
        if ($peopleWorking->defense > 0) {
            echo '<tr><td>Zeitreduktion durch Arbeiter pro Auftrag:</td><td><span id="people_work_done">' . StringUtils::formatTimespan($config->getInt('people_work_done') * $peopleWorking->defense) . '</span></td></tr>';
            echo '<tr><td>Nahrungsverbrauch durch Arbeiter pro Auftrag:</td><td><span id="people_food_require">' . StringUtils::formatNumber($config->getInt('people_food_require') * $peopleWorking->defense) . '</span></td></tr>';
        }
        if ($gen_tech_level  > 0) {
            echo '<tr><td>Gentechnologie:</td><td>' . $gen_tech_level . '</td></tr>';
            echo '<tr><td>Minimale Bauzeit (mit Arbeiter):</td><td>Bauzeit * ' . (0.1 - ($gen_tech_level / 100)) . '</td></tr>';
        }
        echo '<tr><td>Bauzeitverringerung:</td><td>';
        if ($need_bonus_level >= 0) {
            echo StringUtils::formatPercentString($time_boni_factor) . " durch Stufe " . $factoryBuilding->currentLevel . "";
        } else {
            echo "Stufe " . $config->param1Int('build_time_boni_waffenfabrik') . " erforderlich!";
        }
        echo '</td></tr>';
        if ($cancel_res_factor > 0) {
            echo "<tr><td>Ressourcenrückgabe bei Abbruch:</td><td>" . ($cancel_res_factor * 100) . "% (ohne " . ResourceNames::FOOD . ", " . (DEFQUEUE_CANCEL_END * 100) . "% maximal)</td></tr>";
            $cancelable = true;
        } else {
            echo "<tr><td>Abbruchmöglichkeit:</td><td>Stufe " . DEFQUEUE_CANCEL_MIN_LEVEL . " erforderlich!</td></tr>";
            $cancelable = false;
        }
        tableEnd();

        $peopleFree = floor($planet->people) - $peopleWorking->total + $peopleWorking->defense;
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
                                        value="' . StringUtils::formatNumber($peopleWorking->defense) . '"
                                        onkeyup="updatePeopleWorkingBox(this.value,\'-1\',\'-1\');"/>
                        </td>
                        </tr>
                        <tr>
                            <th>Zeitreduktion</th>
                            <td><input  type="text"
                                        name="timeReduction"
                                        id="timeReduction"
                                        value="' . StringUtils::formatTimespan($config->getInt('people_work_done') * $peopleWorking->defense) . '"
                                        onkeyup="updatePeopleWorkingBox(\'-1\',this.value,\'-1\');" /></td>
                        </tr>
                            <th>Nahrungsverbrauch</th>
                            <td><input  type="text"
                                        name="foodUsing"
                                        id="foodUsing"
                                        value="' . StringUtils::formatNumber($config->getInt('people_food_require') * $peopleWorking->defense) . '"
                                        onkeyup="updatePeopleWorkingBox(\'-1\',\'-1\',this.value);" /></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                                <div id="changeWorkingPeopleError" style="display:none;">&nbsp;</div>
                                <input type="submit" value="Speichern" name="submit_people_form" id="submit_people_form" />&nbsp;';
        echo '<div id="changePeople" style="display:none;">';
        tableStart("Arbeiter der Waffenfabrik zuteilen");
        echo '<form id="changeWorkingPeople" method="post" action="?page=' . $page . '">
            ' . $box . '</form>';
        tableEnd();
        echo '</div>';

        // people working changed
        if (isset($_POST['submit_people_form'])) {
            $toBeAssignedPeople = StringUtils::parseFormattedNumber($_POST['peopleWorking']);
            $free = $cp->people - $peopleWorking->total + $peopleWorking->getById(BuildingId::DEFENSE);
            if (count($queue) === 0 && $free > $toBeAssignedPeople && !$factoryBuilding->isUnderConstruction()) {
                $buildingRepository->setPeopleWorking($planet->id, BuildingId::DEFENSE, $toBeAssignedPeople);
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
        foreach (DefenseSort::USER_SORT_VALUES as $value => $name) {
            echo "<option value=\"" . $value . "\"";
            if ($properties->itemOrderDef === $value) {
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

        echo "<form action=\"?page=" . $page . "\" method=\"post\">";


        /****************************
         *  Schiffe in Auftrag geben *
         ****************************/

        if (count($_POST) > 0 && isset($_POST['submit']) && checker_verify()) {
            tableStart();
            echo "<tr><th>Ergebnisse des Bauauftrags</th></tr>";

            $totalMetal = 0;
            $totalCrystal = 0;
            $totalPlastic = 0;
            $totalFuel = 0;
            $totalFood = 0;

            // Endzeit bereits laufender Aufträge laden
            $end_time = time();
            if (count($queue) > 0) {
                // Speichert die letzte Endzeit, da das Array $queue nach queue_starttime (und somit auch endtime) sortiert ist
                foreach ($queue as $queueItem) {
                    $end_time = $queueItem->endTime;
                }
            }

            //
            // Bauaufträge speichern
            //
            $counter = 0;
            foreach ($_POST['build_count'] as $def_id => $build_cnt) {
                $build_cnt = StringUtils::parseFormattedNumber($build_cnt);

                if ($build_cnt > 0 && isset($defs[$def_id])) {
                    // Zählt die Anzahl Verteidigugn dieses Typs im ganzen Account...
                    $def_count = 0;
                    // ... auf den Planeten
                    if (isset($deflist[$def_id])) {
                        $def_count += $deflist[$def_id];
                    }

                    // ... in der Bauliste
                    if (isset($queueItemCounts[$def_id])) {
                        $def_count += $queueItemCounts[$def_id];
                    }

                    //Anzahl überprüfen, ob diese die maximalzahl übersteigt, gegebenenfalls ändern
                    if ($build_cnt + $def_count > $defs[$def_id]->maxCount && $defs[$def_id]->maxCount != 0) {
                        $build_cnt = max(0, $defs[$def_id]->maxCount - $def_count);
                    }

                    //Wenn der User nicht genug freie Felder hat, die Anzahl Anlagen drosseln
                    if ($defs[$def_id]->fields > 0 && $fields_available - $defs[$def_id]->fields * $build_cnt < 0) {
                        $build_cnt = floor($fields_available / $defs[$def_id]->fields);
                    }

                    // TODO: Überprüfen
                    //Wenn der User nicht genug Ress hat, die Anzahl Schiffe drosseln
                    //Titan
                    $bf = [];
                    $bc = [];
                    if ($defenseCosts[$def_id]->metal > 0) {
                        $bf['metal'] = $planet->resMetal / $defenseCosts[$def_id]->metal;
                    } else {
                        $bc['metal'] = 0;
                    }
                    //Silizium
                    if ($defenseCosts[$def_id]->crystal > 0) {
                        $bf['crystal'] = $planet->resCrystal / $defenseCosts[$def_id]->crystal;
                    } else {
                        $bc['crystal'] = 0;
                    }
                    //PVC
                    if ($defenseCosts[$def_id]->plastic > 0) {
                        $bf['plastic'] = $planet->resPlastic / $defenseCosts[$def_id]->plastic;
                    } else {
                        $bc['plastic'] = 0;
                    }
                    //Tritium
                    if ($defenseCosts[$def_id]->fuel > 0) {
                        $bf['fuel'] = $planet->resFuel / $defenseCosts[$def_id]->fuel;
                    } else {
                        $bc['fuel'] = 0;
                    }
                    //Nahrung
                    if (intval($_POST['additional_food_costs']) > 0 || $defenseCosts[$def_id]->food > 0) {
                        $bf['food'] = $planet->resFood / (intval($_POST['additional_food_costs']) + $defenseCosts[$def_id]->food);
                    } else {
                        $bc['food'] = 0;
                    }

                    //Anzahl Drosseln
                    if ($build_cnt > floor(min($bf))) {
                        $build_cnt = floor(min($bf));
                    }

                    //Check for Rene-Bug
                    $additional_food_costs = $people_working * $config->getInt('people_food_require');
                    if ($additional_food_costs != intval($_POST['additional_food_costs']) || intval($_POST['additional_food_costs']) < 0) {
                        $build_cnt = 0;
                    }

                    //Anzahl muss grösser als 0 sein
                    if ($build_cnt > 0) {
                        //Errechne Kosten pro auftrag schiffe
                        $bc['metal'] = $defenseCosts[$def_id]->metal * $build_cnt;
                        $bc['crystal'] = $defenseCosts[$def_id]->crystal * $build_cnt;
                        $bc['plastic'] = $defenseCosts[$def_id]->plastic * $build_cnt;
                        $bc['fuel'] = $defenseCosts[$def_id]->fuel * $build_cnt;
                        $bc['food'] = (intval($_POST['additional_food_costs']) + $defenseCosts[$def_id]->food) * $build_cnt;

                        // Bauzeit pro Def berechnen
                        $btime = $defenseCosts[$def_id]->getSum()
                            / $config->getInt('global_time') * $config->getFloat('def_build_time')
                            * $time_boni_factor
                            * $specialistDefenseTimeFactor;

                        // TODO: Überprüfen
                        //Rechnet zeit wenn arbeiter eingeteilt sind
                        $btime_min = $btime * (0.1 - ($gen_tech_level / 100));
                        if ($btime_min < DEFENSE_MIN_BUILD_TIME) $btime_min = DEFENSE_MIN_BUILD_TIME;
                        $btime = $btime - $people_working * $config->getInt('people_work_done');
                        if ($btime < $btime_min) $btime = $btime_min;
                        $obj_time = ceil($btime);

                        // Gesamte Bauzeit berechnen
                        $duration = $build_cnt * $obj_time;

                        // Setzt Starzeit des Auftrages, direkt nach dem letzten Auftrag
                        $start_time = $end_time;
                        $end_time = $start_time + $duration;

                        // Auftrag speichern
                        $deflist_id = $defenseQueueRepository->add($cu->getId(), $def_id, $planet->id, $build_cnt, $start_time, (int) $end_time, (int) $obj_time);

                        $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, BuildingId::DEFENSE, true);

                        // Queue Array aktualisieren
                        $queue_data = new DefenseQueueItem([
                            'queue_id' => $deflist_id,
                            'queue_def_id' => $def_id,
                            'queue_cnt' => $build_cnt,
                            'queue_user_id' => $cu->getId(),
                            'queue_entity_id' => $planet->id,
                            'queue_starttime' => $start_time,
                            'queue_endtime' => $end_time,
                            'queue_objtime' => $obj_time,
                            'queue_build_type' => 0,
                            'queue_user_click_time' => time(),
                        ]);

                        $queue[$deflist_id] = $queue_data;
                        if (!isset($queueItemCounts[$def_id])) {
                            $queueItemCounts[$def_id] = $build_cnt;
                        } else {
                            $queueItemCounts[$def_id] += $build_cnt;
                        }

                        echo "<tr><td>" . StringUtils::formatNumber($build_cnt) . " " . $defs[$def_id]->name . " in Auftrag gegeben!</td></tr>";

                        //Rohstoffe summieren, diese werden nach der Schleife abgezogen
                        $totalMetal += $bc['metal'];
                        $totalCrystal += $bc['crystal'];
                        $totalPlastic += $bc['plastic'];
                        $totalFuel += $bc['fuel'];
                        $totalFood += $bc['food'];

                        //Felder subtrahieren
                        $fields_available -= $build_cnt * $defs[$def_id]->fields;

                        $log_text = "[b]Verteidigungsauftrag Bauen[/b]

                        [b]Start:[/b] " . date("d.m.Y H:i:s", $start_time) . "
                        [b]Ende:[/b] " . date("d.m.Y H:i:s", (int) $end_time) . "
                        [b]Dauer:[/b] " . StringUtils::formatTimespan($duration) . "
                        [b]Dauer pro Einheit:[/b] " . StringUtils::formatTimespan($obj_time) . "
                        [b]Waffenfabrik Level:[/b] " . $factoryBuilding->currentLevel . "
                        [b]Eingesetzte Bewohner:[/b] " . StringUtils::formatNumber($people_working) . "
                        [b]Gen-Tech Level:[/b] " . $gen_tech_level . "
                        [b]Eingesetzter Spezialist:[/b] " . ($specialist !== null ? $specialist->name : "Kein Spezialist") . "

                        [b]Kosten[/b]
                        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($bc['metal']) . "
                        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($bc['crystal']) . "
                        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($bc['plastic']) . "
                        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($bc['fuel']) . "
                        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($bc['food']) . "

                        [b]Rohstoffe auf dem Planeten[/b]
                        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($planet->resMetal - $totalMetal) . "
                        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($planet->resCrystal - $totalCrystal) . "
                        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($planet->resPlastic - $totalPlastic) . "
                        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($planet->resFuel - $totalFuel) . "
                        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($planet->resFood - $totalFood);

                        $gameLogRepository->add(GameLogFacility::DEF, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $planet->id, $def_id, 1, $build_cnt);
                    } else {
                        echo "<tr><td>" . $defs[$def_id]->name . ": Zu wenig Rohstoffe für diese Anzahl!</td></tr>";
                    }
                    $counter++;
                }
            }

            $planetRepo->addResources($planet->id, -$totalMetal, -$totalCrystal, -$totalPlastic, -$totalFuel, -$totalFood);

            if ($counter == 0) {
                echo "<tr><td>Keine Verteidigung gew&auml;hlt!</td></tr>";
            }
            tableEnd();
        }

        checker_init();

        /*********************
         * Auftrag abbrechen  *
         *********************/
        if (isset($_GET['cancel']) && intval($_GET['cancel']) > 0 && $cancelable) {
            $id = intval($_GET['cancel']);
            if (isset($queue[$id])) {
                //Zu erhaltende Rohstoffe errechnen
                $obj_cnt = min(ceil(($queue[$id]->endTime - max($time, $queue[$id]->startTime)) / $queue[$id]->objectTime), $queue[$id]->count);
                echo "Breche den Bau von " . $obj_cnt . " " . $defs[$queue[$id]->defenseId]->name . " ab...<br/>";

                $ret = [];
                $ret['metal'] = $defenseCosts[$queue[$id]->defenseId]->metal * $obj_cnt * $cancel_res_factor;
                $ret['crystal'] = $defenseCosts[$queue[$id]->defenseId]->crystal * $obj_cnt * $cancel_res_factor;
                $ret['plastic'] = $defenseCosts[$queue[$id]->defenseId]->plastic * $obj_cnt * $cancel_res_factor;
                $ret['fuel'] = $defenseCosts[$queue[$id]->defenseId]->fuel * $obj_cnt * $cancel_res_factor;
                $ret['food'] = $defenseCosts[$queue[$id]->defenseId]->food * $obj_cnt * $cancel_res_factor;

                // Daten für Log speichern
                $def_name = $defs[$queue[$id]->defenseId]->name;
                $defId = $queue[$id]->defenseId;
                $queue_count = $queue[$id]->count;
                $queue_objtime = $queue[$id]->objectTime;
                $start_time = $queue[$id]->startTime;
                $end_time = $queue[$id]->endTime;

                //Felder addieren
                $fields_available += $queue_count * $defs[$queue[$id]->defenseId]->fields;

                //Auftrag löschen
                $defenseQueueRepository->deleteQueueItem($id);

                $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, BuildingId::DEFENSE, false);

                // Nachkommende Aufträge werden Zeitlich nach vorne verschoben
                $queueItems = $defenseQueueRepository->searchQueueItems(DefenseQueueSearch::create()->entityId($planet->id)->startEqualAfter($end_time));
                if (count($queueItems) > 0) {
                    $new_starttime = max($start_time, time());
                    foreach ($queueItems as $item) {
                        $new_endtime = $new_starttime + $item->endTime - $item->startTime;
                        $item->startTime = $new_starttime;
                        $item->endTime = $new_endtime;
                        $defenseQueueRepository->saveQueueItem($item);

                        // Aktualisiert das Queue-Array
                        $queue[$item->id] = $item;

                        $new_starttime = $new_endtime;
                    }
                }

                $queueItemCounts[$defId] -= $queue_count;
                // Auftrag aus Array löschen
                unset($queue[$id]);

                //Rohstoffe dem Planeten gutschreiben und aktualisieren
                $planetRepo->addResources($planet->id, $ret['metal'], $ret['crystal'], $ret['plastic'], $ret['fuel'], $ret['food']);

                echo "Der Auftrag wurde abgebrochen!<br/><br/>";

                //Log schreiben
                $log_text = "[b]Verteidigungsauftrag Abbruch[/b]

                [b]Auftragsdauer:[/b] " . StringUtils::formatTimespan($queue_objtime * $queue_count) . "

                [b]Erhaltene Rohstoffe[/b]
                [b]Faktor:[/b] " . $cancel_res_factor . "
                [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($ret['metal']) . "
                [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($ret['crystal']) . "
                [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($ret['plastic']) . "
                [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($ret['fuel']) . "
                [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($ret['food']) . "

                [b]Rohstoffe auf dem Planeten[/b]
                [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($planet->resMetal + $ret['metal']) . "
                [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($planet->resCrystal + $ret['crystal']) . "
                [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($planet->resPlastic + $ret['plastic']) . "
                [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($planet->resFuel + $ret['fuel']) . "
                [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($planet->resFood + $ret['food']);

                //Log Speichern
                $gameLogRepository->add(GameLogFacility::DEF, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $planet->id, $defId, 0, $queue_count);
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
            foreach ($queue as $queueItem) {
                // Listet nur Die Datensätze aus, die auch eine Verteidiguns ID beinhalten, da ev. der Datensatz mit NULL gleichgesetzt wurde
                if ($queueItem->defenseId > 0) {
                    if ($first) {
                        $obj_t_remaining = ((($queueItem->endTime - $time) / $queueItem->objectTime) - floor(($queueItem->endTime - $time) / $queueItem->objectTime)) * $queueItem->objectTime;
                        if ($obj_t_remaining == 0) {
                            $obj_t_remaining = $queueItem->objectTime;
                        }
                        $obj_time = $queueItem->objectTime;

                        $absolute_starttime = $queueItem->startTime;

                        $obj_t_passed = $queueItem->objectTime - $obj_t_remaining;
                        echo "<tr>
                                <th colspan=\"2\">Aktuell</th>
                                <th style=\"width:150px;\">Start</th>
                                <th style=\"width:150px;\">Ende</th>
                                <th style=\"width:80px;\" colspan=\"2\">Verbleibend</th>
                            </tr>
                            <tr>
                            <td colspan=\"2\">" . $defs[$queueItem->defenseId]->name . "</td>
                            <td>" . StringUtils::formatDate($time - $obj_t_passed) . "</td>
                            <td>" . StringUtils::formatDate($time + $obj_t_remaining) . "</td>
                            <td colspan=\"2\">" . StringUtils::formatTimespan($obj_t_remaining) . "</td>
                        </tr>
                        <tr>
                            <th style=\"width:40px;\">Anzahl</th>
                            <th>Bauauftrag</th>
                            <th style=\"width:150px;\">Start</th>
                            <th style=\"width:150px;\">Ende</th>
                            <th style=\"width:150px;\">Verbleibend</th>
                            <th style=\"width:80px;\">Aktionen</th>
                        </tr>";
                        $first = false;
                    }

                    echo "<tr>
                            <td id=\"objcount\">" . $queueItem->count . "</td>
                            <td>" . $defs[$queueItem->defenseId]->name . "</td>
                            <td>" . StringUtils::formatDate($absolute_starttime) . "</td>
                            <td>" . StringUtils::formatDate($absolute_starttime + $queueItem->endTime - $queueItem->startTime) . "</td>
                            <td>" . StringUtils::formatTimespan($queueItem->endTime - time()) . "</td>
                            <td id=\"cancel\">";
                    if ($cancelable) {
                        echo "<a href=\"?page=$page&amp;cancel=" . $queueItem->id . "\" onclick=\"return confirm('Soll dieser Auftrag wirklich abgebrochen werden?');\">Abbrechen</a>";
                    } else {
                        echo "-";
                    }
                    echo "</td>
                        </tr>";

                    //Setzt die Startzeit des nächsten Schiffes, auf die Endzeit des jetztigen Schiffes
                    $absolute_starttime = $queueItem->endTime;
                }
            }
            tableEnd();
        }

        /***********************
         * Verteidigung auflisten    *
         ***********************/

        $cnt = 0;
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                tableStart($category->name);
                $ccnt = 0;

                // Auflistung der Verteidigung (auch diese, die noch nicht gebaut wurden)
                if (isset($defenseByCategory[$category->id])) {
                    //Einfache Ansicht
                    if ($properties->itemShow !== 'full') {
                        echo "<tr>
                                        <th colspan=\"2\">Anlage</th>
                                        <th>Zeit</th>
                                        <th>" . ResourceNames::METAL . "</th>
                                        <th>" . ResourceNames::CRYSTAL . "</th>
                                        <th>" . ResourceNames::PLASTIC . "</th>
                                        <th>" . ResourceNames::FUEL . "</th>
                                        <th>" . ResourceNames::FOOD . "</th>
                                        <th>Anzahl</th>
                                    </tr>";
                    }

                    foreach ($defenseByCategory[$category->id] as $defense) {
                        // Prüfen ob Schiff gebaut werden kann
                        $build_def = 1;
                        // Gebäude prüfen
                        foreach ($requirements->getBuildingRequirements($defense->id) as $requirement) {
                            if (!isset($buildlist[$requirement->requiredBuildingId]) || $buildlist[$requirement->requiredBuildingId] < $requirement->requiredLevel) {
                                $build_def = 0;
                            }
                        }
                        // Technologien prüfen
                        foreach ($requirements->getTechnologyRequirements($defense->id) as $requirement) {
                            if (!isset($techlist[$requirement->requiredTechnologyId]) || $techlist[$requirement->requiredTechnologyId] < $requirement->requiredLevel) {
                                $build_def = 0;
                            }
                        }

                        // Schiffdatensatz zeigen wenn die Voraussetzungen erfüllt sind und das Schiff in diese Kategorie gehört
                        if ($build_def == 1) {
                            // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
                            $def_count = 0;
                            // ... auf den Planeten
                            if (isset($deflist[$defense->id])) {
                                $def_count += $deflist[$defense->id];
                            }
                            // ... in der Bauliste
                            if (isset($queueItemCounts[$defense->id])) {
                                $def_count += $queueItemCounts[$defense->id];
                            }

                            // Bauzeit berechnen
                            $btime = $defenseCosts[$defense->id]->getSum() / $config->getInt('global_time') * $config->getFloat('def_build_time') * $time_boni_factor * $specialistDefenseTimeFactor;
                            $btime_min = $btime * (0.1 - ($gen_tech_level / 100));
                            $peopleOptimized = ceil(($btime - $btime_min) / $config->getInt('people_work_done'));

                            //Mindest Bauzeit
                            if ($btime_min < DEFENSE_MIN_BUILD_TIME) {
                                $btime_min = DEFENSE_MIN_BUILD_TIME;
                            }

                            $btime = ceil($btime - $people_working * $config->getInt('people_work_done'));
                            if ($btime < $btime_min) {
                                $btime = $btime_min;
                            }

                            //Nahrungskosten berechnen
                            $food_costs = $people_working * $config->getInt('people_food_require');

                            //Nahrungskosten versteckt übermitteln
                            echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"" . $food_costs . "\" />";
                            $food_costs += $defenseCosts[$defense->id]->food;


                            //Errechnet wie viele Verteidigung von diesem Typ maximal Gebaut werden können mit den aktuellen Rohstoffen

                            //Felder
                            if ($defense->fields > 0) {
                                $build_cnt_fields = floor($fields_available / $defense->fields);
                            } else {
                                $build_cnt_fields = 99999999999;
                            }

                            //Titan
                            if ($defenseCosts[$defense->id]->metal > 0) {
                                $build_cnt_metal = floor($planet->resMetal / $defenseCosts[$defense->id]->metal);
                            } else {
                                $build_cnt_metal = 99999999999;
                            }

                            //Silizium
                            if ($defenseCosts[$defense->id]->crystal > 0) {
                                $build_cnt_crystal = floor($planet->resCrystal / $defenseCosts[$defense->id]->crystal);
                            } else {
                                $build_cnt_crystal = 99999999999;
                            }

                            //PVC
                            if ($defenseCosts[$defense->id]->plastic > 0) {
                                $build_cnt_plastic = floor($planet->resPlastic / $defenseCosts[$defense->id]->plastic);
                            } else {
                                $build_cnt_plastic = 99999999999;
                            }

                            //Tritium
                            if ($defenseCosts[$defense->id]->fuel > 0) {
                                $build_cnt_fuel = floor($planet->resFuel / $defenseCosts[$defense->id]->fuel);
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
                            if ($defense->maxCount != 0) {
                                $max_cnt = $defense->maxCount - $def_count;
                            } else {
                                $max_cnt = 99999999999;
                            }

                            //Effetiv max. baubare Verteidigung in Betrachtung der Rohstoffe und des Baumaximums
                            $def_max_build = min($build_cnt_metal, $build_cnt_crystal, $build_cnt_plastic, $build_cnt_fuel, $build_cnt_food, $max_cnt, $build_cnt_fields);

                            //Tippbox Nachricht generieren
                            //X Schiffe baubar
                            if ($def_max_build > 0) {
                                $tm_cnt = "Es k&ouml;nnen maximal " . StringUtils::formatNumber($def_max_build) . " Anlagen gebaut werden.";
                            }
                            //Zuwenig Rohstoffe. Wartezeit errechnen
                            elseif ($def_max_build == 0) {
                                $bwait = [];
                                $bwmsg = [];
                                //Wartezeit Titan
                                if ($planet->prodMetal > 0) {
                                    $bwait['metal'] = ceil(($defenseCosts[$defense->id]->metal - $planet->resMetal) / $planet->prodMetal * 3600);
                                    $bwmsg['metal'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($defenseCosts[$defense->id]->metal - $planet->resMetal) . " Titan<br />Bereit in " . StringUtils::formatTimespan($bwait['metal']) . "");
                                } else {
                                    $bwait['metal'] = 0;
                                }

                                //Wartezeit Silizium
                                if ($planet->prodCrystal > 0) {
                                    $bwait['crystal'] = ceil(($defenseCosts[$defense->id]->crystal - $planet->resCrystal) / $planet->prodCrystal * 3600);
                                    $bwmsg['crystal'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($defenseCosts[$defense->id]->crystal - $planet->resCrystal) . " Silizium<br />Bereit in " . StringUtils::formatTimespan($bwait['crystal']) . "");
                                } else {
                                    $bwait['crystal'] = 0;
                                }

                                //Wartezeit PVC
                                if ($planet->prodPlastic > 0) {
                                    $bwait['plastic'] = ceil(($defenseCosts[$defense->id]->plastic - $planet->resPlastic) / $planet->prodPlastic * 3600);
                                    $bwmsg['plastic'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($defenseCosts[$defense->id]->plastic - $planet->resPlastic) . " PVC<br />Bereit in " . StringUtils::formatTimespan($bwait['plastic']) . "");
                                } else {
                                    $bwait['plastic'] = 0;
                                }

                                //Wartezeit Tritium
                                if ($planet->prodFuel > 0) {
                                    $bwait['fuel'] = ceil(($defenseCosts[$defense->id]->fuel - $planet->resFuel) / $planet->prodFuel * 3600);
                                    $bwmsg['fuel'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($defenseCosts[$defense->id]->fuel - $planet->resFuel) . " Tritium<br />Bereit in " . StringUtils::formatTimespan($bwait['fuel']) . "");
                                } else {
                                    $bwait['fuel'] = 0;
                                }

                                //Wartezeit Nahrung
                                if ($planet->prodFood > 0) {
                                    $bwait['food'] = ceil(($food_costs - $planet->resFood) / $planet->prodFood * 3600);
                                    $bwmsg['food'] = tm("Fehlender Rohstoff", StringUtils::formatNumber($food_costs - $planet->resFood) . " Nahrung<br />Bereit in " . StringUtils::formatTimespan($bwait['food']) . "");
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
                            if ($defenseCosts[$defense->id]->metal > $planet->resMetal && isset($bwmsg['metal'])) {
                                $ress_style_metal = "style=\"color:red;\" " . $bwmsg['metal'] . "";
                            } else {
                                $ress_style_metal = "";
                            }

                            //Silizium
                            if ($defenseCosts[$defense->id]->crystal > $planet->resCrystal && isset($bwmsg['crystal'])) {
                                $ress_style_crystal = "style=\"color:red;\" " . $bwmsg['crystal'] . "";
                            } else {
                                $ress_style_crystal = "";
                            }

                            //PVC
                            if ($defenseCosts[$defense->id]->plastic > $planet->resPlastic && isset($bwmsg['plastic'])) {
                                $ress_style_plastic = "style=\"color:red;\" " . $bwmsg['plastic'] . "";
                            } else {
                                $ress_style_plastic = "";
                            }

                            //Tritium
                            if ($defenseCosts[$defense->id]->fuel > $planet->resFuel && isset($bwmsg['fuel'])) {
                                $ress_style_fuel = "style=\"color:red;\" " . $bwmsg['fuel'] . "";
                            } else {
                                $ress_style_fuel = "";
                            }

                            //Nahrung
                            if ($food_costs > $planet->resFood && isset($bwmsg['food'])) {
                                $ress_style_food = "style=\"color:red;\" " . $bwmsg['food'] . "";
                            } else {
                                $ress_style_food = "";
                            }

                            // Speichert die Anzahl gebauter Schiffe in eine Variable
                            if (isset($deflist[$defense->id])) {
                                $deflist_count = $deflist[$defense->id];
                            } else {
                                $deflist_count = 0;
                            }

                            // Volle Ansicht
                            if ($properties->itemShow == 'full') {
                                if ($ccnt > 0) {
                                    echo "<tr>
                                            <td colspan=\"5\" style=\"height:5px;\"></td>
                                    </tr>";
                                }
                                $s_img = $defense->getImagePath('medium');

                                echo "<tr>
                                        <th colspan=\"5\" height=\"20\">" . $defense->name . "</th>
                                    </tr>
                                    <tr>
                                        <td width=\"120\" height=\"120\" rowspan=\"3\">
                                            <a href=\"" . HELP_URL . "&amp;id=" . $defense->id . "\" title=\"Info zu dieser Anlage anzeigen\">
                                            <img src=\"" . $s_img . "\" width=\"120\" height=\"120\" border=\"0\" /></a>
                                        </td>
                                        <td colspan=\"4\" valign=\"top\">" . $defense->shortComment . "</td>
                                    </tr>
                                    <tr>
                                        <th  height=\"30\">Vorhanden:</th>
                                        <td>" . StringUtils::formatNumber($deflist_count) . "</td>
                                        <th>Felder pro Einheit:</th>
                                        <td>" . StringUtils::formatNumber($defense->fields) . "</td>
                                    </tr>
                                    <tr>
                                        <th height=\"30\">Bauzeit</th>
                                        <td>" . StringUtils::formatTimespan($btime) . "</td>";

                                //Maximale Anzahl erreicht
                                if ($def_count >= $defense->maxCount && $defense->maxCount !== 0) {
                                    echo "<th height=\"30\" colspan=\"2\"><i>Maximalanzahl erreicht</i></th>";
                                } else {
                                    echo "<th height=\"30\">In Aufrag geben:</th>
                                            <td><input type=\"text\" value=\"0\" name=\"build_count[" . $defense->id . "]\" id=\"build_count_" . $defense->id . "\" size=\"4\" maxlength=\"9\" " . tm("", $tm_cnt) . " tabindex=\"" . $tabulator . "\" onkeyup=\"FormatNumber(this.id,this.value, " . $def_max_build . ", '', '');\"/> St&uuml;ck<br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_" . $defense->id . "').value=" . $def_max_build . ";\">max</a>";
                                    if (count($queue) === 0) {
                                        echo '&nbsp;<a href="#changePeople" onclick="javascript:if(document.getElementById(\'changePeople\').style.display==\'none\') {toggleBox(\'changePeople\')};updatePeopleWorkingBox(\'' . $peopleOptimized . '\',\'-1\',\'^-1\');">optimieren</a>';
                                    }
                                    echo "</td>";
                                }

                                echo "</tr>
                                        <tr>
                                            <th height=\"20\" width=\"110\">" . ResourceNames::METAL . ":</th>
                                            <th height=\"20\" width=\"97\">" . ResourceNames::CRYSTAL . ":</th>
                                            <th height=\"20\" width=\"98\">" . ResourceNames::PLASTIC . ":</th>
                                            <th height=\"20\" width=\"97\">" . ResourceNames::FUEL . ":</th>
                                            <th height=\"20\" width=\"98\">" . ResourceNames::FOOD . "</th></tr>
                                        <tr>
                                        <td height=\"20\" width=\"110\" " . $ress_style_metal . ">
                                            " . StringUtils::formatNumber($defenseCosts[$defense->id]->metal) . "
                                        </td>
                                        <td height=\"20\" width=\"25%\" " . $ress_style_crystal . ">
                                            " . StringUtils::formatNumber($defenseCosts[$defense->id]->crystal) . "
                                        </td>
                                        <td height=\"20\" width=\"25%\" " . $ress_style_plastic . ">
                                            " . StringUtils::formatNumber($defenseCosts[$defense->id]->plastic) . "
                                        </td>
                                        <td height=\"20\" width=\"25%\" " . $ress_style_fuel . ">
                                            " . StringUtils::formatNumber($defenseCosts[$defense->id]->fuel) . "
                                        </td>
                                        <td height=\"20\" width=\"25%\" " . $ress_style_food . ">
                                            " . StringUtils::formatNumber($food_costs) . "
                                        </td>
                                    </tr>";
                            }
                            //Einfache Ansicht der Schiffsliste
                            else {
                                $s_img = $defense->getImagePath('small');

                                echo "<tr>
                                        <td>
                                            <a href=\"" . HELP_URL . "&amp;id=" . $defense->id . "\"><img src=\"" . $s_img . "\" width=\"40\" height=\"40\" border=\"0\" /></a>
                                        </td>
                                        <th width=\"30%\">
                                            <span style=\"font-weight:500\">" . $defense->name . "<br/>
                                            Gebaut:</span> " . StringUtils::formatNumber($deflist_count) . "
                                        </th>
                                        <td width=\"13%\">" . StringUtils::formatTimespan($btime) . "</td>
                                        <td width=\"10%\" " . $ress_style_metal . ">" . StringUtils::formatNumber($defenseCosts[$defense->id]->metal) . "</td>
                                        <td width=\"10%\" " . $ress_style_crystal . ">" . StringUtils::formatNumber($defenseCosts[$defense->id]->crystal) . "</td>
                                        <td width=\"10%\" " . $ress_style_plastic . ">" . StringUtils::formatNumber($defenseCosts[$defense->id]->plastic) . "</td>
                                        <td width=\"10%\" " . $ress_style_fuel . ">" . StringUtils::formatNumber($defenseCosts[$defense->id]->fuel) . "</td>
                                        <td width=\"10%\" " . $ress_style_food . ">" . StringUtils::formatNumber($food_costs) . "</td>";

                                //Maximale Anzahl erreicht
                                if ($def_count >= $defense->maxCount && $defense->maxCount != 0) {
                                    echo "<td>Max</td></tr>";
                                } else {
                                    echo "<td><input type=\"text\" value=\"0\" id=\"build_count_" . $defense->id . "\" name=\"build_count[" . $defense->id . "]\" size=\"5\" maxlength=\"9\" " . tm("", $tm_cnt) . " tabindex=\"" . $tabulator . "\" onkeyup=\"FormatNumber(this.id,this.value, " . $def_max_build . ", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_" . $defense->id . "').value=" . $def_max_build . ";\">max</a></td></tr>";
                                }
                            }

                            $tabulator++;
                            $cnt++;
                            $ccnt++;
                        }
                    }

                    // Es können keine Schiffe gebaut werden
                    if ($ccnt == 0) {
                        echo "<tr>
                                    <td colspan=\"9\" height=\"30\" align=\"center\">
                                        Es k&ouml;nnen noch keine Anlagen gebaut werden!<br>
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
    echo "<h1>Waffenfabrik des Planeten " . $planet->name . "</h1>";

    // Ressourcen anzeigen
    echo $resourceBoxDrawer->getHTML($planet);
    info_msg("Die Waffenfabrik wurde noch nicht gebaut!");
}
echo "</form>";
