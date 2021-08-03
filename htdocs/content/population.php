<?PHP

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Technology\TechnologyRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];
/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

if ($cp) {
    $planet = $planetRepo->find($cp->id);

    echo '<h1>Bevölkerungsübersicht des Planeten ' . $planet->name . '</h1>';
    echo '<div id="population_info"></div>'; // Nur zu testzwecken
    echo $resourceBoxDrawer->getHTML($planet);

    $res = dbquery("
    SELECT
        buildings.building_store_factor,
        buildings.building_name,
        buildings.building_people_place,
        buildlist.buildlist_current_level
    FROM
        buildlist
    INNER JOIN
        buildings
    ON
        buildlist.buildlist_building_id=buildings.building_id
    AND buildlist.buildlist_entity_id=" . $planet->id . "
    AND buildings.building_people_place>0
    AND buildlist.buildlist_current_level>0;");
    if (mysql_num_rows($res) > 0) {
        //
        // Wohnfläche
        //
        tableStart("Wohnfl&auml;che", 400);
        echo '<tr>
        <th style="width:150px">Grundwohnfl&auml;che</th>
        <td>' . nf($config->param1Int('user_start_people')) . '</td>
        </tr>';
        $pcnt = $config->param1Int('user_start_people');
        while ($arr = mysql_fetch_array($res)) {
            $place = round($arr['building_people_place'] * pow($arr['building_store_factor'], $arr['buildlist_current_level'] - 1));
            echo '<tr><th>' . $arr['building_name'] . '</th>
            <td>' . nf($place) . '</td></tr>';
            $pcnt += $place;
        }
        echo '<tr><th>TOTAL</b></th><td><b>' . nf($pcnt) . '</b></td></tr>';
        tableEnd();

        //überprüft tätigkeit des Schiffswerftes
        $sql = "
        SELECT
            COUNT(queue_id)
        FROM
            ship_queue
        WHERE
            queue_entity_id='" . $planet->id . "'
        AND queue_user_id='" . $cu->id . "'
        AND queue_starttime>'0'
        AND queue_endtime>'0';";
        $tres = dbquery($sql);
        $tarr = mysql_fetch_row($tres);
        $w = [];
        $w[SHIP_BUILDING_ID] = $tarr[0];

        //überprüft tätigkeit der waffenfabrik
        $sql = "
        SELECT
            COUNT(queue_id)
        FROM
            def_queue
        WHERE
            queue_entity_id='" . $planet->id . "'
        AND queue_user_id='" . $cu->id . "'
        AND queue_starttime>'0'
        AND queue_endtime>'0';";
        $tres = dbquery($sql);
        $tarr = mysql_fetch_row($tres);
        $w[DEF_BUILDING_ID] = $tarr[0];

        //überprüft tätigkeit des forschungslabors
        $sql = "
        SELECT
            COUNT(techlist_id)
        FROM
        techlist
        WHERE
            techlist_entity_id='" . $planet->id . "'
        AND techlist_user_id='" . $cu->id . "'
        AND techlist_build_type>'2'
        AND techlist_tech_id <>" . GEN_TECH_ID;
        $tres = dbquery($sql);
        $tarr = mysql_fetch_row($tres);
        $w[TECH_BUILDING_ID] = $tarr[0];

        //überprüft tätigkeit des bauhofes
        $sql = "
        SELECT
            COUNT(buildlist_id)
        FROM
            buildlist
        WHERE
            buildlist_entity_id='" . $planet->id . "'
        AND buildlist_user_id='" . $cu->id . "'
        AND buildlist_build_start_time>'0'
        AND buildlist_build_end_time>'0';";
        $tres = dbquery($sql);
        $tarr = mysql_fetch_row($tres);
        $w[BUILD_BUILDING_ID] = $tarr[0];

        $sql = "
            SELECT
                1
            FROM
                techlist
            WHERE
                techlist_tech_id=" . GEN_TECH_ID . "
            AND techlist_user_id=" . $cu->id . "
            AND techlist_build_type>2";

        $tres = mysql_query($sql);
        $tarr = mysql_fetch_row($tres);
        $w[PEOPLE_BUILDING_ID] = (int)isset($tarr[0]);

        //
        // Arbeiter zuteilen
        //
        if (isset($_POST['submit_people_work']) && checker_verify()) {
            //zählt gesperrte Arbeiter auf dem aktuellen Planet
            $check_res = dbquery("
            SELECT
                SUM(buildlist_people_working)
            FROM
                buildlist
            WHERE
                buildlist_entity_id=" . $planet->id . "
            AND buildlist_people_working_status='1';");

            $working = 0;
            $check_arr = mysql_fetch_array($check_res);
            // Frei = total auf Planet - gesperrt auf Planet
            $free_people = floor($planet->people) - $check_arr[0];

            if (isset($_POST['people_work']) && gettype($_POST['people_work']) == 'array') {

                foreach ($_POST['people_work'] as $id => $num) {
                    if (!$w[$id]) {
                        $working += nf_back($num);
                    }
                }

                $available = min($free_people, $working);

                foreach ($_POST['people_work'] as $id => $num) {
                    if (!$w[$id]) {
                        $num = nf_back($num);
                        $work = $available > 0 ? min($num, $available) : 0;
                        $available -= $num;

                        $buildingRepository->setPeopleWorking($planet->id, (int) $id, (int) $work);
                    }
                }
            }
        }

        // Alle Arbeiter freistellen (solange sie nicht noch an einer Arbeit sind)
        if (isset($_POST['submit_people_free']) && checker_verify()) {
            foreach ($w as $id => $v) {
                if ($v == 0) {
                    $buildingRepository->setPeopleWorking($planet->id, (int) $id, 0);
                }
            }
        }
        echo '<form action="?page=' . $page . '" method="post">';
        checker_init();
        tableStart("Arbeiter zuteilen");
        echo '<tr><th>Geb&auml;ude</th><th>Arbeiter</th><th>Zus&auml;tzliche Nahrung</th></tr>';

        // Gebäudede mit Arbeitsplätzen auswählen
        $sp_res = dbquery("
        SELECT
            buildlist.buildlist_people_working,
            buildings.building_name,
            buildings.building_people_place,
            buildings.building_id
        FROM
            buildlist,
            buildings
        WHERE
            buildlist.buildlist_building_id=buildings.building_id
        AND buildings.building_workplace='1'
        AND buildlist.buildlist_entity_id='" . $planet->id . "'
        ORDER BY
            buildings.building_id;");
        $work_available = false;
        if (mysql_num_rows($sp_res) > 0) {
            $work_available = true;
            while ($sp_arr = mysql_fetch_array($sp_res)) {
                if ($sp_arr['building_id'] == PEOPLE_BUILDING_ID) {
                    $requirements_passed = true;
                    $rres = dbquery("SELECT * FROM tech_requirements where obj_id=" . GEN_TECH_ID);
                    $bl = new BuildList($planet->id, $cu->id);

                    /** @var TechnologyRepository $technologyRepository */
                    $technologyRepository = $app[TechnologyRepository::class];
                    $techlist = $technologyRepository->getTechnologyLevels($cu->getId());

                    $buildingLevels = $buildingRepository->getBuildingLevels($planet->id);

                    while ($rarr = mysql_fetch_array($rres)) {
                        if ($rarr['req_tech_id'] > 0) {
                            if (($rarr['req_level']) > ($techlist[$rarr['req_tech_id']] ?? 0)) {
                                $requirements_passed = false;
                            }
                        }
                        if ($rarr['req_building_id'] > 0) {

                            if (($rarr['req_level']) > ($buildingLevels[$rarr['req_building_id']] ?? 0)) {
                                $requirements_passed = false;
                            }
                        }
                    }

                    if (!$requirements_passed) {
                        continue;
                    }
                }

                echo '<tr><td style="width:150px">';
                switch ($sp_arr['building_id']) {
                    case BUILD_BUILDING_ID:
                        echo 'Bauhof';
                        break;
                    case PEOPLE_BUILDING_ID:
                        echo 'Genlabor';
                        break;
                    default:
                        echo $sp_arr['building_name'];
                }
                echo '</td><td>';

                if ($w[$sp_arr['building_id']] > 0) {
                    echo $sp_arr['buildlist_people_working'];

                    //Sperrt arbeiter
                    $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, (int) $sp_arr['building_id'], true);
                } else {

                    echo '<input type="text" id="' . $sp_arr['building_id'] . '" name="people_work[' . $sp_arr['building_id'] . ']" value="' . $sp_arr['buildlist_people_working'] . '" size="8" maxlength="20" onKeyUp="FormatNumber(this.id,this.value, ' . $planet->people . ', \'\', \'\');"/>';

                    //Entsperrt arbeiter
                    $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, (int) $sp_arr['building_id'], false);
                }
                echo '</td><td>' . (nf($sp_arr['buildlist_people_working'] * $config->getInt('people_food_require'))) . ' t</td></tr>';
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


        // Zählt alle arbeiter die eingetragen snid (besetzt oder nicht) für die anszeige!
        $bres = dbquery("
        SELECT
            SUM(buildlist_people_working)
        FROM
            buildlist
        WHERE
            buildlist_entity_id=" . $planet->id . ";");
        $barr = mysql_fetch_array($bres);
        $people_working = $barr[0];

        // Infodaten
        $capacity = $planet->peoplePlace;
        if ($capacity < 200) {
            $capacity = 200;
        }
        $people_free = floor($planet->people) - $people_working;
        $people_div = $planet->people * (($config->getFloat('people_multiply')  + $cp->typePopulation + $cu->race->population + $cp->starPopulation + $cu->specialist->population - 4) * (1 - ($planet->people / ($capacity + 1))) / 24);


        tableStart("Daten", 500);
        echo '<tr><th style="width:300px">Bevölkerung total</th><td>' . nf(floor($planet->people)) . '</td></tr>';
        echo '<tr><th>Arbeiter</th><td>' . nf($people_working) . '</td></tr>';
        echo '<tr><th>Freie Leute</th><td>' . nf($people_free) . '</td></tr>';
        echo '<tr><th>Zeitreduktion pro Arbeiter und Auftrag</th><td>' . tf($config->getInt('people_work_done')) . '</td></tr>';
        echo '<tr><th>Nahrung pro Arbeiter und Auftrag</th><td>' . nf($config->getInt('people_food_require')) . ' t</td></tr>';
        echo '<tr><th>Grundwachstumsrate</th><td>' . get_percent_string($config->getFloat('people_multiply')) . "</td></tr>";
        echo '<tr><th>Wachstumsbonus ' . $cp->typeName . '</th><td>' . get_percent_string($cp->typePopulation, 1) . "</td></tr>";
        echo '<tr><th>Wachstumsbonus ' . $cu->race->name . '</th><td>' . get_percent_string($cu->race->population, 1) . "</td></tr>";
        echo '<tr><th>Wachstumsbonus ' . $cp->starTypeName . '</th><td>' . get_percent_string($cp->starPopulation, 1) . '</td></tr>';
        echo '<tr><th>Wachstumsbonus ' . $cu->specialist->name . '</th><td>' . get_percent_string($cu->specialist->population, 1) . '</td></tr>';
        echo '<tr><th>Wachstumsbonus total</th><td>' . get_percent_string(array($cp->typePopulation, $cu->race->population, $cp->starPopulation, $cu->specialist->population), 1) . '</td></tr>';
        echo '<tr><th>Bevölkerungszuwachs pro Stunde</th><td>' . nf($people_div) . '</td></tr>';
        tableEnd();
    } else
        error_msg("Es sind noch keine Geb&auml;ude gebaut, in denen deine Bevölkerung wohnen oder arbeiten kann!");
}
