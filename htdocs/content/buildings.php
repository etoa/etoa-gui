<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Building\BuildingTypeDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\Specialist\SpecialistService;
use EtoA\Technology\TechnologyRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\User\UserPropertiesRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

if ($properties->itemShow != 'full') {
    define('NUM_BUILDINGS_PER_ROW', 9);
    define('TABLE_WIDTH', '');
} else {
    define('NUM_BUILDINGS_PER_ROW', 5);
    define('CELL_WIDTH', 120);
    define('TABLE_WIDTH', 'auto');
}

define('HELP_URL', "?page=help&site=buildings");

// Aktiviert / Deaktiviert Bildfilter
if ($properties->imageFilter) {
    $use_img_filter = true;
} else {
    $use_img_filter = false;
}

if (isset($cp)) {
    $planet = $planetRepo->find($cp->id);

    echo "<h1>Bauhof des Planeten " . $planet->name . "</h1>";

    echo $resourceBoxDrawer->getHTML($planet);

    /** @var TechnologyRepository $technologyRepository */
    $technologyRepository = $app[TechnologyRepository::class];
    $techlist = $technologyRepository->getTechnologyLevels($cu->getId());

    $bl = new BuildList($planet->id, $cu->id, 2);
    $bid = 0;

    // create posted id for small view
    if (isset($_POST['command_build']) && is_array($_POST['command_build'])) {
        foreach ($_POST['command_build'] as $cbid => $bmsg) {
            foreach ($_POST['id'] as $id => $msg) {
                if ($id == $cbid) {
                    $_POST['id'] = $cbid;
                    break;
                }
            }
        }
    }
    if (isset($_POST['command_cbuild']) && is_array($_POST['command_cbuild'])) {
        foreach ($_POST['command_cbuild'] as $cbid => $bmsg) {
            foreach ($_POST['id'] as $id => $msg) {
                if ($id == $cbid) {
                    $_POST['id'] = $cbid;
                    break;
                }
            }
        }
    }

    //Gebäude ausbauen/abreissen/abbrechen
    if ((isset($_GET['id']) && intval($_GET['id']) > 0) || (count($_POST) > 0 && checker_verify())) {
        $bid = 0;
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

        $building = $buildingRepository->getEntityBuilding($cu->getId(), $planet->id, $bid);

        // people working changed
        if (isset($_POST['submit_people_form'])) {
            if ($bl->setPeopleWorking(BUILD_BUILDING_ID, StringUtils::parseFormattedNumber($_POST['peopleWorking'])))
                success_msg("Arbeiter zugeteilt!");
            else
                error_msg('Arbeiter konnten nicht zugeteilt werden!');
        }

        // build building
        if (isset($_POST['command_build']) && ($building === null || $building->buildType === 0)) {
            if ($bl->build($bid)) {
                success_msg('Bauauftrag wurde erfolgreich gestartet!');
            } else {
                error_msg($bl->getLastError());
            }
        }

        // demolish building
        elseif (isset($_POST['command_demolish']) && ($building !== null && $building->buildType === 0)) {
            if ($bl->demolish($bid)) {
                success_msg('Abbruchauftrag wurde erfolgreich gestartet!');
            } else {
                error_msg($bl->getLastError());
            }
        }

        // cancel build building
        elseif (isset($_POST['command_cbuild']) && $building !== null && $building->buildType === 3) {
            if ($bl->cancelBuild($bid)) {
                success_msg('Bauauftrag wurde erfolgreich abgebrochen!');
            } else {
                error_msg($bl->getLastError());
            }
        }

        // cancel demolish building
        elseif (isset($_POST['command_cdemolish']) && $building !== null && $building->buildType === 4) {
            if ($bl->cancelDemolish($bid)) {
                success_msg('Abbruchauftrag wurde erfolgreich abgebrochen!');
            } else {
                error_msg($bl->getLastError());
            }
        }

        // create design relateted stuff
        if ($building !== null && $building->buildType === 3 && $building->currentLevel > 0) {
            $color = "color:#0f0;";
            $status_text = "Wird ausgebaut";
        } elseif ($building !== null && $building->buildType === 3) {
            $color = "color:#0f0;";
            $status_text = "Wird gebaut";
        } elseif ($building !== null && $building->buildType === 4) {
            $color = "color:#f80;";
            $status_text = "Wird abgerissen";
        } else {
            $color = "";
            $status_text = "";
        }
    }

    // cache checker to add it to several forms
    ob_start();
    checker_init();
    $checker = ob_get_contents();
    ob_end_clean();

    $peopleWorking = $buildingRepository->getPeopleWorking($planet->id);
    $peopleFree = floor($planet->people) - $peopleWorking->total + $peopleWorking->building;
    // create box to change people working
    $box =    '
                <input type="hidden" name="workDone" id="workDone" value="' . $config->getInt('people_work_done') . '" />
                <input type="hidden" name="foodRequired" id="foodRequired" value="' . $config->getInt('people_food_require') . '" />
                <input type="hidden" name="peopleFree" id="peopleFree" value="' . $peopleFree . '" />
                <input type="hidden" name="foodAvailable" id="foodAvailable" value="' . $cp->getRes1(4) . '" />';
    if ($properties->itemShow == 'full' && $bid > 0 && $bl->item($bid) !== false) {
        $box .= '<input type="hidden" name="peopleOptimized" id="peopleOptimized" value="' . $bl->item($bid)->getPeopleOptimized() . '" />';
    } else {
        $box .= '<input type="hidden" name="peopleOptimized" id="peopleOptimized" value="0" />';
    }
    $box .= '	<tr>
                        <th>Eingestellte Arbeiter</th>
                        <td>
                            <input 	type="text"
                                    name="peopleWorking"
                                    id="peopleWorking"
                                    value="' . StringUtils::formatNumber($peopleWorking->building) . '"
                                    onkeyup="updatePeopleWorkingBox(this.value,\'-1\',\'-1\');"/>
                    </td>
                    </tr>
                    <tr>
                        <th>Zeitreduktion</th>
                        <td><input	type="text"
                                    name="timeReduction"
                                    id="timeReduction"
                                    value="' . StringUtils::formatTimespan($config->getInt('people_work_done') * $peopleWorking->building) . '"
                                    onkeyup="updatePeopleWorkingBox(\'-1\',this.value,\'-1\');" /></td>
                    </tr>
                        <th>Nahrungsverbrauch</th>
                        <td><input	type="text"
                                    name="foodUsing"
                                    id="foodUsing"
                                    value="' . StringUtils::formatNumber($config->getInt('people_food_require') * $peopleWorking->building) . '"
                                    onkeyup="updatePeopleWorkingBox(\'-1\',\'-1\',this.value);" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <div id="changeWorkingPeopleError" style="display:none;">&nbsp;</div>
                            <input type="submit" value="Speichern" name="submit_people_form" id="submit_people_form" />&nbsp;';

    if ($properties->itemShow == 'full' && $bid > 0 && $bl->item($bid) !== false) {
        $peopleOptimized = $bl->item($bid)->getPeopleOptimized();
        $box .= '<input type="button" value="Optimieren" onclick="updatePeopleWorkingBox(\'' . $peopleOptimized . '\',\'-1\',\'^-1\');">';
    }
    $box .= '
                </td>
            </tr>';

    //
    // create infobox incl. editable stuff for working people adjustements
    //
    $genTechLevel = $techlist[GEN_TECH_ID] ?? 0;
    tableStart('Bauhof-Infos');
    echo '<colgroup><col style="width:400px;"/><col/></colgroup>';

    // Specialist
    /** @var SpecialistService $specialistService */
    $specialistService = $app[SpecialistService::class];
    $specialist = $specialistService->getSpecialistOfUser($cu->id);
    if ($specialist !== null && $specialist->costsBuildings != 1) {
        echo '<tr><td>Kostenreduktion durch ' . $specialist->name . ':</td><td>' . StringUtils::formatPercentString($specialist->costsBuildings) . '</td></tr>';
    }
    if ($specialist !== null && $specialist->timeBuildings != 1) {
        echo '<tr><td>Bauzeitverringerung durch ' . $specialist->name . ':</td><td>' . StringUtils::formatPercentString($specialist->timeBuildings) . '</td></tr>';
    }

    // Worker
    echo '<tr><td>Eingestellte Arbeiter:</td><td><span id="people_working">' . StringUtils::formatNumber($peopleWorking->building) . '</span>';
    if (!$bl->isUnderConstruction()) {
        echo '&nbsp;<a href="javascript:;" onclick="toggleBox(\'changePeople\');">[&Auml;ndern]</a>';
    }
    echo '</td></tr>';
    if ($peopleWorking->building > 0) {
        echo '<tr><td>Zeitreduktion durch Arbeiter pro Auftrag:</td><td><span id="people_work_done">' . StringUtils::formatTimespan($config->getInt('people_work_done') * $peopleWorking->building) . '</span></td></tr>';
        echo '<tr><td>Nahrungsverbrauch durch Arbeiter pro Auftrag:</td><td><span id="people_food_require">' . StringUtils::formatNumber($config->getInt('people_food_require') * $peopleWorking->building) . '</span></td></tr>';
    }
    // Genetics technology level
    if ($genTechLevel > 0) {
        echo '<tr><td>Gentechnologie:</td><td>' . $genTechLevel . '</td></tr>';
        echo '<tr><td>Minimale Bauzeit (mit Arbeiter):</td><td>Bauzeit * ' . (0.1 - ($genTechLevel / 100)) . '</td></tr>';
    }
    // Boost system
    if ($config->getBoolean('boost_system_enable')) {
        echo '<tr><td>Geschwindigkeitsboost:</td><td>' . StringUtils::formatPercentString($cu->boostBonusBuilding + 1) . '</td></tr>';
    }
    tableEnd();

    echo '<div id="changePeople" style="display:none;">';
    tableStart("Arbeiter im Bauhof zuteilen");
    echo '<form id="changeWorkingPeople" action="?page=' . $page . '&amp;id=' . $bid . '" method="post">
        ' . $checker . $box . '</form>';
    tableEnd();
    echo '</div>';

    // if full view and detail view selected, show it
    if ($bid > 0 && $bl->item($bid) !== false && $properties->itemShow == 'full') {

        //
        // Gebäudedaten anzeigen
        //
        $item = $bl->item($bid);
        $caption = (string)$item;
        if (isset($status_text) && $status_text) {
            $caption .= ' <span style="' . $color . '">' . $status_text . '</span>';
        }
        tableStart($caption);
        echo '<tr>
                    <td rowspan="3" style="width:220px;background:#000;vertical-align:middle;">
                        ' . helpImageLink('buildings&amp;id=' . $item->buildingId, $item->building->imgPathBig(), $item->building, 'width:220px;height:220px') . '
                </td>
                <td colspan="2" style="vertical-align:top;height:150px;">
                    ' . $item->building->longDesc . '
                </td>
            </tr>';
        $f = $item->building->fields;
        echo '<tr>
                    <th style="width:250px;height:20px;">Platzverbrauch pro Ausbaustufe:</th>
                    <td>' . $f . ' ' . ($f != 1 ? 'Felder' : 'Feld') . '</td>
                </tr>';
        $f = $item->building->fields * $item->level;
        echo '<tr>
                <th style="width:250px;height:20px;">Platzverbrauch total:</th>
                <td>' . $f . ' ' . ($f != 1 ? 'Felder' : 'Feld') . '</td>
            </tr>';
        tableEnd();

        //
        // Baumenü
        //
        echo '<form action="?page=' . $page . '" method="post">';
        echo '<input type="hidden" name="id" value="' . $bid . '">';
        echo $checker;

        // Voraussetzungen sind erfüllt
        if ($bl->requirementsPassed($bid)) {
            $costs = $bl->getCosts($bid, 'build');
            $demolishCosts = $bl->getCosts($bid, 'demolish');
            tableStart('Bauoptionen');
            echo '<tr>
                    <th width="16%">Aktion</td>
                    <th width="14%">' . RES_ICON_TIME . ' Zeit</th>
                    <th width="14%">' . RES_ICON_METAL . '</td>
                    <th width="14%">' . RES_ICON_CRYSTAL . '</td>
                    <th width="14%">' . RES_ICON_PLASTIC . '</td>
                    <th width="14%">' . RES_ICON_FUEL . '</td>
                    <th width="14%">' . RES_ICON_FOOD . '</td>
                    <th width="14%">' . RES_ICON_POWER . '</td>
                </tr>';

            // Bauen
            if ($item->buildType == 0) {
                $waitArr = $item->waitingTimeString();
                if ($bl->checkBuildable($bid) <= 0) {
                    if ($bl->checkBuildable($bid) == 0) {
                        echo '<tr>
                                <td style="color:red;">Bauen</td>
                                <td>' . StringUtils::formatTimespan($costs['time']) . '</td>'
                            . $waitArr['string'] . '
                                <td>' . StringUtils::formatNumber(ceil($costs['costs5'])) . '</td>
                            </tr>';
                    }

                    echo '<tr>
                            <td colspan="8">
                                <i>' . $bl->getLastError() . '</i>
                            </td>
                        </tr>';
                } else {
                    // Bauen
                    if ($item->level == 0) {
                        echo '<tr>
                                <td>
                                    <input type="submit" class="button" name="command_build" value="Bauen">
                                </td>
                                <td>' . StringUtils::formatTimespan($costs['time']) . '</td>';
                    }
                    // Ausbauen
                    else {
                        echo '<tr>
                                <td>
                                    <input type="submit" class="button" name="command_build" value="Ausbauen">
                                </td>
                                <td>' . StringUtils::formatTimespan($costs['time']) . '</td>';
                    }
                    foreach ($resNames as $rk => $rn) {
                        echo '<td>' . StringUtils::formatNumber(ceil($costs['costs' . $rk])) . '</td>';
                    }
                    echo '<td>' . StringUtils::formatNumber(ceil($costs['costs5'])) . '</td>';
                    echo '</tr>';
                }
            }

            // Abreissen
            $canDemolish = ($item->level > 0 && $item->building->demolishCostsFactor != 0 && $item->buildType == 0);
            if ($canDemolish) {
                $waitArr = $item->waitingTimeString('demolish');
                // Es wird bereits an einem Gebäude gebaut
                if (!$bl->checkDemolishable($bid)) {
                    echo '<tr class="demolishActionContainer">
                                <td style="color:red;">Abreissen</td>
                                <td>' . StringUtils::formatTimespan($demolishCosts['time']) . '</td>'
                        . $waitArr['string'] . '
                                <td>' . StringUtils::formatNumber(ceil($costs['costs5'])) . '</td>
                            </tr>
                            <tr class="demolishActionContainer">
                                <td colspan="8">
                                    <i>' . $bl->getLastError() . '</i>
                                </td>
                            </tr>';
                } else {
                    echo '<tr class="demolishActionContainer">
                                <td>
                                    <input type="submit" class="button" name="command_demolish" value="Abreissen" onclick="if (this.value==\'Abreissen\'){return confirm(\'Geb&auml;de wirklich abreissen?\');}">
                                </td>
                                <td>' . StringUtils::formatTimespan($demolishCosts['time']) . '</td>';
                    foreach ($resNames as $rk => $rn) {
                        echo '<td>' . StringUtils::formatNumber(ceil($demolishCosts['costs' . $rk])) . '</td>';
                    }
                    echo '<td>' . StringUtils::formatNumber(ceil($demolishCosts['costs5'])) . '</td>';
                    echo '</tr>';
                }
            }


            // Bau abbrechen
            if ($item->buildType == 3) {
                echo '<tr>
                                <td id="buildcancel">
                                    <input type="submit" class="button" name="command_cbuild" value="Bau abbrechen" onclick="if (this.value==\'Bau abbrechen\'){return confirm(\'Wirklich abbrechen?\');}" />
                                </td>
                                <td id="buildtime" style="vertical-align:middle;">-</td>
                                <td colspan="6" id="progressbar" style="text-align:center;vertical-align:middle;font-weight:bold;"></td>
                            </tr>';
                if ($item->level < $item->building->maxLevel - 1) {
                    $costs = $bl->getCosts($bid, 'build', 1);
                    echo '<tr>
                                    <td width="90">N&auml;chste Stufe:</td>
                                    <td>' . StringUtils::formatTimespan($costs['time']) . '</td>';
                    foreach ($resNames as $rk => $rn) {
                        echo '<td>' . StringUtils::formatNumber(ceil($costs['costs' . $rk])) . '</td>';
                    }
                    echo '<td>' . StringUtils::formatNumber(ceil($costs['costs5'])) . '</td>';
                    echo '</tr>';
                }
            }

            // Abriss abbrechen
            if ($item->buildType == 4) {
                echo '<tr>
                                <td id="buildcancel">
                                    <input type="submit" class="button" name="command_cdemolish" value="Abriss abbrechen" onclick="if (this.value==\'Abriss abbrechen\'){return confirm(\'Wirklich abbrechen?\');}" />
                                </td>
                                <td id="buildtime" style="vertical-align:middle;">-</td>
                                <td colspan="6"  id="progressbar" style="text-align:center;vertical-align:middle;font-weight:bold;"></td>
                            </tr>';
            }
            tableEnd();

            if ($canDemolish) {
                echo '<div>Falls mehr Platz benötigt wird, kann dieses Gebäude <a href="javascript:;" onclick="$(\'.demolishActionContainer\').show();$(this).parent().hide();return false;">abgerissen</a> werden.</div>';
?>
                <script>
                    $(function() {
                        $('.demolishActionContainer').hide();
                    });
                </script>
<?PHP
            }

            if ($item->getWaitingTime() > 0) {
                echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Bau vorhanden sind: <b>" . StringUtils::formatTimespan($item->getWaitingTime()) . "</b><br/>";
            }
            if (isset($dWaitArray) && $dWaitArray[1] > 0) {
                echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Abriss vorhanden sind: <b>" . StringUtils::formatTimespan($dWaitArray[1]) . "</b><br/>";
            }
            echo "<br/>";

            if ($item->buildType == 3 || $item->buildType == 4) {
                countDown("buildtime", $item->endTime, "buildcancel");
                jsProgressBar("progressbar", $item->startTime, $item->endTime);
            }
        } else {
            echo '<div>Gebäude kann nicht (aus)gebaut werden, <a href="?page=techtree">Voraussetzungen</a> nicht erfüllt!<br/><br/></div>';
        }

        echo "<input type=\"submit\" name=\"command_show\" value=\"Aktualisieren\" /> &nbsp; ";
        echo "<input type=\"button\" value=\"Zur&uuml;ck zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
        echo "</form>";
    }
    /*	else {
            error_msg("Geb&auml;ude nicht vorhanden!");
            return_btn();
        }

/********************
* Übersicht         *
********************/ else {

        $tabitems = array(
            "all" => "Alle anzeigen",
            "buildable" => "Baubare Gebäude",
            "resable" => "Ausbaubare Gebäude",
        );
        show_tab_menu("mode", $tabitems);
        $mode = (isset($_GET['mode']) && ctype_alpha($_GET['mode'])) ? $_GET['mode'] : "all";

        /** @var BuildingTypeDataRepository $buildingTypeRepository */
        $buildingTypeRepository = $app[BuildingTypeDataRepository::class];
        $buildingTypeNames = $buildingTypeRepository->getTypeNames();
        if (count($buildingTypeNames) > 0) {
            $buildingLevels = $buildingRepository->getBuildingLevels($planet->id);
            // Jede Kategorie durchgehen
            echo '<form action="?page=' . $page . '" method="post"><div>';
            echo $checker;

            /** @var BuildingDataRepository $buildingRepository */
            $buildingRepository = $app[BuildingDataRepository::class];
            $buildingNames = $buildingRepository->getBuildingNames(true);

            /** @var TechnologyDataRepository $technologyRepository */
            $technologyRepository = $app[TechnologyDataRepository::class];
            $technologyNames = $technologyRepository->getTechnologyNames(true);

            foreach ($buildingTypeNames as $typeId => $typeName) {
                tableStart($typeName, TABLE_WIDTH);

                //Einfache Ansicht
                if ($properties->itemShow != 'full') {
                    echo "<tr>
                            <th colspan=\"2\">Gebäude</th>
                            <th>Zeit</th>
                            <th>" . RES_METAL . "</th>
                            <th>" . RES_CRYSTAL . "</th>
                            <th>" . RES_PLASTIC . "</th>
                            <th>" . RES_FUEL . "</th>
                            <th>" . RES_FOOD . "</th>
                            <th>Ausbau</th>
                        </tr>";
                }

                $cnt = 0; // Counter for current row
                $scnt = 0; // Counter for shown buildings

                $it = $bl->getCatIterator($typeId, $mode);

                while ($it->valid()) {
                    if ($properties->itemShow != 'full')
                        $img = $it->current()->building->imgPathSmall();
                    else
                        $img = $it->current()->building->imgPathMiddle();
                    $filterStyleClass = "";

                    if (!$bl->requirementsPassed($it->key())) {
                        $subtitle =  'Voraussetzungen fehlen';
                        $tmtext = '<span style="color:#999">Baue zuerst die nötigen Gebäude und erforsche die nötigen Technologien um diese Gebäude zu bauen!</span><br/>';

                        foreach ($it->current()->building->getBuildingRequirements() as $id => $level) {
                            $tmtext .= "<div style=\"color:" . ($level <= ($buildingLevels[$id] ?? 0) ? '#0f0' : '#f30') . "\">" . $buildingNames[$id] . " Stufe " . $level . "</div>";
                        }

                        foreach ($it->current()->building->getTechRequirements() as $id => $level) {
                            $tmtext .= "<div style=\"color:" . ($level <= ($techlist[$id] ?? 0) ? '#0f0' : '#f30') . "\">" . $technologyNames[$id] . " Stufe " . $level . "</div>";
                        }

                        $color = '#999';
                        if ($use_img_filter) {
                            $filterStyleClass = "filter-unavailable";
                        }
                    }
                    // Ist im Bau
                    elseif ($it->current()->buildType == 3) {
                        $subtitle =  "Ausbau auf Stufe " . ($it->current()->level + 1);
                        $tmtext = "<span style=\"color:#0f0\">Wird ausgebaut<br/>Dauer: " . StringUtils::formatTimespan($it->current()->endTime - time()) . "</span><br/>";
                        $color = '#0f0';
                        if ($use_img_filter) {
                            $filterStyleClass = "filter-building";
                        }
                    }
                    //Wird abgerissen
                    elseif ($it->current()->buildType == 4) {
                        $subtitle = "Abriss auf Stufe " . ($it->current()->level - 1);
                        $tmtext = "<span style=\"color:#f90\">Wird abgerissen!<br/>Dauer: " . StringUtils::formatTimespan($it->current()->endTime - time()) . "</span><br/>";
                        $color = '#f90';
                        if ($use_img_filter) {
                            $filterStyleClass = "filter-destructing";
                        }
                    }
                    // Untätig
                    else {
                        // Zuwenig Ressourcen
                        $waitArr = $it->current()->waitingTimeString('build');
                        if ($waitArr['max'] > 0) {
                            $tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen f&uuml;r weiteren Ausbau!</span><br/>";
                            $color = '#f00';

                            if ($use_img_filter) {
                                $filterStyleClass = "filter-noresources";
                            }
                        } else {
                            $tmtext = "";
                            $color = '#fff';
                            $style = [];
                            $style['metal'] = $style['crystal'] = $style['plastic'] = $style['food'] = "";
                        }

                        if ($it->current()->level == 0) {
                            $subtitle = "Noch nicht gebaut";
                        } elseif ($it->current()->isMaxLevel()) {
                            $subtitle = 'Vollständig ausgebaut';
                            $tmtext = '';
                        } else {
                            $subtitle = 'Stufe ' . $it->current()->level;
                        }
                    }

                    //Einfache Ansicht
                    if ($properties->itemShow != 'full') {
                        echo "<tr>
                                    <td>
                                        <a href=\"" . HELP_URL . "&amp;id=" . $it->key() . "\"><img class=\"" . $filterStyleClass . "\" src=\"" . $img . "\" width=\"40px\" height=\"40px\" border=\"0\" /></a>
                                </td>
                                <th width=\"45%\">
                                    <span style=\"font-weight:500\">" . $it->current()->building . "<br/>
                                            Stufe:</span> " . StringUtils::formatNumber($it->current()->level) . "
                                        </th>";
                        if (!$bl->requirementsPassed($it->key()) || $it->current()->isMaxLevel()) {
                            echo "<td width=\"90%\" style=\"color:#999\" colspan=\"7\" " . tm($it->current()->building, $subtitle . "<br/>" . $tmtext) . ">" . $subtitle . "</td>";
                        } elseif ($it->current()->buildType == 4 || $it->current()->buildType == 3) {
                            echo '<td id="buildtime" style="vertical-align:middle;">-</td>
                                <td colspan="5"  id="progressbar" style="text-align:center;vertical-align:middle;font-weight:bold;"></td>
                                <td id="buildcancel">
                                    <form action="?page=' . $page . '" method="post">
                                        <input type="hidden" name="id[' . $it->key() . ']" value="' . $it->key() . '">';
                            echo $checker;
                            echo '<input type="submit" class="button" name="command_cbuild[' . $it->key() . ']" value="Bau abbrechen" onclick="if (this.value==\'Bau abbrechen\'){return confirm(\'Wirklich abbrechen?\');}" />
                                </td>';
                            countDown("buildtime", $it->current()->endTime, "buildcancel");
                            jsProgressBar("progressbar", $it->current()->startTime, $it->current()->endTime);
                        } else {
                            echo '<td>' . StringUtils::formatTimespan($it->current()->getBuildTime()) . '</td>' . $waitArr['string'];

                            //Maximale Anzahl erreicht oder anderes Gebäude im Bau
                            if ($tmtext != "" || $bl->isUnderConstruction()) {
                                echo "<td style=\"color:red;\" " . tm($it->current()->building, $subtitle . "<br/>" . $tmtext) . ">Bauen</td></tr>";
                            } else {
                                echo '<td>
                                        <form action="?page=' . $page . '" method="post">
                                            <input type="hidden" name="id[' . $it->key() . ']" value="' . $it->key() . '">';
                                echo $checker;
                                echo '<input type="submit" class="button" name="command_build[' . $it->key() . ']" value="Ausbauen"></td</tr>';
                            }
                        }
                        echo '</tr>';
                        $scnt++;
                    } else {

                        if ($properties->itemShow == 'full') {
                            // Display row starter if needed
                            if ($cnt == 0) {
                                echo "<tr>";
                            }

                            echo "<td style=\"width:" . CELL_WIDTH . "px;height:" . CELL_WIDTH . "px ;padding:0px;\">";
                            echo "<div style=\"position:relative;height:" . CELL_WIDTH . "px;overflow:hidden;\">";
                            echo "<div class=\"buildOverviewObjectTitle\">" . $it->current()->building . "</div>";
                            echo "<a href=\"?page=$page&amp;id=" . $it->key() . "\" " . tm($it->current()->building, "<b>" . $subtitle . "</b><br/>" . $tmtext . $it->current()->building->shortDesc) . " style=\"display:block;height:180px;\"><img class=\"" . $filterStyleClass . "\" src=\"" . $img . "\"/></a>";
                            if ($it->current()->level || ($it->current()->level == 0 && isset($it->current()->buildType) && $it->current()->buildType == 3)) {
                                echo "<div class=\"buildOverviewObjectLevel\" style=\"color:" . $color . "\">" . $it->current()->level . "</div>";
                            }
                            echo "</div>";
                            echo "</td>\n";

                            $cnt++;
                            $scnt++;
                        }
                    }

                    // Display row finisher if needed
                    if ($cnt == NUM_BUILDINGS_PER_ROW) {
                        echo "</tr>";
                        $cnt = 0;
                    }
                    $it->next();
                }
                // Fill up missing cols and end row
                if ($cnt < NUM_BUILDINGS_PER_ROW && $cnt > 0) {
                    for ($x = 0; $x < NUM_BUILDINGS_PER_ROW - $cnt; $x++) {
                        echo "<td class=\"buildOverviewObjectNone\" style=\"width:" . CELL_WIDTH . "px;padding:0px;\">&nbsp;</td>";
                    }
                    echo '</tr>';
                }

                if ($scnt == 0) {
                    echo "<tr>
                            <td colspan=\"" . NUM_BUILDINGS_PER_ROW . "\" style=\"text-align:center;border:0;width:100%\">
                                <i>In dieser Kategorie kann momentan noch nichts gebaut werden!</i>
                                </td>
                            </tr>";
                }
                tableEnd();
            }
            echo '</div></form>';
        }
    }
}
