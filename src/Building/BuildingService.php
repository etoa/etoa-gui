<?php

namespace EtoA\Building;

use EtoA\Core\ObjectWithImage;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserPropertiesRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class BuildingService
{
    //TODO: refactor
    public function __construct(
        private readonly Security                 $security,
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly RequestStack $requestStack,
        private readonly BuildingDataRepository $buildingDataRepository,
        private readonly TechnologyDataRepository $technologyDataRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly BuildList $buildList,
        private readonly BuildingTypeDataRepository $buildingTypeDataRepository
    )
    {}

    public function renderBuilding(): bool|string
    {
        ob_start();

        /** @var BuildingTypeDataRepository $buildingTypeRepository */
        $buildingTypeNames = $this->buildingTypeDataRepository->getTypeNames();
        if (count($buildingTypeNames) > 0) {
            $user = $this->security->getUser()->getData();
            $request = $this->requestStack->getCurrentRequest();
            $properties = $this->userPropertiesRepository->getOrCreateProperties($user);
            $compactView = $properties->getItemShow() != 'full'?'compact':'';
            $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
            $buildingLevels = $this->buildingListItemRepository->getBuildingLevels($cp);

            // Aktiviert / Deaktiviert Bildfilter
            if ($properties->isImageFilter()) {
                $use_img_filter = true;
            } else {
                $use_img_filter = false;
            }

            if ($properties->getItemShow() != 'full') {
                $numBuildingsPerRow =  9;
                $tableWidth = '';
            } else {
                $numBuildingsPerRow = 5;
                $cellWith = 120;
                $tableWidth = 'auto';
            }

            // Jede Kategorie durchgehen
            echo '<form action="?page=" method="post"><div>';

            /** @var BuildingDataRepository $buildingRepository */
            $buildingNames = $this->buildingDataRepository->getBuildingNames(true);

            $technologyNames = $this->technologyDataRepository->getTechnologyNames(true);

            foreach ($buildingTypeNames as $typeId => $typeName) {
                echo '<table style="width:'.$tableWidth.'"  class="tb '.$compactView .'">';

                //Einfache Ansicht
                if ($properties->getItemShow() != 'full') {
                    echo "<tr>
                            <th colspan=\"2\">Gebäude</th>
                            <th>Zeit</th>
                            <th>" . ResourceNames::METAL . "</th>
                            <th>" . ResourceNames::CRYSTAL . "</th>
                            <th>" . ResourceNames::PLASTIC . "</th>
                            <th>" . ResourceNames::FUEL . "</th>
                            <th>" . ResourceNames::FOOD . "</th>
                            <th>Ausbau</th>
                        </tr>";
                }

                $cnt = 0; // Counter for current row
                $scnt = 0; // Counter for shown buildings

                $it = $this->buildList->getCatIterator($typeId);
                while ($it->valid()) {
                    if ($properties->getItemShow() != 'full')

                        $img = $this->imgPathSmall($it->current()->getId());
                    else
                        $img = $this->imgPathMiddle($it->current()->getId());
                    $filterStyleClass = "";

                    if (!$this->buildList->requirementsPassed($it->key())) {
                        $subtitle =  'Voraussetzungen fehlen';
                        $tmtext = '<span style="color:#999">Baue zuerst die nötigen Gebäude und erforsche die nötigen Technologien um diese Gebäude zu bauen!</span><br/>';

                        foreach ($it->current()->getObjectRequirements() as $requirement) {
                            if($requirement->getBuilding()) {
                                //$tmtext .= "<div style=\"color:" . ($level <= ($buildingLevels[$id] ?? 0) ? '#0f0' : '#f30') . "\">" . $buildingNames[$id] . " Stufe " . $level . "</div>";
                                //$tmtext .= "<div style=\"color:" . ($level <= ($techlist[$id] ?? 0) ? '#0f0' : '#f30') . "\">" . $technologyNames[$id] . " Stufe " . $level . "</div>";
                            }
                        }

                        $color = '#999';
                        if ($use_img_filter) {
                            $filterStyleClass = "filter-unavailable";
                        }
                    }
                    // Ist im Bau
                    elseif ($it->current()->bl?->getBuildType() === 3) {
                        $subtitle =  "Ausbau auf Stufe " . ($it->current()->level + 1);
                        $tmtext = "<span style=\"color:#0f0\">Wird ausgebaut<br/>Dauer: " . StringUtils::formatTimespan($it->current()->endTime - time()) . "</span><br/>";
                        $color = '#0f0';
                        if ($use_img_filter) {
                            $filterStyleClass = "filter-building";
                        }
                    }
                    //Wird abgerissen
                    elseif ($it->current()->bl?->getBuildType() === 4) {
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

                        //TODO
                        //$waitArr = $it->current()->waitingTimeString('build');
                        $waitArr['max'] = 1;

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

                        if (!$it->current()->bl?->getLevel()) {
                            $subtitle = "Noch nicht gebaut";
                        } elseif ($it->current()->bl->getLevel() === $it->current()->getLastLevel) {
                            $subtitle = 'Vollständig ausgebaut';
                            $tmtext = '';
                        } else {
                            $subtitle = 'Stufe ' . $it->current()->level;
                        }
                    }

                    //Einfache Ansicht
                    if ($properties->getItemShow() != 'full') {
                        echo "<tr>
                                    <td class='buildingCellImage'>
                                        <a href=\"" . HELP_URL . "&amp;id=" . $it->key() . "\"><img class=\"" . $filterStyleClass ." buildingImageSmall\" src=\"" . $img . "\" border=\"0\" /></a>
                                </td>
                                <th class='buildingCellName' width=\"45%\">
                                    <span style=\"font-weight:500\">" . $it->current()->building . "<br/>
                                            Stufe:</span> " . StringUtils::formatNumber($it->current()->level) . "
                                        </th>";
                        if (!$this->buildList->requirementsPassed($it->key()) || $it->current()->isMaxLevel()) {
                            echo "<td width=\"90%\" style=\"color:#999\" colspan=\"7\" " . tm($it->current()->building, $subtitle . "<br/>" . $tmtext) . ">" . $subtitle . "</td>";
                        } elseif ($it->current()->buildType == 4 || $it->current()->buildType == 3) {
                            echo '<td id="buildtime" style="vertical-align:middle;">-</td>
                                <td colspan="5"  id="progressbar" style="text-align:center;vertical-align:middle;font-weight:bold;"></td>
                                <td id="buildcancel">
                                    <form action="?page=" method="post">
                                        <input type="hidden" name="id[' . $it->key() . ']" value="' . $it->key() . '">';
                            echo '<input type="submit" class="button" name="command_cbuild[' . $it->key() . ']" value="Bau abbrechen" onclick="if (this.value==\'Bau abbrechen\'){return confirm(\'Wirklich abbrechen?\');}" />
                                </td>';
                            countDown("buildtime", $it->current()->endTime, "buildcancel");
                            jsProgressBar("progressbar", $it->current()->startTime, $it->current()->endTime);
                        } else {
                            echo '<td class="buildingCellTime">' . StringUtils::formatTimespan($it->current()->getBuildTime()) . '</td>' . $waitArr['string'];

                            //Maximale Anzahl erreicht oder anderes Gebäude im Bau
                            if ($tmtext != "" || $this->buildList->isUnderConstruction()) {
                                echo "<td style=\"color:red;\" " . tm($it->current()->building, $subtitle . "<br/>" . $tmtext) . ">Bauen</td></tr>";
                            } else {
                                echo '<td>
                                        <form action="?page=" method="post">
                                            <input type="hidden" name="id[' . $it->key() . ']" value="' . $it->key() . '">';
                                echo '<input type="submit" class="button" name="command_build[' . $it->key() . ']" value="Ausbauen"></td</tr>';
                            }
                        }
                        echo '</tr>';
                        $scnt++;
                    } else {

                        if ($properties->getItemShow() == 'full') {
                            // Display row starter if needed
                            if ($cnt == 0) {
                                echo "<tr>";
                            }

                            echo "<td style=\"width:" . $cellWith . "px;height:" . $cellWith . "px ;padding:0px;\">";
                            echo "<div style=\"position:relative;height:" . $cellWith . "px;overflow:hidden;\">";
                            echo "<div class=\"buildOverviewObjectTitle\">" . $it->current()->getName() . "</div>";
                            echo "<a href=\"?page=id=" . $it->key() . "\" " . tm($it->current()->getName(), "<b>" . $subtitle . "</b><br/>" . $tmtext . $it->current()->getShortComment()) . " style=\"display:block;height:180px;\"><img class=\"" . $filterStyleClass . "\" src=\"" . $img . "\"/></a>";
                            if ($it->current()->bl?->getCurrentLevel() || ($it->current()->bl?->getCurrentlevel() == 0 && $it->current()->bl?->getBuildType() == 3)) {
                                echo "<div class=\"buildOverviewObjectLevel\" style=\"color:" . $color . "\">" . $it->current()->bl->getCurrentLevel() . "</div>";
                            }
                            echo "</div>";
                            echo "</td>\n";

                            $cnt++;
                            $scnt++;
                        }
                    }

                    // Display row finisher if needed
                    if ($cnt == $numBuildingsPerRow) {
                        echo "</tr>";
                        $cnt = 0;
                    }
                    $it->next();
                }
                // Fill up missing cols and end row
                if ($cnt < $numBuildingsPerRow && $cnt > 0) {
                    for ($x = 0; $x < $numBuildingsPerRow - $cnt; $x++) {
                        echo "<td class=\"buildOverviewObjectNone\" style=\"width:" . $cellWith . "px;padding:0px;\">&nbsp;</td>";
                    }
                    echo '</tr>';
                }

                if ($scnt == 0) {
                    echo "<tr>
                            <td colspan=\"" . $numBuildingsPerRow . "\" style=\"text-align:center;border:0;width:100%\">
                                <i>In dieser Kategorie kann momentan noch nichts gebaut werden!</i>
                                </td>
                            </tr>";
                }
                echo '</table>';
            }
            echo '</div></form>';
        }

        return ob_get_clean();
    }

    public function imgPathSmall(string $id): string
    {
        return ObjectWithImage::BASE_PATH . "/buildings/building" . $id . "_small.png";
    }

    public function imgPathMiddle(string $id): string
    {
        return ObjectWithImage::BASE_PATH . "/buildings/building" . $id . "_middle.png";
    }

    public function imgPathBig(string $id): string
    {
        return ObjectWithImage::BASE_PATH . "/buildings/building" . $id . ".png";
    }

    public function waitingTimeString($type = 'build')
    {
        // TODO
        global $cp;

        $notAvStyle = " style=\"color:red;\"";
        if ($type == 'build')
            $costs = $this->getBuildCosts(0);
        else
            $costs = $this->getDemolishCosts(0);

        $wTime = array();
        // Wartezeiten auf Ressourcen berechnen
        foreach (ResourceNames::NAMES as $rk => $rn) {
            if ($cp->getProd($rk)) {
                $wTime[$rk] = ceil(($costs['costs' . $rk] - $cp->getRes1($rk)) / $cp->getProd($rk) * 3600);
            } else
                $wTime[$rk] = 0;
        }
        $wTime['max'] = max($wTime);

        $wTime['string'] = "";
        foreach (ResourceNames::NAMES as $rk => $rn) {
            $wTime['string'] .= '<td ';
            if ($costs['costs' . $rk] > $cp->getRes1($rk)) {
                $wTime['string'] .= $notAvStyle . ' ' . tm('Fehlender Rohstoff', '<strong>' . StringUtils::formatNumber(ceil($costs['costs' . $rk] - $cp->getRes1($rk))) . '</strong> ' . $rn . '<br />Bereit in <strong>' . StringUtils::formatTimespan($wTime[$rk]) . '</strong>');
            }
            $wTime['string'] .= '>' . StringUtils::formatNumber(ceil($costs['costs' . $rk])) . '</td>';
        }
        return $wTime;
    }

    public function getBuildCosts($levelUp = 0)
    {
        if (!(count($this->costs) > 0 && !$levelUp) || !(count($this->nextCosts) > 0  && $levelUp)) {
            // TODO
            global $cp, $cu, $bl, $app;

            /** @var BuildingRepository $buildingRepository */
            $buildingRepository = $app[BuildingRepository::class];
            $peopleWorking = $buildingRepository->getPeopleWorking($this->entityId);

            /** @var RaceDataRepository $raceRepository */
            $raceRepository = $app[RaceDataRepository::class];
            $race = $raceRepository->getRace($cu->raceId);

            /** @var SpecialistService $specialistService */
            $specialistService = $app[SpecialistService::class];
            $specialist = $specialistService->getSpecialistOfUser($cu->id);
            $specialistBuildingCostFactor = $specialist !== null ? $specialist->costsBuildings : 1;
            $specialistBuildTimeFactor = $specialist !== null ? $specialist->timeBuildings : 1;

            $bc = array();
            foreach (ResourceNames::NAMES as $rk => $rn) {
                $bc['costs' . $rk] = $specialistBuildingCostFactor * $this->building->costs[$rk] * pow($this->building->costsFactor, $this->level + $levelUp);
            }

            $bonus = $race->buildTime + $cp->typeBuildtime + $cp->starBuildtime + $specialistBuildTimeFactor - 3;

            $bc['time'] = (array_sum($bc)) / $this->config->getInt('global_time') * $this->config->getFloat('build_build_time');
            $bc['time'] *= $bonus;

//Use Build calculator

            // Boost
            if ($this->config->getBoolean('boost_system_enable')) {
                $bc['time'] *= 1 / ($cu->boostBonusBuilding + 1);
            }

            if ($this->level != 0) {
                $bc['costs5'] = ($specialistBuildingCostFactor * $this->building->costs[5] * pow($this->building->prodFactor, $this->level + $levelUp)) -
                    ($specialistBuildingCostFactor * $this->building->costs[5] * pow($this->building->prodFactor, $this->level - 1));
            } else {
                $bc['costs5'] = ($specialistBuildingCostFactor * $this->building->costs[5] * pow($this->building->prodFactor, $this->level + $levelUp));
            }

            if ($peopleWorking->building > 0) {
                $bc['min_time'] = $bc['time'] * $this->minBuildTimeFactor();
                $bc['time'] -= ($peopleWorking->building * $this->config->getInt('people_work_done'));
                if ($bc['time'] < $bc['min_time'])
                    $bc['time'] = $bc['min_time'];
                $bc['costs4'] += $peopleWorking->building * $this->config->getInt('people_food_require');
            }

            if ($levelUp)
                $this->nextCosts = $bc;
            else
                $this->costs = $bc;
            unset($bc);
        }
        if ($levelUp)
            return $this->nextCosts;
        else
            return $this->costs;
    }

    public function getDemolishCosts($levelUp = 0)
    {
        if (count($this->demolishCosts) === 0) {
            $this->demolishCosts = $this->getBuildCosts($levelUp);

            foreach ($this->demolishCosts as $id => $element) {
                if ($id == 'costs5') $element = 0;
                $this->demolishCosts[$id] = $element * $this->building->demolishCostsFactor;
            }
        }
        return $this->demolishCosts;
    }
}