<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Technology\Technology;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologyTypeRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Technology\TechnologyDataRepository;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];
/** @var TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[TechnologyDataRepository::class];

define('NUM_BUILDINGS_PER_ROW', 5);
define('CELL_WIDTH', 120);
define('TABLE_WIDTH', 'auto');

// Aktiviert / Deaktiviert Bildfilter
if ($cu->properties->imageFilter == 1) {
    $use_img_filter = true;
} else {
    $use_img_filter = false;
}

if (isset($cp)) {
    $planet = $planetRepo->find($cp->id);

    $bl = new BuildList($planet->id, $cu->id);

    $researchBuilding = $buildingRepository->getEntityBuilding($cu->getId(), $planet->id, TECH_BUILDING_ID);
    if ($researchBuilding !== null && $researchBuilding->currentLevel > 0) {
        $technologyRepository = $app[TechnologyRepository::class];
        define("GEN_TECH_LEVEL", $technologyRepository->getTechnologyLevel($cu->getId(), GEN_TECH_ID));
        $minBuildTimeFactor = (0.1 - (GEN_TECH_LEVEL / 100));

        // Überschrift
        echo "<h1>Forschungslabor (Stufe " . $researchBuilding->currentLevel . ") des Planeten " . $planet->name . "</h1>";
        echo $resourceBoxDrawer->getHTML($planet);

        // Forschungsliste laden && Gentech level definieren
        $tres = dbquery("
        SELECT
            *
        FROM
            techlist
        WHERE
            techlist_user_id='" . $cu->id . "';");
        $building_something = false;
        $building_gen = false;
        $techlist = [];
        while ($tarr = mysql_fetch_array($tres)) {
            $techlist[$tarr['techlist_tech_id']] = $tarr;
            // Check, ob schon eine Technik geforscht wird
            // BUGFIX: this is tech, NO check for same planet,
            // because only one tech at the same time per user

            if ($tarr['techlist_build_type'] > 2) {
                if ($tarr['techlist_tech_id'] == 23) {
                    $building_gen = true;
                } else {
                    $building_something = true;
                }
            }
        }

        $new_people_set = false;
        // people working changed
        if (isset($_POST['submit_people_form_gen'])) {

            $set_people = nf_back($_POST['peopleWorking']);
            if (!$building_gen && $bl->setPeopleWorking(PEOPLE_BUILDING_ID, $set_people, true)) {
                success_msg("Arbeiter zugeteilt!");
                $new_people_set = true;
            } else {
                error_msg('Arbeiter konnten nicht zugeteilt werden!');
            }
        }

        if (isset($_POST['submit_people_form'])) {
            $set_people = nf_back($_POST['peopleWorking']);
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
        // Läd alle benötgten Daten in Arrays
        //

        // Load built buildings
        $buildlist = $buildingRepository->getBuildingLevels($planet->id);

        // Load requirements
        $b_req = [];
        $rres = dbquery("
            SELECT
                *
            FROM
                tech_requirements;");
        while ($rarr = mysql_fetch_array($rres)) {
            if ($rarr['req_building_id'] > 0)
                $b_req[$rarr['obj_id']]['b'][$rarr['req_building_id']] = $rarr['req_level'];
            if ($rarr['req_tech_id'] > 0)
                $b_req[$rarr['obj_id']]['t'][$rarr['req_tech_id']] = $rarr['req_level'];
        }

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

        if ($bid == GEN_TECH_ID)
            $peopleFree = floor($planet->people) - $peopleWorking->total + ($peopleWorkingGen);
        else
            $peopleFree = floor($planet->people) - $peopleWorking->total + ($peopleWorkingResearch);

        $peopleOptimized = 0;
        $technology = null;
        if ($bid > 0) {
            // Forschungsdaten laden
            $technology = $technologyDataRepository->getTechnology($bid);
            if ($technology !== null) {
                $bc = array();
                $costs = array();
                $costs[0] = $technology->costsMetal;
                $costs[1] = $technology->costsCrystal;
                $costs[2] = $technology->costsPlastic;
                $costs[3] = $technology->costsFuel;
                $costs[4] = $technology->costsFood;
                $costs[5] = $technology->costsPower;
                $level = 0;
                if (isset($techlist[$technology->id])) {
                    $level = $techlist[$technology->id]['techlist_current_level'];
                }

                foreach ($resNames as $rk => $rn) {
                    //BUGFIX by river: costsResearch factor. Still whole code is wrong, but at least consistent now.
                    $bc['costs' . $rk] = $cu->specialist->costsResearch * $costs[$rk] * pow($technology->buildCostsFactor, $level);
                }
                $bc['costs5'] = $costs[5] * pow($technology->buildCostsFactor, $level);

                $bonus = $cu->race->researchTime + $cp->typeResearchtime + $cp->starResearchtime - 2;
                $bonus *= $cu->specialist->researchTime;

                $bc['time'] = (array_sum($bc)) / $config->getInt('global_time') * $config->getFloat('res_build_time') * $time_boni_factor;
                $bc['time'] *= $bonus;
                $maxReduction = $bc['time'] - $bc['time'] * $minBuildTimeFactor;

                $peopleOptimized = ceil($maxReduction / $config->getInt('people_work_done'));
            }
        }

        // create box to change people working
        $box =    '
                    <input type="hidden" name="workDone" id="workDone" value="' . $config->getInt('people_work_done') . '" />
                    <input type="hidden" name="foodRequired" id="foodRequired" value="' . $config->getInt('people_food_require') . '" />
                    <input type="hidden" name="peopleFree" id="peopleFree" value="' . $peopleFree . '" />
                    <input type="hidden" name="foodAvaiable" id="foodAvaiable" value="' . $planet->resFood . '" />';
        if ($cu->properties->itemShow == 'full' && $bid > 0) {
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
                                        value="' . nf($people) . '"
                                        onkeyup="updatePeopleWorkingBox(this.value,\'-1\',\'-1\');"/>
                        </td>
                    </tr>';
        $box .=        '<tr>
                            <th>Zeitreduktion</th>
                            <td><input	type="text"
                                        name="timeReduction"
                                        id="timeReduction"
                                        value="' . tf($config->getInt('people_work_done') * $people) . '"
                                        onkeyup="updatePeopleWorkingBox(\'-1\',this.value,\'-1\');" /></td>
                        </tr>
                            <th>Nahrungsverbrauch</th>
                            <td><input	type="text"
                                        name="foodUsing"
                                        id="foodUsing"
                                        value="' . nf($config->getInt('people_food_require') * $people) . '"
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
        if ($cu->specialist->costsResearch != 1) {
            echo "<tr><td>Kostenreduktion durch " . $cu->specialist->name . ":</td><td>" . get_percent_string($cu->specialist->costsResearch) . "</td></tr>";
        }
        if ($cu->specialist->researchTime != 1) {
            echo "<tr><td>Forschungszeitverringerung durch " . $cu->specialist->name . ":</td><td>" . get_percent_string($cu->specialist->researchTime) . "</td></tr>";
        }
        // Building level time bonus
        echo "<tr><td>Forschungszeitverringerung:</td><td>";
        if ($need_bonus_level >= 0) {
            echo get_percent_string($time_boni_factor) . " durch Stufe " . $researchBuilding->currentLevel . " (-" . ((1 - $config->param2Float('build_time_boni_forschungslabor')) * 100) . "% maximum)";
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
        echo "<tr><td>Eingestellte Arbeiter:</td><td>" . nf($peopleWorkingResearch);

        if (($building_something <> 1) && ($bid > 0) && ($bid <> 23)) {
            echo '&nbsp;<a id ="link" href="javascript:;" onclick="toggleBox(\'changePeople\');">[&Auml;ndern]</a>';
        }
        echo '</td><td>' . nf($peopleWorkingGen);

        if (($building_gen <> 1) && ($bid == 23)) {
            echo '&nbsp;<a id ="link" href="javascript:;" onclick="toggleBox(\'changePeople\');">[&Auml;ndern]</a>';
        }

        echo '</td></tr>';
        if (($peopleWorkingResearch > 0) || ($peopleWorkingGen > 0)) {
            echo "<tr><td>Zeitreduktion durch Arbeiter pro Auftrag:</td><td>" . tf($peopleTimeReduction * $peopleWorkingResearch) . "</td><td>" . tf($peopleTimeReduction * $peopleWorkingGen) . "</td></tr>";
            echo "<tr><td>Nahrungsverbrauch durch Arbeiter pro Auftrag:</td><td>" . nf($peopleFoodConsumption * $peopleWorkingResearch) . "</td><td>" . nf($peopleFoodConsumption * $peopleWorkingGen) . "</td></tr>";
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

                    $b_level = $techlist[$technology->id]['techlist_current_level'];
                    $b_status = $techlist[$technology->id]['techlist_build_type'];
                    $start_time = $techlist[$technology->id]['techlist_build_start_time'];
                    $end_time = $techlist[$technology->id]['techlist_build_end_time'];
                    $planet_id = $techlist[$technology->id]['techlist_entity_id'];
                }
                // Tech wurde noch nicht erforscht. Es werden Default Werte vergeben
                else {
                    $built = false;

                    $b_level = 0;
                    $b_status = 0;
                    $start_time = 0;
                    $end_time = 0;
                    $planet_id = 0;
                }


                $bc = calcTechCosts($technology, $b_level, $cu->specialist->costsResearch);
                $bcn = calcTechCosts($technology, $b_level + 1, $cu->specialist->costsResearch);

                // Bauzeit
                $bonus = $cu->race->researchTime + $cp->typeResearchtime + $cp->starResearchtime - 2;

                $btime = ($bc['metal'] + $bc['crystal'] + $bc['plastic'] + $bc['fuel'] + $bc['food']) / $config->getInt('global_time') * $config->getFloat('res_build_time') * $time_boni_factor;
                $btime *= $bonus * $cu->specialist->researchTime;

                //Nächste Stufe
                $btimen = ($bcn['metal'] + $bcn['crystal'] + $bcn['plastic'] + $bcn['fuel'] + $bcn['food']) / $config->getInt('global_time') * $config->getFloat('res_build_time') * $time_boni_factor;
                $btimen  *= $bonus * $cu->specialist->researchTime;


                // Berechnet mindest Bauzeit in beachtung von Gentechlevel
                $btime_min = $btime * $minBuildTimeFactor;
                if ($bid != GEN_TECH_ID) {
                    $btime = $btime - $peopleWorkingResearch * $peopleTimeReduction;
                    if ($btime < $btime_min) {
                        $btime = $btime_min;
                    }
                    $bc['food'] += $peopleWorkingResearch * $peopleFoodConsumption;
                } else {
                    $btime = $btime - $peopleWorkingGen * $peopleTimeReduction;
                    if ($btime < $btime_min) {
                        $btime = $btime_min;
                    }
                    $bc['food'] += $peopleWorkingGen * $peopleFoodConsumption;
                }

                //
                // Befehle ausführen
                //

                if (isset($_POST['command_build']) && $b_status == 0) {
                    if (!$building_something) {
                        if ($planet->resMetal >= $bc['metal'] && $planet->resCrystal >= $bc['crystal'] && $planet->resPlastic >= $bc['plastic']  && $planet->resFuel >= $bc['fuel']  && $planet->resFood >= $bc['food']) {
                            $start_time = time();
                            $end_time = time() + $btime;
                            if (isset($techlist[$technology->id])) {
                                dbquery("
                                UPDATE
                                    techlist
                                SET
                                    techlist_build_type='3',
                                    techlist_build_start_time='" . time() . "',
                                    techlist_build_end_time='" . $end_time . "',
                                    techlist_entity_id='" . $planet->id . "'
                                WHERE
                                    techlist_tech_id='" . $technology->id . "'
                                    AND techlist_user_id='" . $cu->id . "';");
                            } else {
                                dbquery("
                                INSERT INTO
                                techlist
                                (
                                    techlist_entity_id,
                                    techlist_build_type,
                                    techlist_build_start_time,
                                    techlist_build_end_time,
                                    techlist_tech_id,
                                    techlist_user_id
                                )
                                VALUES
                                (
                                    '" . $planet->id . "',
                                    '3',
                                    '" . time() . "',
                                    '" . $end_time . "',
                                    '" . $technology->id . "',
                                    '" . $cu->id . "'
                                );");
                            }

                            $buildingId = $technology->id === GEN_TECH_ID ? BuildingId::PEOPLE : BuildingId::TECHNOLOGY;
                            $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, $buildingId, true);

                            $planet_id = $planet->id;

                            //Rohstoffe vom Planeten abziehen und aktualisieren
                            $planetRepo->addResources($planet->id, -$bc['metal'], -$bc['crystal'], -$bc['plastic'], -$bc['fuel'], -$bc['food']);

                            $b_status = 3;

                            //Log schreiben
                            $log_text = "[b]Forschung Ausbau[/b]

                            [b]Erforschungsdauer:[/b] " . tf($btime) . "
                            [b]Ende:[/b] " . date("d.m.Y H:i:s", (int) $end_time) . "
                            [b]Forschungslabor Level:[/b] " . $researchBuilding->currentLevel . "
                            [b]Eingesetzte Bewohner:[/b] " . nf($peopleWorkingResearch) . "
                            [b]Gen-Tech Level:[/b] " . GEN_TECH_LEVEL . "
                            [b]Eingesetzter Spezialist:[/b] " . $cu->specialist->name . "

                            [b]Kosten[/b]
                            [b]" . RES_METAL . ":[/b] " . nf($bc['metal']) . "
                            [b]" . RES_CRYSTAL . ":[/b] " . nf($bc['crystal']) . "
                            [b]" . RES_PLASTIC . ":[/b] " . nf($bc['plastic']) . "
                            [b]" . RES_FUEL . ":[/b] " . nf($bc['fuel']) . "
                            [b]" . RES_FOOD . ":[/b] " . nf($bc['food']) . "

                            [b]Restliche Rohstoffe auf dem Planeten[/b]
                            [b]" . RES_METAL . ":[/b] " . nf($planet->resMetal - $bc['metal']) . "
                            [b]" . RES_CRYSTAL . ":[/b] " . nf($planet->resCrystal - $bc['crystal']) . "
                            [b]" . RES_PLASTIC . ":[/b] " . nf($planet->resPlastic - $bc['plastic']) . "
                            [b]" . RES_FUEL . ":[/b] " . nf($planet->resFuel - $bc['fuel']) . "
                            [b]" . RES_FOOD . ":[/b] " . nf($planet->resFood - $bc['food']);

                            GameLog::add(GameLog::F_TECH, GameLog::INFO, $log_text, $cu->id, $cu->allianceId, $planet->id, $technology->id, $b_status, $b_level);

                            echo '<script>toggleBox(\'link\'); </script>';
                        } else {
                            echo "<i>Forschung kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!</i><br/><br/>";
                        }
                    } else {
                        echo "<i>Forschung kann nicht gestartet werden, es wird bereits an einer Technologie geforscht!</i><br/><br/>";
                    }
                }


                if (isset($_POST['command_cbuild']) && $b_status == 3) {
                    if (isset($techlist[$technology->id]['techlist_build_end_time']) && $techlist[$technology->id]['techlist_build_end_time'] > time()) {
                        $fac = ($end_time - time()) / ($end_time - $start_time);
                        dbquery("
                        UPDATE
                            techlist
                        SET
                            techlist_build_type='0',
                            techlist_build_start_time='0',
                            techlist_build_end_time='0'
                        WHERE
                            techlist_tech_id='" . $technology->id . "'
                            AND techlist_user_id='" . $cu->id . "';");

                        $buildingId = $technology->id === GEN_TECH_ID ? BuildingId::PEOPLE : BuildingId::TECHNOLOGY;
                        $buildingRepository->markBuildingWorkingStatus($cu->getId(), $planet->id, $buildingId, false);

                        //Rohstoffe zurückgeben und aktualisieren
                        $planetRepo->addResources($planet->id, $bc['metal'] * $fac, $bc['crystal'] * $fac, $bc['plastic'] * $fac, $bc['fuel'] * $fac, $bc['food'] * $fac);

                        $b_status = 0;
                        $builing_something = false;

                        //Log schreiben
                        $log_text = "[b]Forschung Abbruch[/b]

                        [b]Start der Forschung:[/b] " . date("d.m.Y H:i:s", $start_time) . "
                        [b]Ende der Forschung:[/b] " . date("d.m.Y H:i:s", $end_time) . "

                        [b]Erhaltene Rohstoffe[/b]
                        [b]Faktor:[/b] " . $fac . "
                        [b]" . RES_METAL . ":[/b] " . nf($bc['metal'] * $fac) . "
                        [b]" . RES_CRYSTAL . ":[/b] " . nf($bc['crystal'] * $fac) . "
                        [b]" . RES_PLASTIC . ":[/b] " . nf($bc['plastic'] * $fac) . "
                        [b]" . RES_FUEL . ":[/b] " . nf($bc['fuel'] * $fac) . "
                        [b]" . RES_FOOD . ":[/b] " . nf($bc['food'] * $fac) . "

                        [b]Rohstoffe auf dem Planeten[/b]
                        [b]" . RES_METAL . ":[/b] " . nf($planet->resMetal + $bc['metal'] * $fac) . "
                        [b]" . RES_CRYSTAL . ":[/b] " . nf($planet->resCrystal + $bc['crystal'] * $fac) . "
                        [b]" . RES_PLASTIC . ":[/b] " . nf($planet->resPlastic + $bc['plastic'] * $fac) . "
                        [b]" . RES_FUEL . ":[/b] " . nf($planet->resFuel + $bc['fuel'] * $fac) . "
                        [b]" . RES_FOOD . ":[/b] " . nf($planet->resFood + $bc['food'] * $fac);

                        //Log Speichern
                        GameLog::add(GameLog::F_TECH, GameLog::INFO, $log_text, $cu->id, $cu->allianceId, $planet->id, $technology->id, $b_status, $b_level);

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
                    $status_text = "Unt&auml;tig";
                }

                //
                // Forschungsdaten anzeigen
                //
                tableStart(text2html($technology->name . " " . $b_level));
                echo "<tr><td width=\"220\" rowspan=\"3\" style=\"background:#000;;vertical-align:middle;\">
                " . helpImageLink("research&amp;id=" . $technology->id, IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $technology->id . "." . IMAGE_EXT, $technology->name, "width:220px;height:220px") . "
                </td>";
                echo "<td valign=\"top\" colspan=\"2\">" . text2html($technology->shortComment) . "</td></tr>";
                echo "<tr><th height=\"20\" width=\"50%\">Status:</th>";
                echo "<td id=\"buildstatus\" width=\"50%\" style=\"" . $color . "\">$status_text</td></tr>";
                echo "<tr><th height=\"20\" width=\"50%\">Stufe:</th>";

                if ($b_level > 0) {
                    echo "<td id=\"buildlevel\" width=\"50%\">" . $b_level . "</td></tr>";
                } else {
                    echo "<td id=\"buildlevel\" width=\"50%\">Noch nicht erforscht</td></tr>";
                }
                tableEnd();


                // Check requirements for this building
                $requirements_passed = true;
                $bid = $technology->id;
                if (isset($b_req[$bid]['b']) && count($b_req[$bid]['b']) > 0) {
                    foreach ($b_req[$bid]['b'] as $b => $l) {
                        if (!isset($buildlist[$b]) || $buildlist[$b] < $l) {
                            $requirements_passed = false;
                        }
                    }
                }
                if (isset($b_req[$bid]['t']) && count($b_req[$bid]['t']) > 0) {
                    foreach ($b_req[$bid]['t'] as $id => $level) {
                        if (!isset($techlist[$id]['techlist_current_level']) || $techlist[$id]['techlist_current_level'] < $level) {
                            $requirements_passed = false;
                        }
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
                    $bwait = [];
                    if ($b_status == 0) {
                        // Wartezeiten auf Ressourcen berechnen
                        if ($planet->prodMetal > 0) $bwait['metal'] = ceil(($bc['metal'] - $planet->resMetal) / $planet->prodMetal * 3600);
                        else $bwait['metal'] = 0;
                        if ($planet->prodCrystal > 0) $bwait['crystal'] = ceil(($bc['crystal'] - $planet->resCrystal) / $planet->prodCrystal * 3600);
                        else $bwait['crystal'] = 0;
                        if ($planet->prodPlastic > 0) $bwait['plastic'] = ceil(($bc['plastic'] - $planet->resPlastic) / $planet->prodPlastic * 3600);
                        else $bwait['plastic'] = 0;
                        if ($planet->prodFuel > 0) $bwait['fuel'] = ceil(($bc['fuel'] - $planet->resFuel) / $planet->prodFuel * 3600);
                        else $bwait['fuel'] = 0;
                        if ($planet->prodFood > 0) $bwait['food'] = ceil(($bc['food'] - $planet->resFood) / $planet->prodFood * 3600);
                        else $bwait['food'] = 0;
                        $bwmax = max($bwait['metal'], $bwait['crystal'], $bwait['plastic'], $bwait['fuel'], $bwait['food']);

                        // Baukosten-String
                        $bcstring = "<td";
                        if ($bc['metal'] > $planet->resMetal)
                            $bcstring .= $notAvStyle . " " . tm("Fehlender Rohstoff", "<b>" . nf($bc['metal'] - $planet->resMetal) . "</b> " . RES_METAL . "<br/>Bereit in <b>" . tf($bwait['metal']) . "</b>");
                        $bcstring .= ">" . nf($bc['metal']) . "</td><td";
                        if ($bc['crystal'] > $planet->resCrystal)
                            $bcstring .= $notAvStyle . " " . tm("Fehlender Rohstoff", nf($bc['crystal'] - $planet->resCrystal) . " " . RES_CRYSTAL . "<br/>Bereit in <b>" . tf($bwait['crystal']) . "</b>");
                        $bcstring .= ">" . nf($bc['crystal']) . "</td><td";
                        if ($bc['plastic'] > $planet->resPlastic)
                            $bcstring .= $notAvStyle . " " . tm("Fehlender Rohstoff", nf($bc['plastic'] - $planet->resPlastic) . " " . RES_PLASTIC . "<br/>Bereit in <b>" . tf($bwait['plastic']) . "</b>");
                        $bcstring .= ">" . nf($bc['plastic']) . "</td><td";
                        if ($bc['fuel'] > $planet->resFuel)
                            $bcstring .= $notAvStyle . " " . tm("Fehlender Rohstoff", nf($bc['fuel'] - $planet->resFuel) . " " . RES_FUEL . "<br/>Bereit in <b>" . tf($bwait['fuel']) . "</b>");
                        $bcstring .= ">" . nf($bc['fuel']) . "</td><td";
                        if ($bc['food'] > $planet->resFood)
                            $bcstring .= $notAvStyle . " " . tm("Fehlender Rohstoff", nf($bc['food'] - $planet->resFood) . " " . RES_FOOD . "<br/>Bereit in <b>" . tf($bwait['food']) . "</b>");
                        $bcstring .= ">" . nf($bc['food']) . "</td></tr>";
                        // Maximale Stufe erreicht
                        //$techlist[$bid]

                        if ($b_level >= $technology->lastLevel) {
                            echo "<tr><td colspan=\"7\"><i>Keine Weiterentwicklung m&ouml;glich.</i></td></tr>";
                        }
                        // Es wird bereits geforscht
                        elseif ($building_something) {
                            //Sonderfeld Gentech
                            if ($technology->id == GEN_TECH_ID) {
                                if (!$building_gen) {
                                    echo "<tr><td><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td><td>" . tf($btime) . "</td>";
                                    echo "<td>" . nf($bc['metal']) . "</td><td>" . nf($bc['crystal']) . "</td><td>" . nf($bc['plastic']) . "</td><td>" . nf($bc['fuel']) . "</td><td>" . nf($bc['food']) . "</td></tr>";
                                } else {
                                    echo "<tr><td style=\"color:red;\">Erforschen</td><td>" . tf($btime) . "</td>";
                                    echo $bcstring;
                                    echo "<tr><td colspan=\"7\"><i>Es kann nichts erforscht werden da gerade an einer anderen Technik geforscht wird!</i></td></tr>";
                                }
                            } else {
                                echo "<tr><td style=\"color:red;\">Erforschen</td><td>" . tf($btime) . "</td>";
                                echo $bcstring;
                                echo "<tr><td colspan=\"7\"><i>Es kann nichts erforscht werden da gerade an einer anderen Technik geforscht wird!</i></td></tr>";
                            }
                        }
                        // Zuwenig Rohstoffe vorhanden
                        elseif ($planet->resMetal < $bc['metal'] || $planet->resCrystal < $bc['crystal']  || $planet->resPlastic < $bc['plastic']  || $planet->resFuel < $bc['fuel']  || $planet->resFood < $bc['food']) {
                            echo "<tr><td style=\"color:red;\">Erforschen</td><td>" . tf($btime) . "</td>";
                            echo $bcstring;
                            echo "<tr><td colspan=\"7\"><i>Keine Weiterentwicklung m&ouml;glich, zuwenig Rohstoffe!</i></td></tr>";
                        }
                        // Forschen
                        elseif ($b_level == 0) {
                            echo "<tr><td><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td><td>" . tf($btime) . "</td>";
                            echo "<td>" . nf($bc['metal']) . "</td><td>" . nf($bc['crystal']) . "</td><td>" . nf($bc['plastic']) . "</td><td>" . nf($bc['fuel']) . "</td><td>" . nf($bc['food']) . "</td></tr>";
                        }
                        // Ausbauen
                        else {
                            echo ($building_something);
                            echo "<tr><td><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td><td>" . tf($btime) . "</td>";
                            echo "<td>" . nf($bc['metal']) . "</td><td>" . nf($bc['crystal']) . "</td><td>" . nf($bc['plastic']) . "</td><td>" . nf($bc['fuel']) . "</td><td>" . nf($bc['food']) . "</td></tr>";
                        }
                    }


                    // Bau abbrechen
                    if ($b_status == 3) {
                        if ($planet_id == $planet->id) {
                            echo "<tr><td><input type=\"submit\" class=\"button\" id=\"buildcancel\" name=\"command_cbuild\" value=\"Abbrechen\"  onclick=\"if (this.value=='Abbrechen'){return confirm('Wirklich abbrechen?');}\" /></td>";
                            echo '<td id="buildtime" style="vertical-align:middle;">-</td>
                        <td colspan="5"  id="progressbar" style="text-align:center;vertical-align:middle;font-weight:bold;"></td></tr>';
                            if ($b_level < $technology->lastLevel - 1)
                                echo "<tr><td width=\"90\">N&auml;chste Stufe:</td><td>" . tf($btimen) . "</td><td>" . nf($bcn['metal']) . "</td><td>" . nf($bcn['crystal']) . "</td><td>" . nf($bcn['plastic']) . "</td><td>" . nf($bcn['fuel']) . "</td><td>" . nf($bcn['food']) . "</td></tr>";
                            countDown("buildtime", $end_time, "buildcancel");
                            jsProgressBar("progressbar", $start_time, $end_time);
                        } else {
                            echo "<tr><td colspan=\"7\">Technologie wird auf einem anderen Planeten bereits erforscht!</td></tr>";
                        }
                    }


                    tableEnd();

                    if (isset($bwmax) && $bwmax > 0)
                        echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Forschen vorhanden sind: <b>" . tf($bwmax) . "</b><br/><br/>";



                    /*if ($b_status==3 || $b_status==4)
                    {
                        ?>
                            <script type="text/javascript">
                                function setCountdown()
                                {
                                    var ts;
                                    cTime = <?PHP echo time();?>;
                                    b_level = <?PHP echo $b_level;?>;
                                    te = <?PHP if($end_time) echo $end_time; else echo 0;?>;
                                    tc = cTime + cnt;
                                    window.status = tc;
                                    ts = te - tc;

                                    if(b_level>0)
                                    {
                                        b_level=b_level+1;
                                    }
                                    else
                                    {
                                        b_level=1;
                                    }

                                    if (ts>=0)
                                    {
                                        t = Math.floor(ts / 3600 / 24);
                                        h = Math.floor(ts / 3600);
                                        m = Math.floor((ts-(h*3600))/60);
                                        s = Math.floor((ts-(h*3600)-(m*60)));
                                        nv = h+"h "+m+"m "+s+"s";
                                    }
                                    else
                                    {
                                        nv = "-";
                                        document.getElementById('buildstatus').firstChild.nodeValue="Fertig";
                                        document.getElementById('buildlevel').firstChild.nodeValue=b_level;
                                        document.getElementById("buildcancel").name = "submit_info";
                                            document.getElementById("buildcancel").value = "Aktualisieren";
                                    }
                                    document.getElementById('buildtime').firstChild.nodeValue=nv;
                                    cnt = cnt + 1;
                                    setTimeout("setCountdown()",1000);
                                }
                                if (document.getElementById('buildtime')!=null)
                                {
                                    cnt = 0;
                                    setCountdown();
                                }
                            </script>
                        <?PHP
                    }*/
                } else {
                    echo "<a href=\"?page=techtree\">Voraussetzungen</a> noch nicht erfüllt!<br/><br/>";
                }

                echo "<input type=\"submit\" name=\"command_show\" value=\"Aktualisieren\" /> &nbsp; ";
                echo "<input type=\"button\" value=\"Zur&uuml;ck zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
                echo "</form>";
            } else {
                error_msg("Technik nich vorhanden!");
                return_btn();
            }
        }

        //
        // Übersicht anziegen
        //
        else {
            // Load categories
            /** @var TechnologyTypeRepository $technologyTypeRepository */
            $technologyTypeRepository = $app[TechnologyTypeRepository::class];
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
                                $b_level = intval($techlist[$tech->id]['techlist_current_level']);
                                $start_time = intval($techlist[$tech->id]['techlist_build_start_time']);
                                $end_time = intval($techlist[$tech->id]['techlist_build_end_time']);
                            } else {
                                $b_level = 0;
                                $end_time = 0;
                            }

                            // Check requirements for this tech
                            $requirements_passed = true;
                            $b_req_info = array();
                            $t_req_info = array();
                            if (isset($b_req[$tech->id]['t']) && count($b_req[$tech->id]['t']) > 0) {
                                foreach ($b_req[$tech->id]['t'] as $b => $l) {
                                    if (!isset($techlist[$b]['techlist_current_level']) || $techlist[$b]['techlist_current_level'] < $l) {
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
                            if (!$tech->show && $b_level > 0) {
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

                                    /** @var BuildingDataRepository $buildingRepository */
                                    $buildingRepository = $app[BuildingDataRepository::class];
                                    $buildingNames = $buildingRepository->getBuildingNames(true);
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
                                elseif (isset($techlist[$tech->id]['techlist_build_type']) && $techlist[$tech->id]['techlist_build_type'] == 3) {
                                    $subtitle =  "Forschung auf Stufe " . ($b_level + 1);
                                    $tmtext = "<span style=\"color:#0f0\">Wird erforscht!<br/>Dauer: " . tf($end_time - time()) . "</span><br/>";
                                    $color = '#0f0';
                                    if ($use_img_filter) {
                                        $filterStyleClass = "filter-building";
                                    }
                                    $img = "" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $tech->id . "." . IMAGE_EXT . "";
                                }
                                // Untätig
                                else {
                                    // Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
                                    $bc = calcTechCosts($tech, $b_level, $cu->specialist->costsResearch);

                                    // Zuwenig Ressourcen
                                    if ($b_level < $tech->lastLevel && ($planet->resMetal < $bc['metal'] || $planet->resCrystal < $bc['crystal']  || $planet->resPlastic < $bc['plastic']  || $planet->resFuel < $bc['fuel']  || $planet->resFood < $bc['food'])) {
                                        $tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen f&uuml;r<br/>weitere Forschungen!</span><br/>";
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

                                    if ($b_level == 0) {
                                        $subtitle = "Noch nicht erforscht";
                                    } elseif ($b_level >= $tech->lastLevel) {
                                        $subtitle = 'Vollständig erforscht';
                                    } else {
                                        $subtitle = 'Stufe ' . $b_level . '';
                                    }
                                }
                            }


                            // Display all buildings that are buildable or are already built
                            if ($tech->show || $b_level > 0) {
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
                                if ($b_level > 0 || ($b_level == 0 && isset($techlist[$tech->id]['techlist_build_type']) && $techlist[$tech->id]['techlist_build_type'] == 3)) {
                                    echo "<div class=\"buildOverviewObjectLevel\" style=\"color:" . $color . "\">" . $b_level . "</div>";
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
                echo "<i>Es k&ouml;nnen noch keine Forschungen erforscht werden!</i>";
            }
        }
    } else {
        echo "<h1>Forschungslabor des Planeten " . $planet->name . "</h1>";
        echo $resourceBoxDrawer->getHTML($planet);
        info_msg("Das Forschungslabor wurde noch nicht gebaut!");
    }
}
