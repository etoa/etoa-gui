<?PHP

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\StringUtils;
use EtoA\Specialist\SpecialistService;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologyRequirementRepository;
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
/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];
/** @var DefenseRepository $defenseRepository */
$defenseRepository = $app[DefenseRepository::class];
/** @var TechnologyRepository $technologyRepository */
$technologyRepository = $app[TechnologyRepository::class];
/** @var TechnologyRequirementRepository $technologyRequirementRepository */
$technologyRequirementRepository = $app[TechnologyRequirementRepository::class];

if ($cp) {
    $planet = $planetRepo->find($cp->id);

    echo '<h1>Bevölkerungsübersicht des Planeten ' . $planet->name . '</h1>';
    echo '<div id="population_info"></div>'; // Nur zu testzwecken
    echo $resourceBoxDrawer->getHTML($planet);

    $peopleStorageBuildings = $buildingRepository->getPeopleStorageBuildings($planet->id);
    if (count($peopleStorageBuildings) > 0) {
        //
        // Wohnfläche
        //
        tableStart("Wohnfl&auml;che", 400);
        echo '<tr>
        <th style="width:150px">Grundwohnfl&auml;che</th>
        <td>' . StringUtils::formatNumber($config->param1Int('user_start_people')) . '</td>
        </tr>';
        $pcnt = $config->param1Int('user_start_people');
        foreach ($peopleStorageBuildings as $storage) {
            $place = round($storage->peoplePlace * pow($storage->storeFactor, $storage->currentLevel - 1));
            echo '<tr><th>' . $storage->buildingName . '</th>
            <td>' . StringUtils::formatNumber($place) . '</td></tr>';
            $pcnt += $place;
        }
        echo '<tr><th>TOTAL</b></th><td><b>' . StringUtils::formatNumber($pcnt) . '</b></td></tr>';
        tableEnd();

        //überprüft tätigkeit
        $workingStatus = [
            SHIP_BUILDING_ID => $shipRepository->countBuildInProgress($cu->getId(), $planet->id),
            DEF_BUILDING_ID => $defenseRepository->countBuildInProgress($cu->getId(), $planet->id),
            TECH_BUILDING_ID => $technologyRepository->countResearchInProgress($cu->getId(), $planet->id),
            BUILD_BUILDING_ID => $buildingRepository->countBuildInProgress($cu->getId(), $planet->id),
            PEOPLE_BUILDING_ID =>  (int) $technologyRepository->isTechInProgress($cu->getId(), GEN_TECH_ID),
        ];

        //
        // Arbeiter zuteilen
        //
        if (isset($_POST['submit_people_work']) && checker_verify()) {
            //zählt gesperrte Arbeiter auf dem aktuellen Planet
            $peopleWorking = $buildingRepository->getPeopleWorking($planet->id, true);

            $working = 0;
            // Frei = total auf Planet - gesperrt auf Planet
            $free_people = floor($planet->people) - $peopleWorking->total;

            if (isset($_POST['people_work']) && gettype($_POST['people_work']) == 'array') {

                foreach ($_POST['people_work'] as $id => $num) {
                    if (!$workingStatus[$id]) {
                        $working += StringUtils::parseFormattedNumber($num);
                    }
                }

                $available = min($free_people, $working);

                foreach ($_POST['people_work'] as $buildingId => $num) {
                    if ($workingStatus[$buildingId] === 0) {
                        $num = StringUtils::parseFormattedNumber($num);
                        $work = $available > 0 ? min($num, $available) : 0;
                        $available -= $num;

                        $buildingRepository->setPeopleWorking($planet->id, $buildingId, (int) $work);
                    }
                }
            }
        }

        // Alle Arbeiter freistellen (solange sie nicht noch an einer Arbeit sind)
        if (isset($_POST['submit_people_free']) && checker_verify()) {
            foreach ($workingStatus as $buildingId => $v) {
                if ($v === 0) {
                    $buildingRepository->setPeopleWorking($planet->id, $buildingId, 0);
                }
            }
        }
        echo '<form action="?page=' . $page . '" method="post">';
        checker_init();
        tableStart("Arbeiter zuteilen");
        echo '<tr><th>Geb&auml;ude</th><th>Arbeiter</th><th>Zus&auml;tzliche Nahrung</th></tr>';

        // Gebäudede mit Arbeitsplätzen auswählen
        $workplaces = $buildingRepository->getWorkplaceBuildings($planet->id);
        $work_available = false;
        if (count($workplaces) > 0) {
            $work_available = true;
            foreach ($workplaces as $workplace) {
                if ($workplace->buildingId === PEOPLE_BUILDING_ID) {
                    $requirements_passed = true;
                    $requirements = $technologyRequirementRepository->getRequirements(GEN_TECH_ID);

                    /** @var TechnologyRepository $technologyRepository */
                    $technologyRepository = $app[TechnologyRepository::class];
                    $techlist = $technologyRepository->getTechnologyLevels($cu->getId());

                    foreach ($requirements->getAll(GEN_TECH_ID) as $requirement) {
                        if ($requirement->requiredTechnologyId > 0) {
                            if ($requirement->requiredLevel > ($techlist[$requirement->requiredTechnologyId] ?? 0)) {
                                $requirements_passed = false;
                            }
                        }
                        if ($requirement->requiredBuildingId > 0) {
                            if ($requirement->requiredLevel > ($buildingLevels[$requirement->requiredBuildingId] ?? 0)) {
                                $requirements_passed = false;
                            }
                        }
                    }

                    if (!$requirements_passed) {
                        continue;
                    }
                }

                echo '<tr><td style="width:150px">';
                switch ($workplace->buildingId) {
                    case BUILD_BUILDING_ID:
                        echo 'Bauhof';
                        break;
                    case PEOPLE_BUILDING_ID:
                        echo 'Genlabor';
                        break;
                    default:
                        echo $workplace->buildingName;
                }
                echo '</td><td>';

                if ($workingStatus[$workplace->buildingId] > 0) {
                    echo $workplace->peopleWorking;

                    //Sperrt arbeiter
                    $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, $workplace->buildingId, true);
                } else {

                    echo '<input type="text" id="' . $workplace->buildingId . '" name="people_work[' . $workplace->buildingId . ']" value="' . $workplace->peopleWorking . '" size="8" maxlength="20" onKeyUp="FormatNumber(this.id,this.value, ' . $planet->people . ', \'\', \'\');"/>';

                    //Entsperrt arbeiter
                    $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, $workplace->buildingId, false);
                }
                echo '</td><td>' . (StringUtils::formatNumber($workplace->peopleWorking * $config->getInt('people_food_require'))) . ' t</td></tr>';
            }
        }

        if ($work_available) {
            echo '<tr><td>&nbsp;</td>
            <td><input type="submit" name="submit_people_work" value="Speichern" /></td>
            <td><input type="submit" name="submit_people_free" value="Alle Arbeiter freigeben" /></td></tr>';
        }
        echo '<tr><td colspan="3">
        Wenn einem Geb&auml;ude Arbeiter zugeteilt werden, wird es entsprechend schneller gebaut. Die Arbeiter benötigen jedoch Nahrung. ';
        echo 'Die Zuteilung der Arbeiter kann erst ge&auml;ndert werden, wenn entsprechende Bauauftr&auml;ge abgeschlossen sind. ';
        echo 'Die gesamte Nahrung für die Arbeiter wird beim Start eines Bauvorgangs sofort vom Planetenkonto abgezogen.
        </td></tr>';
        tableEnd();
        echo '</form>';

        /** @var SpecialistService $specialistService */
        $specialistService = $app[SpecialistService::class];
        $specialist = $specialistService->getSpecialistOfUser($cu->id);

        // Zählt alle arbeiter die eingetragen snid (besetzt oder nicht) für die anszeige!
        $peopleWorking = $buildingRepository->getPeopleWorking($planet->id);

        // Infodaten
        $capacity = $planet->peoplePlace;
        if ($capacity < 200) {
            $capacity = 200;
        }
        $people_free = floor($planet->people) - $peopleWorking->total;
        $people_div = $planet->people * (($config->getFloat('people_multiply')  + $cp->typePopulation + $cu->race->population + $cp->starPopulation + ($specialist !== null ? $specialist->prodPeople : 1) - 4) * (1 - ($planet->people / ($capacity + 1))) / 24);


        tableStart("Daten", 500);
        echo '<tr><th style="width:300px">Bevölkerung total</th><td>' . StringUtils::formatNumber(floor($planet->people)) . '</td></tr>';
        echo '<tr><th>Arbeiter</th><td>' . StringUtils::formatNumber($peopleWorking->total) . '</td></tr>';
        echo '<tr><th>Freie Leute</th><td>' . StringUtils::formatNumber($people_free) . '</td></tr>';
        echo '<tr><th>Zeitreduktion pro Arbeiter und Auftrag</th><td>' . StringUtils::formatTimespan($config->getInt('people_work_done')) . '</td></tr>';
        echo '<tr><th>Nahrung pro Arbeiter und Auftrag</th><td>' . StringUtils::formatNumber($config->getInt('people_food_require')) . ' t</td></tr>';
        echo '<tr><th>Grundwachstumsrate</th><td>' . StringUtils::formatPercentString($config->getFloat('people_multiply')) . "</td></tr>";
        echo '<tr><th>Wachstumsbonus ' . $cp->typeName . '</th><td>' . StringUtils::formatPercentString($cp->typePopulation, true) . "</td></tr>";
        echo '<tr><th>Wachstumsbonus ' . $cu->race->name . '</th><td>' . StringUtils::formatPercentString($cu->race->population, true) . "</td></tr>";
        echo '<tr><th>Wachstumsbonus ' . $cp->starTypeName . '</th><td>' . StringUtils::formatPercentString($cp->starPopulation, true) . '</td></tr>';
        if ($specialist !== null) {
            echo '<tr><th>Wachstumsbonus ' . $specialist->name . '</th><td>' . StringUtils::formatPercentString($specialist->prodPeople, true) . '</td></tr>';
        }
        echo '<tr><th>Wachstumsbonus total</th><td>' . StringUtils::formatPercentString(array($cp->typePopulation, $cu->race->population, $cp->starPopulation, ($specialist !== null ? $specialist->prodPeople : 1)), true) . '</td></tr>';
        echo '<tr><th>Bevölkerungszuwachs pro Stunde</th><td>' . StringUtils::formatNumber($people_div) . '</td></tr>';
        tableEnd();
    } else
        error_msg("Es sind noch keine Geb&auml;ude gebaut, in denen deine Bevölkerung wohnen oder arbeiten kann!");
}
