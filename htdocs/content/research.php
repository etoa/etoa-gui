<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Race\RaceDataRepository;
use EtoA\Support\StringUtils;
use EtoA\Specialist\SpecialistService;
use EtoA\Support\BBCodeUtils;
use EtoA\Technology\Technology;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologyRequirement;
use EtoA\Technology\TechnologyRequirementRepository;
use EtoA\Technology\TechnologyTypeRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyService;
use EtoA\Universe\Resources\BuildCosts;
use EtoA\Universe\Resources\PreciseResources;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

/** @var TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[TechnologyDataRepository::class];

/** @var TechnologyRepository $technologyRepository */
$technologyRepository = $app[TechnologyRepository::class];

/** @var GameLogRepository $gameLogRepository */
$gameLogRepository = $app[GameLogRepository::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

/** @var TechnologyService $technologyService */
$technologyService = $app[TechnologyService::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var TechnologyRequirementRepository $technologyRequirementRepository */
$technologyRequirementRepository = $app[TechnologyRequirementRepository::class];

/** @var SpecialistService $specialistService */
$specialistService = $app[SpecialistService::class];

/** @var RaceDataRepository $raceRepository */
$raceRepository = $app[RaceDataRepository::class];

/** @var TechnologyTypeRepository $technologyTypeRepository */
$technologyTypeRepository = $app[TechnologyTypeRepository::class];

/** @var BuildingDataRepository $buildingDataRepository */
$buildingDataRepository = $app[BuildingDataRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

define('NUM_BUILDINGS_PER_ROW', 5);
define('CELL_WIDTH', 120);
define('TABLE_WIDTH', 'auto');

// Aktiviert / Deaktiviert Bildfilter
if ($properties->imageFilter) {
    $use_img_filter = true;
} else {
    $use_img_filter = false;
}

if (isset($cp)) {
    $planet = $planetRepo->find($cp->id);
    $user = $userRepository->getUser($cu->id);

    $bl = new BuildList($planet->id, $cu->id);

    $researchBuilding = $buildingRepository->getEntityBuilding($cu->getId(), $planet->id, TECH_BUILDING_ID);
    if ($researchBuilding !== null && $researchBuilding->currentLevel > 0) {
        define("GEN_TECH_LEVEL", $technologyRepository->getTechnologyLevel($cu->getId(), GEN_TECH_ID));
        $minBuildTimeFactor = (0.1 - (GEN_TECH_LEVEL / 100));

        // Überschrift
        echo "<h1>Forschungslabor (Stufe " . $researchBuilding->currentLevel . ") des Planeten " . $planet->name . "</h1>";
        echo $resourceBoxDrawer->getHTML($planet);

        // Forschungsliste laden && Gentech level definieren
        $userTechnologies = $technologyRepository->findForUser($cu->getId());
        $building_something = false;
        $building_gen = false;
        $techlist = [];
        foreach ($userTechnologies as $userTechnology) {
            $techlist[$userTechnology->technologyId] = $userTechnology;
            // Check, ob schon eine Technik geforscht wird
            // BUGFIX: this is tech, NO check for same planet,
            // because only one tech at the same time per user

            if ($userTechnology->buildType > 2) {
                if ($userTechnology->technologyId === 23) {
                    $building_gen = true;
                } else {
                    $building_something = true;
                }
            }
        }

        $new_people_set = false;
        // people working changed
        if (isset($_POST['submit_people_form_gen'])) {

            $set_people = StringUtils::parseFormattedNumber($_POST['peopleWorking']);
            if (!$building_gen && $bl->setPeopleWorking(PEOPLE_BUILDING_ID, $set_people, true)) {
                success_msg("Arbeiter zugeteilt!");
                $new_people_set = true;
            } else {
                error_msg('Arbeiter konnten nicht zugeteilt werden!');
            }
        }

        if (isset($_POST['submit_people_form'])) {
            $set_people = StringUtils::parseFormattedNumber($_POST['peopleWorking']);
            if (!$building_something && $bl->setPeopleWorking(TECH_BUILDING_ID, $set_people, true)) {
                success_msg("Arbeiter zugeteilt!");
                $new_people_set = true;
            } else {
                error_msg('Arbeiter konnten nicht zugeteilt werden!');
            }
        }

        // reload buildlist and techlist in case the number of workers has changed.
        $bl = new BuildList($planet->id, $cu->id);

        $minBuildTimeFactor = (0.1 - (GEN_TECH_LEVEL / 100));

        // People working in the tech building.
        $peopleWorking = $buildingRepository->getPeopleWorking($planet->id);
        $peopleWorkingResearch = $peopleWorking->research;
        $peopleWorkingGen = $peopleWorking->people;

        $peopleTimeReduction = $config->getInt('people_work_done');
        $peopleFoodConsumption = $config->getInt('people_food_require');

        //level zählen welches das forschungslabor über dem angegeben level ist und faktor berechnen
        $need_bonus_level = $researchBuilding->currentLevel - $config->param1Int('build_time_boni_forschungslabor');
        if ($need_bonus_level <= 0) {
            $time_boni_factor = 1;
        } else {
            $time_boni_factor = max($config->param2Float('build_time_boni_forschungslabor'), 1 - ($need_bonus_level * ($config->getInt('build_time_boni_forschungslabor') / 100)));
        }

        //
        // Läd alle benötigten Daten in Arrays
        //

        // Load built buildings
        $buildlist = $buildingRepository->getBuildingLevels($planet->id);

        // Load requirements
        $requirements = $technologyRequirementRepository->getAll();

        $specialist = $specialistService->getSpecialistOfUser($cu->id);
        $race = $raceRepository->getRace($cu->raceId);

        $bid = 0;

        if ((isset($_GET['id']) && intval($_GET['id']) > 0) || (count($_POST) > 0    && checker_verify())) {
            if (isset($_GET['id']) && intval($_GET['id']) > 0) {
                $bid = intval($_GET['id']);
            } else {
                foreach ($_POST as $k => $v) {
                    if (stristr($k, '_x')) {
                        $bid = intval(preg_replace('/show_([0-9]+)_x/', '\1', $k));
                        break;
                    }
                }
                if ($bid == 0 && isset($_POST['show'])) {
                    $bid = intval($_POST['show']);
                }
                if ($bid == 0 && isset($_POST['id'])) {
                    $bid = intval($_POST['id']);
                }
            }
        }

        // cache checker to add it to several forms
        ob_start();
        checker_init();
        $checker = ob_get_contents();
        ob_end_clean();

        $peopleFree = $bid == GEN_TECH_ID
            ? floor($planet->people) - $peopleWorking->total + ($peopleWorkingGen)
            : floor($planet->people) - $peopleWorking->total + ($peopleWorkingResearch);

        $peopleOptimized = 0;
        $technology = null;
        if ($bid > 0) {
            // Forschungsdaten laden
            $technology = $technologyDataRepository->getTechnology($bid);
            if ($technology !== null) {
                $level = isset($techlist[$technology->id]) ? $techlist[$technology->id]->currentLevel : 0;
                $costs = $technologyService->calculateCosts($technology, $level, $user);
                $timeRequired = $technologyService->calculateBuildTime($costs, $user, $planet);

                $maxReduction = $timeRequired - $timeRequired * $minBuildTimeFactor;
                $peopleOptimized = ceil($maxReduction / $config->getInt('people_work_done'));
            }
        }

        // create box to change people working
        $box =    '
            <input type="hidden" name="workDone" id="workDone" value="' . $config->getInt('people_work_done') . '" />
            <input type="hidden" name="foodRequired" id="foodRequired" value="' . $config->getInt('people_food_require') . '" />
            <input type="hidden" name="peopleFree" id="peopleFree" value="' . $peopleFree . '" />
            <input type="hidden" name="foodAvailable" id="foodAvailable" value="' . $planet->resFood . '" />';
        if ($properties->itemShow == 'full' && $bid > 0) {
            $box .= '<input type="hidden" name="peopleOptimized" id="peopleOptimized" value="' . $peopleOptimized . '" />';
        } else {
            $box .= '<input type="hidden" name="peopleOptimized" id="peopleOptimized" value="0" />';
        }

        if ($bid == 23) {
            $form_button = 'submit_people_form_gen';
            $people = $peopleWorkingGen;
        } else {
            $form_button = 'submit_people_form';
            $people = $peopleWorkingResearch;
        }

        $box .= '	<tr>
                            <th>Eingestellte Arbeiter</th>
                            <td>
                                <input 	type="text"
                                        name="peopleWorking"
                                        id="peopleWorking"
                                        value="' . StringUtils::formatNumber($people) . '"
                                        onkeyup="updatePeopleWorkingBox(this.value,\'-1\',\'-1\');"/>
                        </td>
                    </tr>';
        $box .=        '<tr>
                            <th>Zeitreduktion</th>
                            <td><input	type="text"
                                        name="timeReduction"
                                        id="timeReduction"
                                        value="' . StringUtils::formatTimespan($config->getInt('people_work_done') * $people) . '"
                                        onkeyup="updatePeopleWorkingBox(\'-1\',this.value,\'-1\');" /></td>
                        </tr>
                            <th>Nahrungsverbrauch</th>
                            <td><input	type="text"
                                        name="foodUsing"
                                        id="foodUsing"
                                        value="' . StringUtils::formatNumber($config->getInt('people_food_require') * $people) . '"
                                        onkeyup="updatePeopleWorkingBox(\'-1\',\'-1\',this.value);" /></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                                <div id="changeWorkingPeopleError" style="display:none;">&nbsp;</div>
                                <input type="submit" value="Speichern" name="' . $form_button . '" id="' . $form_button . '" />&nbsp;';

        if ($bid > 0) {
            $box .= '<input type="button" value="Optimieren" onclick="updatePeopleWorkingBox(\'' . $peopleOptimized . '\',\'-1\',\'^-1\');">';
        }
        $box .= '
                    </td>
                </tr>';

        tableStart("Allgemeine-Infos");

        echo '<colgroup><col style="width:400px;"/><col/></colgroup>';
        // Specialist
        if ($specialist !== null && $specialist->costsTechnologies != 1) {
            echo "<tr><td>Kostenreduktion durch " . $specialist->name . ":</td><td>" . StringUtils::formatPercentString($specialist->costsTechnologies) . "</td></tr>";
        }
        if ($specialist !== null && $specialist->timeTechnologies != 1) {
            echo "<tr><td>Forschungszeitverringerung durch " . $specialist->name . ":</td><td>" . StringUtils::formatPercentString($specialist->timeTechnologies) . "</td></tr>";
        }
        // Building level time bonus
        echo "<tr><td>Forschungszeitverringerung:</td><td>";
        if ($need_bonus_level >= 0) {
            echo StringUtils::formatPercentString($time_boni_factor) . " durch Stufe " . $researchBuilding->currentLevel . " (-" . ((1 - $config->param2Float('build_time_boni_forschungslabor')) * 100) . "% maximum)";
        } else {
            echo "Stufe " . $config->param1Int('build_time_boni_forschungslabor') . " erforderlich!";
        }
        echo '</td></tr>';

        // Genetics technology level
        if (GEN_TECH_LEVEL > 0) {
            echo "<tr><td>Gentechnologie:</td><td>" . GEN_TECH_LEVEL . "</td></tr>";
            echo "<tr><td>Minimale Forschungszeit (mit Arbeiter):</td><td>Forschungszeit * $minBuildTimeFactor</td></tr>";
        }

        tableEnd();

        tableStart("Labor-Infos");
        echo '<colgroup><col span="3" style="width:400px;"/><col/></colgroup>';

        // Worker
        echo "<tr><td><th>Normal</th></td><th>Gentech</th></tr>";
        echo "<tr><td>Eingestellte Arbeiter:</td><td>" . StringUtils::formatNumber($peopleWorkingResearch);

        if (($building_something <> 1) && ($bid > 0) && ($bid <> 23)) {
            echo '&nbsp;<a id ="link" href="javascript:;" onclick="toggleBox(\'changePeople\');">[Ändern]</a>';
        }
        echo '</td><td>' . StringUtils::formatNumber($peopleWorkingGen);

        if (($building_gen <> 1) && ($bid == 23)) {
            echo '&nbsp;<a id ="link" href="javascript:;" onclick="toggleBox(\'changePeople\');">[Ändern]</a>';
        }

        echo '</td></tr>';
        if (($peopleWorkingResearch > 0) || ($peopleWorkingGen > 0)) {
            echo "<tr><td>Zeitreduktion durch Arbeiter pro Auftrag:</td><td>" . StringUtils::formatTimespan($peopleTimeReduction * $peopleWorkingResearch) . "</td><td>" . StringUtils::formatTimespan($peopleTimeReduction * $peopleWorkingGen) . "</td></tr>";
            echo "<tr><td>Nahrungsverbrauch durch Arbeiter pro Auftrag:</td><td>" . StringUtils::formatNumber($peopleFoodConsumption * $peopleWorkingResearch) . "</td><td>" . StringUtils::formatNumber($peopleFoodConsumption * $peopleWorkingGen) . "</td></tr>";
        }

        tableEnd();

        echo '<div id="changePeople" style="display:none;">';
        tableStart("Arbeiter im Forschungslabor zuteilen");
        echo '<form id="changeWorkingPeople" action="?page=' . $page . '&amp;id=' . $bid . '" method="post">
            ' . $checker . $box . '</form>';
        tableEnd();
        echo '</div>';

        //
        //Forschung erforschen/abbrechen
        //
        if ($bid > 0) {
            if ($technology !== null) {
                // Prüft, ob Technik schon erforscht wurde und setzt Variablen
                if (isset($techlist[$technology->id])) {
                    $built = true;

                    $currentLevel = $techlist[$technology->id]->currentLevel;
                    $b_status = $techlist[$technology->id]->buildType;
                    $start_time = $techlist[$technology->id]->startTime;
                    $end_time = $techlist[$technology->id]->endTime;
                    $planet_id = $techlist[$technology->id]->entityId;
                }
                // Tech wurde noch nicht erforscht. Es werden Default Werte vergeben
                else {
                    $built = false;

                    $currentLevel = 0;
                    $b_status = 0;
                    $start_time = 0;
                    $end_time = 0;
                    $planet_id = 0;
                }

                $costs = $technologyService->calculateCosts($technology, $currentLevel, $user);
                $timeRequired = $technologyService->calculateBuildTime($costs, $user, $planet);

                // Berechnet mindest Bauzeit in beachtung von Gentechlevel
                $btime_min = $timeRequired * $minBuildTimeFactor;
                if ($bid != GEN_TECH_ID) {
                    $timeRequired = $timeRequired - $peopleWorkingResearch * $peopleTimeReduction;
                    if ($timeRequired < $btime_min) {
                        $timeRequired = $btime_min;
                    }
                    $costs->food += $peopleWorkingResearch * $peopleFoodConsumption;
                } else {
                    $timeRequired = $timeRequired - $peopleWorkingGen * $peopleTimeReduction;
                    if ($timeRequired < $btime_min) {
                        $timeRequired = $btime_min;
                    }
                    $costs->food += $peopleWorkingGen * $peopleFoodConsumption;
                }

                //
                // Befehle ausführen
                //

                if (isset($_POST['command_build']) && $b_status == 0) {
                    if (!$building_something) {
                        if ($costs->isCoveredOnPlanet($planet)) {
                            $start_time = time();
                            $end_time = time() + $timeRequired;
                            $technologyRepository->updateBuildStatus($cu->getId(), $planet->id, $technology->id, 3, $start_time, (int) $end_time);
                            $buildingId = $technology->id === GEN_TECH_ID ? BuildingId::PEOPLE : BuildingId::TECHNOLOGY;
                            $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, $buildingId, true);

                            $planet_id = $planet->id;

                            //Rohstoffe vom Planeten abziehen und aktualisieren
                            $planetRepo->removeResources($planet->id, PreciseResources::createFromCosts($costs));

                            $b_status = 3;

                            //Log schreiben
                            $log_text = "[b]Forschung Ausbau[/b]

                            [b]Erforschungsdauer:[/b] " . StringUtils::formatTimespan($timeRequired) . "
                            [b]Ende:[/b] " . date("d.m.Y H:i:s", (int) $end_time) . "
                            [b]Forschungslabor Level:[/b] " . $researchBuilding->currentLevel . "
                            [b]Eingesetzte Bewohner:[/b] " . StringUtils::formatNumber($peopleWorkingResearch) . "
                            [b]Gen-Tech Level:[/b] " . GEN_TECH_LEVEL . "
                            [b]Eingesetzter Spezialist:[/b] " . ($specialist !== null ? $specialist->name : "Kein Spezialist") . "

                            [b]Kosten[/b]
                            [b]" . RES_METAL . ":[/b] " . StringUtils::formatNumber($costs->metal) . "
                            [b]" . RES_CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs->crystal) . "
                            [b]" . RES_PLASTIC . ":[/b] " . StringUtils::formatNumber($costs->crystal) . "
                            [b]" . RES_FUEL . ":[/b] " . StringUtils::formatNumber($costs->fuel) . "
                            [b]" . RES_FOOD . ":[/b] " . StringUtils::formatNumber($costs->food) . "

                            [b]Restliche Rohstoffe auf dem Planeten[/b]
                            [b]" . RES_METAL . ":[/b] " . StringUtils::formatNumber($planet->resMetal - $costs->metal) . "
                            [b]" . RES_CRYSTAL . ":[/b] " . StringUtils::formatNumber($planet->resCrystal - $costs->crystal) . "
                            [b]" . RES_PLASTIC . ":[/b] " . StringUtils::formatNumber($planet->resPlastic - $costs->plastic) . "
                            [b]" . RES_FUEL . ":[/b] " . StringUtils::formatNumber($planet->resFuel - $costs->fuel) . "
                            [b]" . RES_FOOD . ":[/b] " . StringUtils::formatNumber($planet->resFood - $costs->food);

                            $gameLogRepository->add(GameLogFacility::TECH, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $planet->id, $technology->id, $b_status, $currentLevel);

                            echo '<script>toggleBox(\'link\'); </script>';
                        } else {
                            echo "<i>Forschung kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!</i><br/><br/>";
                        }
                    } else {
                        echo "<i>Forschung kann nicht gestartet werden, es wird bereits an einer Technologie geforscht!</i><br/><br/>";
                    }
                }

                if (isset($_POST['command_cbuild']) && $b_status == 3) {
                    if (isset($techlist[$technology->id]->endTime) && $techlist[$technology->id]->endTime > time()) {
                        $technologyRepository->updateBuildStatus($cu->getId(), 0, $technology->id, 0, 0, 0);

                        $buildingId = $technology->id === GEN_TECH_ID ? BuildingId::PEOPLE : BuildingId::TECHNOLOGY;
                        $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, $buildingId, false);

                        //Rohstoffe zurückgeben und aktualisieren
                        $fac = ($end_time - time()) / ($end_time - $start_time);
                        $reimbursableResources = PreciseResources::createFromCosts($costs->clone()->multiply($fac));
                        $planetRepo->addPreciseResources($planet->id, $reimbursableResources);

                        $b_status = 0;
                        $builing_something = false;

                        //Log schreiben
                        $log_text = "[b]Forschung Abbruch[/b]

                        [b]Start der Forschung:[/b] " . date("d.m.Y H:i:s", $start_time) . "
                        [b]Ende der Forschung:[/b] " . date("d.m.Y H:i:s", $end_time) . "

                        [b]Erhaltene Rohstoffe[/b]
                        [b]Faktor:[/b] " . $fac . "
                        [b]" . RES_METAL . ":[/b] " . StringUtils::formatNumber($reimbursableResources->metal) . "
                        [b]" . RES_CRYSTAL . ":[/b] " . StringUtils::formatNumber($reimbursableResources->crystal) . "
                        [b]" . RES_PLASTIC . ":[/b] " . StringUtils::formatNumber($reimbursableResources->plastic) . "
                        [b]" . RES_FUEL . ":[/b] " . StringUtils::formatNumber($reimbursableResources->fuel) . "
                        [b]" . RES_FOOD . ":[/b] " . StringUtils::formatNumber($reimbursableResources->food) . "

                        [b]Rohstoffe auf dem Planeten[/b]
                        [b]" . RES_METAL . ":[/b] " . StringUtils::formatNumber($planet->resMetal + $reimbursableResources->metal) . "
                        [b]" . RES_CRYSTAL . ":[/b] " . StringUtils::formatNumber($planet->resCrystal + $reimbursableResources->crystal) . "
                        [b]" . RES_PLASTIC . ":[/b] " . StringUtils::formatNumber($planet->resPlastic + $reimbursableResources->plastic) . "
                        [b]" . RES_FUEL . ":[/b] " . StringUtils::formatNumber($planet->resFuel + $reimbursableResources->fuel) . "
                        [b]" . RES_FOOD . ":[/b] " . StringUtils::formatNumber($planet->resFood + $reimbursableResources->food);

                        //Log Speichern
                        $gameLogRepository->add(GameLogFacility::TECH, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $planet->id, $technology->id, $b_status, $currentLevel);

                        header("Refresh:0; url=?page=research&id=" . $bid);
                    } else {
                        echo "<i>Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!</i><br/><br/>";
                    }
                }

                if ($b_status == 3) {
                    $color = "color:#0f0;";
                    $status_text = "Wird erforscht";
                } else {
                    $color = "";
                    $status_text = "Untätig";
                }

                //
                // Forschungsdaten anzeigen
                //
                tableStart(BBCodeUtils::toHTML($technology->name . " " . $currentLevel));
                echo "<tr><td width=\"220\" rowspan=\"3\" style=\"background:#000;;vertical-align:middle;\">
                " . helpImageLink("research&amp;id=" . $technology->id, IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $technology->id . "." . IMAGE_EXT, $technology->name, "width:220px;height:220px") . "
                </td>";
                echo "<td valign=\"top\" colspan=\"2\">" . BBCodeUtils::toHTML($technology->shortComment) . "</td></tr>";
                echo "<tr><th height=\"20\" width=\"50%\">Status:</th>";
                echo "<td id=\"buildstatus\" width=\"50%\" style=\"" . $color . "\">$status_text</td></tr>";
                echo "<tr><th height=\"20\" width=\"50%\">Stufe:</th>";

                if ($currentLevel > 0) {
                    echo "<td id=\"buildlevel\" width=\"50%\">" . $currentLevel . "</td></tr>";
                } else {
                    echo "<td id=\"buildlevel\" width=\"50%\">Noch nicht erforscht</td></tr>";
                }
                tableEnd();


                // Check requirements for this building
                $requirements_passed = true;
                foreach ($requirements->getBuildingRequirements($technology->id) as $requirement) {
                    if (!isset($buildlist[$requirement->requiredBuildingId]) || $buildlist[$requirement->requiredBuildingId] < $requirement->requiredLevel) {
                        $requirements_passed = false;
                    }
                }

                foreach ($requirements->getTechnologyRequirements($technology->id) as $requirement) {
                    if (!isset($techlist[$requirement->requiredTechnologyId]) || $techlist[$requirement->requiredTechnologyId]->currentLevel < $requirement->requiredLevel) {
                        $requirements_passed = false;
                    }
                }

                //
                // Baumenü
                //
                echo "<form action=\"?page=$page\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"id\" value=\"" . $technology->id . "\">";
                echo $checker;


                if ($requirements_passed) {
                    tableStart("Forschoptionen");
                    echo "<tr>
                        <th width=\"16%\">Aktion</th>
                        <th width=\"14%\">Zeit</th>
                        <th width=\"14%\">" . RES_METAL . "</th>
                        <th width=\"14%\">" . RES_CRYSTAL . "</th>
                        <th width=\"14%\">" . RES_PLASTIC . "</th>
                        <th width=\"14%\">" . RES_FUEL . "</th>
                        <th width=\"14%\">" . RES_FOOD . "</th></tr>";

                    $notAvStyle = " style=\"color:red;\"";

                    // Bauen
                    if ($b_status == 0) {
                        // Wartezeiten auf Ressourcen berechnen
                        $waitForMetal = $planet->prodMetal > 0 ? ceil(($costs->metal - $planet->resMetal) / $planet->prodMetal * 3600) : 0;
                        $waitForCrystal = $planet->prodCrystal > 0 ? ceil(($costs->crystal - $planet->resCrystal) / $planet->prodCrystal * 3600) : 0;
                        $waitForPlastic = $planet->prodPlastic > 0 ? ceil(($costs->plastic - $planet->resPlastic) / $planet->prodPlastic * 3600) : 0;
                        $waitForFuel = $planet->prodFuel > 0 ? ceil(($costs->fuel - $planet->resFuel) / $planet->prodFuel * 3600) : 0;
                        $waitForFood = $planet->prodFood > 0 ? ceil(($costs->food - $planet->resFood) / $planet->prodFood * 3600) : 0;
                        $waitTime = max($waitForMetal, $waitForMetal, $waitForPlastic, $waitForFuel, $waitForFood);

                        // Baukosten-String
                        $buildCostsString = "<td";
                        if ($costs->metal > $planet->resMetal) {
                            $buildCostsString .= $notAvStyle . " " . tm("Fehlender Rohstoff", "<b>" . StringUtils::formatNumber($costs->metal - $planet->resMetal) . "</b> " . RES_METAL . "<br/>
                                Bereit in <b>" . StringUtils::formatTimespan($waitForMetal) . "</b>");
                        }
                        $buildCostsString .= ">" . StringUtils::formatNumber($costs->metal) . "</td><td";
                        if ($costs->crystal > $planet->resCrystal) {
                            $buildCostsString .= $notAvStyle . " " . tm("Fehlender Rohstoff", StringUtils::formatNumber($costs->crystal - $planet->resCrystal) . " " . RES_CRYSTAL . "<br/>
                                Bereit in <b>" . StringUtils::formatTimespan($waitForMetal) . "</b>");
                        }
                        $buildCostsString .= ">" . StringUtils::formatNumber($costs->crystal) . "</td><td";
                        if ($costs->plastic > $planet->resPlastic) {
                            $buildCostsString .= $notAvStyle . " " . tm("Fehlender Rohstoff", StringUtils::formatNumber($costs->plastic - $planet->resPlastic) . " " . RES_PLASTIC . "<br/>
                                Bereit in <b>" . StringUtils::formatTimespan($waitForPlastic) . "</b>");
                        }
                        $buildCostsString .= ">" . StringUtils::formatNumber($costs->crystal) . "</td><td";
                        if ($costs->fuel > $planet->resFuel) {
                            $buildCostsString .= $notAvStyle . " " . tm("Fehlender Rohstoff", StringUtils::formatNumber($costs->fuel - $planet->resFuel) . " " . RES_FUEL . "<br/>
                                Bereit in <b>" . StringUtils::formatTimespan($waitForFuel) . "</b>");
                        }
                        $buildCostsString .= ">" . StringUtils::formatNumber($costs->fuel) . "</td><td";
                        if ($costs->food > $planet->resFood) {
                            $buildCostsString .= $notAvStyle . " " . tm("Fehlender Rohstoff", StringUtils::formatNumber($costs->food - $planet->resFood) . " " . RES_FOOD . "<br/>
                                Bereit in <b>" . StringUtils::formatTimespan($waitForFood) . "</b>");
                        }
                        $buildCostsString .= ">" . StringUtils::formatNumber($costs->food) . "</td></tr>";

                        // Maximale Stufe erreicht
                        if ($currentLevel >= $technology->lastLevel) {
                            echo "<tr><td colspan=\"7\"><i>Keine Weiterentwicklung möglich.</i></td></tr>";
                        }

                        // Es wird bereits geforscht
                        elseif ($building_something) {
                            //Sonderfeld Gentech
                            if ($technology->id == GEN_TECH_ID) {
                                if (!$building_gen) {
                                    echo "<tr><td><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td><td>" . StringUtils::formatTimespan($timeRequired) . "</td>";
                                    echo "<td>" . StringUtils::formatNumber($costs->metal) . "</td>
                                        <td>" . StringUtils::formatNumber($costs->crystal) . "</td>
                                        <td>" . StringUtils::formatNumber($costs->crystal) . "</td>
                                        <td>" . StringUtils::formatNumber($costs->fuel) . "</td>
                                        <td>" . StringUtils::formatNumber($costs->food) . "</td>
                                    </tr>";
                                } else {
                                    echo "<tr><td style=\"color:red;\">Erforschen</td><td>" . StringUtils::formatTimespan($timeRequired) . "</td>";
                                    echo $buildCostsString;
                                    echo "<tr><td colspan=\"7\"><i>Es kann nichts erforscht werden da gerade an einer anderen Technik geforscht wird!</i></td></tr>";
                                }
                            } else {
                                echo "<tr><td style=\"color:red;\">Erforschen</td><td>" . StringUtils::formatTimespan($timeRequired) . "</td>";
                                echo $buildCostsString;
                                echo "<tr><td colspan=\"7\"><i>Es kann nichts erforscht werden da gerade an einer anderen Technik geforscht wird!</i></td></tr>";
                            }
                        }
                        // Zuwenig Rohstoffe vorhanden
                        elseif (!$costs->isCoveredOnPlanet($planet)) {
                            echo "<tr><td style=\"color:red;\">Erforschen</td><td>" . StringUtils::formatTimespan($timeRequired) . "</td>";
                            echo $buildCostsString;
                            echo "<tr><td colspan=\"7\"><i>Keine Weiterentwicklung möglich, zuwenig Rohstoffe!</i></td></tr>";
                        }
                        // Forschen
                        elseif ($currentLevel == 0) {
                            echo "<tr>
                                <td><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td>
                                <td>" . StringUtils::formatTimespan($timeRequired) . "</td>
                                <td>" . StringUtils::formatNumber($costs->metal) . "</td>
                                <td>" . StringUtils::formatNumber($costs->crystal) . "</td>
                                <td>" . StringUtils::formatNumber($costs->plastic) . "</td>
                                <td>" . StringUtils::formatNumber($costs->fuel) . "</td>
                                <td>" . StringUtils::formatNumber($costs->food) . "</td>
                            </tr>";
                        }
                        // Ausbauen
                        else {
                            echo ($building_something);
                            echo "<tr>
                                <td><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td>
                                <td>" . StringUtils::formatTimespan($timeRequired) . "</td>
                                <td>" . StringUtils::formatNumber($costs->metal) . "</td>
                                <td>" . StringUtils::formatNumber($costs->crystal) . "</td>
                                <td>" . StringUtils::formatNumber($costs->plastic) . "</td>
                                <td>" . StringUtils::formatNumber($costs->fuel) . "</td>
                                <td>" . StringUtils::formatNumber($costs->food) . "</td>
                            </tr>";
                        }
                    }

                    // Bau abbrechen
                    if ($b_status == 3) {
                        if ($planet_id == $planet->id) {
                            echo "<tr><td><input type=\"submit\" class=\"button\" id=\"buildcancel\" name=\"command_cbuild\" value=\"Abbrechen\"  onclick=\"if (this.value=='Abbrechen'){return confirm('Wirklich abbrechen?');}\" /></td>";
                            echo '<td id="buildtime" style="vertical-align:middle;">-</td>
                        <td colspan="5"  id="progressbar" style="text-align:center;vertical-align:middle;font-weight:bold;"></td></tr>';
                            if ($currentLevel < $technology->lastLevel - 1) {
                                $costsNext = $technologyService->calculateCosts($technology, $currentLevel + 1, $user);
                                $timeNext = $technologyService->calculateBuildTime($costsNext, $user, $planet);
                                echo "<tr>
                                    <td width=\"90\">Nächste Stufe:</td>
                                    <td>" . StringUtils::formatTimespan($timeNext) . "</td>
                                    <td>" . StringUtils::formatNumber($costsNext->metal) . "</td>
                                    <td>" . StringUtils::formatNumber($costsNext->crystal) . "</td>
                                    <td>" . StringUtils::formatNumber($costsNext->plastic) . "</td>
                                    <td>" . StringUtils::formatNumber($costsNext->fuel) . "</td>
                                    <td>" . StringUtils::formatNumber($costsNext->food) . "</td>
                                </tr>";
                            }
                            countDown("buildtime", $end_time, "buildcancel");
                            jsProgressBar("progressbar", $start_time, $end_time);
                        } else {
                            echo "<tr><td colspan=\"7\">Technologie wird auf einem anderen Planeten bereits erforscht!</td></tr>";
                        }
                    }

                    tableEnd();

                    if (isset($waitTime) && $waitTime > 0) {
                        echo "Wartezeit bis genügend Rohstoffe zum Forschen vorhanden sind: <b>" . StringUtils::formatTimespan($waitTime) . "</b><br/><br/>";
                    }
                } else {
                    echo "<a href=\"?page=techtree\">Voraussetzungen</a> noch nicht erfüllt!<br/><br/>";
                }

                echo "<input type=\"submit\" name=\"command_show\" value=\"Aktualisieren\" /> &nbsp; ";
                echo "<input type=\"button\" value=\"Zurück zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
                echo "</form>";
            } else {
                error_msg("Technik nich vorhanden!");
                return_btn();
            }
        }

        //
        // Übersicht anzeigen
        //
        else {
            // Load categories
            $technologyTypes = $technologyTypeRepository->getTypes();
            if (count($technologyTypes) > 0) {
                // Load technologies
                $technologies = $technologyDataRepository->getTechnologies();
                /** @var array<int, array<Technology>> $groupedTechnologies */
                $groupedTechnologies = [];
                foreach ($technologies as $tech) {
                    $groupedTechnologies[$tech->typeId][] = $tech;
                }

                $technologyNames = $technologyDataRepository->getTechnologyNames(true);

                $cstr = $checker;
                echo "<form action=\"?page=$page\" method=\"post\"><div>";
                echo $cstr;
                foreach ($technologyTypes as $technologyType) {
                    tableStart($technologyType->name, TABLE_WIDTH);
                    $cnt = 0; // Counter for current row
                    $scnt = 0; // Counter for shown techs

                    if (isset($groupedTechnologies[$technologyType->id])) {
                        // Run through all techs in this cat
                        foreach ($groupedTechnologies[$technologyType->id] as $tech) {

                            // Aktuellen Level feststellen wenn Tech vorhanden
                            if (isset($techlist[$tech->id])) {
                                $currentLevel = $techlist[$tech->id]->currentLevel;
                                $start_time = $techlist[$tech->id]->startTime;
                                $end_time = $techlist[$tech->id]->endTime;
                            } else {
                                $currentLevel = 0;
                                $end_time = 0;
                            }

                            // Check requirements for this tech
                            $requirements_passed = true;
                            $b_req_info = array();
                            $t_req_info = array();
                            if (isset($b_req[$tech->id]['t']) && count($b_req[$tech->id]['t']) > 0) {
                                foreach ($b_req[$tech->id]['t'] as $b => $l) {
                                    if (!isset($techlist[$b]->currentLevel) || $techlist[$b]->currentLevel < $l) {
                                        $t_req_info[] = array($b, $l, false);
                                        $requirements_passed = false;
                                    } else
                                        $t_req_info[] = array($b, $l, true);
                                }
                            }
                            if (isset($b_req[$tech->id]['b']) && count($b_req[$tech->id]['b']) > 0) {
                                foreach ($b_req[$tech->id]['b'] as $id => $level) {
                                    if (!isset($buildlist[$id]) || $buildlist[$id] < $level) {
                                        $requirements_passed = false;
                                        $b_req_info[] = array($id, $level, false);
                                    } else {
                                        $b_req_info[] = array($id, $level, true);
                                    }
                                }
                            }

                            $filterStyleClass = "";
                            if (!$tech->show && $currentLevel > 0) {
                                $subtitle =  'Kann nicht erforscht werden';
                                $tmtext = '<span style="color:#999">Es ist nicht vorgesehen dass diese Technologie erforscht werden kann!</span><br/>';
                                $color = '#999';
                                if ($use_img_filter) {
                                    $filterStyleClass = "filter-unavailable";
                                }
                                $img = "" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $tech->id . "." . IMAGE_EXT . "";
                            } elseif ($tech->show) {
                                // Voraussetzungen nicht erfüllt
                                if (!$requirements_passed) {
                                    $subtitle =  'Voraussetzungen fehlen';
                                    $tmtext = '<span style="color:#999">Baue zuerst die nötigen Gebäude und erforsche die nötigen Technologien um diese Technologie zu erforschen!</span><br/>';

                                    $buildingNames = $buildingDataRepository->getBuildingNames(true);
                                    foreach ($b_req_info as $v) {
                                        $tmtext .= "<div style=\"color:" . ($v[2] ? '#0f0' : '#f30') . "\">" . $buildingNames[$v[0]] . " Stufe " . $v[1] . "</div>";
                                    }

                                    foreach ($t_req_info as $v) {
                                        $tmtext .= "<div style=\"color:" . ($v[2] ? '#0f0' : '#f30') . "\">" . $technologyNames[$v[0]] . " Stufe " . $v[1] . "</div>";
                                    }

                                    $color = '#999';
                                    if ($use_img_filter) {
                                        $filterStyleClass = "filter-unavailable";
                                    }
                                    $img = "" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $tech->id . "." . IMAGE_EXT . "";
                                }
                                // Ist im Bau
                                elseif (isset($techlist[$tech->id]) && $techlist[$tech->id]->buildType === 3) {
                                    $subtitle =  "Forschung auf Stufe " . ($currentLevel + 1);
                                    $tmtext = "<span style=\"color:#0f0\">Wird erforscht!<br/>Dauer: " . StringUtils::formatTimespan($end_time - time()) . "</span><br/>";
                                    $color = '#0f0';
                                    if ($use_img_filter) {
                                        $filterStyleClass = "filter-building";
                                    }
                                    $img = "" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $tech->id . "." . IMAGE_EXT . "";
                                }
                                // Untätig
                                else {
                                    $costs = $technologyService->calculateCosts($technology, $currentLevel, $user);

                                    // Zuwenig Ressourcen
                                    if ($currentLevel < $tech->lastLevel && !$costs->isCoveredOnPlanet($planet)) {
                                        $tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen für<br/>weitere Forschungen!</span><br/>";
                                        $color = '#f00';
                                        if ($use_img_filter) {
                                            $filterStyleClass = "filter-noresources";
                                        }
                                        $img = "" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $tech->id . "." . IMAGE_EXT . "";
                                    } else {
                                        $tmtext = "";
                                        $color = '#fff';
                                        $img = "" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $tech->id . "." . IMAGE_EXT . "";
                                    }

                                    if ($currentLevel == 0) {
                                        $subtitle = "Noch nicht erforscht";
                                    } elseif ($currentLevel >= $tech->lastLevel) {
                                        $subtitle = 'Vollständig erforscht';
                                    } else {
                                        $subtitle = 'Stufe ' . $currentLevel . '';
                                    }
                                }
                            }

                            // Display all buildings that are buildable or are already built
                            if ($tech->show || $currentLevel > 0) {
                                $img = "" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $tech->id . "_middle." . IMAGE_EXT . "";

                                if (!$requirements_passed) {
                                    $filterStyleClass = "filter-unavailable";
                                }

                                // Display row starter if needed
                                if ($cnt == 0) {
                                    echo "<tr>";
                                }

                                echo "<td style=\"width:" . CELL_WIDTH . "px;height:" . CELL_WIDTH . "px ;padding:0px;\">
                                        <div style=\"position:relative;height:" . CELL_WIDTH . "px;overflow:hidden\">
                                        <div class=\"buildOverviewObjectTitle\">" . $tech->name . "</div>";
                                echo "<a href=\"?page=$page&amp;id=" . $tech->id . "\" " . tm($tech->name, "<b>" . $subtitle . "</b><br/>" . $tmtext . $tech->shortComment) . " style=\"display:block;height:180px;\"><img class=\"" . $filterStyleClass . "\" src=\"" . $img . "\"/></a>";
                                if ($currentLevel > 0 || ($currentLevel == 0 && isset($techlist[$tech->id]) && $techlist[$tech->id]->buildType === 3)) {
                                    echo "<div class=\"buildOverviewObjectLevel\" style=\"color:" . $color . "\">" . $currentLevel . "</div>";
                                }
                                echo "</div></td>\n";

                                $cnt++;
                                $scnt++;
                            }

                            // Display row finisher if needed
                            if ($cnt == NUM_BUILDINGS_PER_ROW) {
                                echo "</tr>";
                                $cnt = 0;
                            }
                        }

                        // Fill up missing cols and end row
                        if ($cnt < NUM_BUILDINGS_PER_ROW && $cnt > 0) {
                            for ($x = 0; $x < NUM_BUILDINGS_PER_ROW - $cnt; $x++) {
                                echo "<td class=\"buildOverviewObjectNone\" style=\"width:" . CELL_WIDTH . "px;padding:0px;\">&nbsp;</td>";
                            }
                            echo '</tr>';
                        }

                        // Display message if no tech can be researched
                        if ($scnt == 0) {
                            echo "<tr>
                                            <td class=\"tbldata\" colspan=\"" . NUM_BUILDINGS_PER_ROW . "\" style=\"text-align:center;border:0;width:100%\">
                                                <i>In dieser Kategorie kann momentan noch nichts geforscht werden!</i>
                                            </td>
                                        </tr>";
                        }
                    } else {
                        echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center;border:0;width:100%\"><i>In dieser Kategorie kann momentan noch nichts erforscht werden!</i></td></tr>";
                    }
                    tableEnd();
                }
                echo '</div></form>';
            } else {
                echo "<i>Es können noch keine Forschungen erforscht werden!</i>";
            }
        }
    } else {
        echo "<h1>Forschungslabor des Planeten " . $planet->name . "</h1>";
        echo $resourceBoxDrawer->getHTML($planet);
        info_msg("Das Forschungslabor wurde noch nicht gebaut!");
    }
}
