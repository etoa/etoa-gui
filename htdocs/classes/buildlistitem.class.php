<?PHP

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Race\RaceDataRepository;
use EtoA\Support\StringUtils;
use EtoA\Specialist\SpecialistService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;

class BuildListItem
{
    // item data
    private $id = 0;
    private $ownerId = 0;
    private $entityId = 0;
    private $buildingId = 0;
    private $buildType = 0;
    private $level = 0;
    private $startTime = 0;
    private $endTime = 0;
    private $prodPercent = 1;
    private $peopleWorking = 0;
    private $peopleWorkingStatus = 0;
    private $deactivated = 0;
    private $cooldown = 0;

    // building data
    private $building = null;

    // changed data
    private $changedFields = array();

    // calculations
    private $buildableStatus = null;
    /** @var array<string, int> */
    private $costs = array();
    private $demolishCosts = array();
    /** @var array<string, int> */
    private $nextCosts = array();

    private ConfigurationService $config;
    private PlanetRepository $planetRepo;

    public function __construct(array $arr)
    {
        // TODO
        global $app;

        $this->config = $app[ConfigurationService::class];
        $this->planetRepo = $app[PlanetRepository::class];

        if (intval($arr['buildlist_id']) > 0) {
            $this->id = $arr['buildlist_id'];
            $this->ownerId = $arr['buildlist_user_id'];
            $this->entityId = $arr['buildlist_entity_id'];
            $this->buildingId = $arr['buildlist_building_id'];
            $this->buildType = $arr['buildlist_build_type'];
            $this->level = $arr['buildlist_current_level'];
            $this->startTime = $arr['buildlist_build_start_time'];
            $this->endTime = $arr['buildlist_build_end_time'];
            $this->prodPercent = $arr['buildlist_prod_percent'];
            $this->peopleWorking = $arr['buildlist_people_working'];
            $this->peopleWorkingStatus = $arr['buildlist_people_working_status'];
            $this->deactivated = $arr['buildlist_deactivated'];
            $this->cooldown = $arr['buildlist_cooldown'];
        } else {
            // TODO
            global $cp, $cu;

            $this->ownerId = $cu->id;
            $this->entityId = $cp->id;
            $this->buildingId = $arr['building_id'];
        }

        if (isset($arr['building_id'])) {
            $this->building = new Building($arr);
        }
    }

    public function __toString()
    {
        if ($this->buildingId > 0) {
            $title = $this->building . ' <span id="buildlevel">';
            $title .= $this->level > 0 ? $this->level : '';
            $title .= '</span>';
            return $title;
        }
        return $this->id;
    }

    public function __set($key, $val)
    {
        try {
            if (!property_exists($this, $key))
                throw new EException("Property $key existiert nicht in der Klasse " . __CLASS__);

            if ($key == "cooldown") {
                $this->changedFields[$key] = "buildlist_cooldown";
                $this->$key = $val;
            } elseif ($key == 'buildableStatus') {
                $this->$key = $val;
            } else {
                throw new EException("Property $key hat keine UPDATE-Instruktion in der Klasse " . __CLASS__);
            }
        } catch (EException $e) {
            echo $e;
        }
    }

    public function __get($key)
    {
        try {
            if (!property_exists($this, $key))
                throw new EException("Property $key existiert nicht in " . __CLASS__);
            if ($key == "bunkerRes") {
                return $this->building->bunkerRes * intpow($this->building->storeFactor, $this->level - 1);
            } elseif ($key == "bunkerFleetCount") {
                return $this->building->bunkerFleetCount * intpow($this->building->storeFactor, $this->level - 1);
            } elseif ($key == "bunkerFleetSpace") {
                return $this->building->bunkerFleetSpace * intpow($this->building->storeFactor, $this->level - 1);
            }

            return $this->$key;
        } catch (EException $e) {
            echo $e;
            return null;
        }
    }

    public function resetCalculation()
    {
        $this->buildableStatus = null;
        $this->costs = array();
        $this->nextCosts = array();
        $this->demolishCosts = array();
    }

    public function setPeopleWorking($people, $force = false)
    {
        if ($this->buildType == 0 || $force) {
            global $app;

            /** @var BuildingRepository $buildingRepository */
            $buildingRepository = $app[BuildingRepository::class];
            $buildingRepository->setPeopleWorking($this->entityId, $this->buildingId, (int) $people);
            $this->peopleWorking = $people;
            return true;
        }
        return false;
    }

    public function isMaxLevel()
    {
        return $this->level >= $this->building->maxLevel ? true : false;
    }

    public function getBuildTime()
    {
        if (count($this->costs) === 0) {
            $this->getBuildCosts();
        }
        return $this->costs['time'];
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

    public function build()
    {
        // TODO
        global $cp, $cu, $bl, $app;

        /** @var GameLogRepository $gameLogRepository */
        $gameLogRepository = $app[GameLogRepository::class];

        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        $peopleWorking = $buildingRepository->getPeopleWorking($this->entityId);

        /** @var SpecialistService $specialistService */
        $specialistService = $app[SpecialistService::class];
        $specialist = $specialistService->getSpecialistOfUser($cu->id);

        $costs = $this->getBuildCosts();
        $this->changedFields['startTime'] = "buildlist_build_start_time";
        $this->changedFields['endTime'] = "buildlist_build_end_time";
        $this->changedFields['buildType'] = "buildlist_build_type";

        $this->startTime = time();
        $this->endTime = $this->startTime + $costs['time'];
        $this->buildType = 3;

        if ($this->id > 0) {
            $buildingRepository->updateBuildingListEntry($this->id, $this->level, $this->buildType, $this->startTime, $this->endTime);
        } else {
            $buildingRepository->addBuilding($this->buildingId, $this->level, $this->ownerId, $this->entityId, $this->buildType, $this->startTime, $this->endTime);
        }

        $buildingRepository->markBuildingWorkingStatus($cu->getId(), (int) $cp->id, BuildingId::BUILDING, true);

        BuildList::$underConstruction = true;

        $this->planetRepo->addResources($cp->id, -$costs['costs0'], -$costs['costs1'], -$costs['costs2'], -$costs['costs3'], -$costs['costs4']);

        //Log schreiben
        $log_text = "[b]Gebäudebau[/b]

        [b]Baudauer:[/b] " . StringUtils::formatTimespan($costs['time']) . "
        [b]Ende:[/b] " . date("d.m.Y H:i:s", $this->endTime) . "
        [b]Eingesetzte Bewohner:[/b] " . StringUtils::formatNumber($peopleWorking->building) . "
        [b]Gen-Tech Level:[/b] " . BuildList::$GENTECH . "
        [b]Eingesetzter Spezialist:[/b] " . ($specialist !== null ? $specialist->name : "Kein Spezialist") . "

        [b]Kosten[/b]
        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($costs['costs0']) . "
        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs['costs1']) . "
        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($costs['costs2']) . "
        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($costs['costs3']) . "
        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($costs['costs4']) . "

        [b]Restliche Rohstoffe auf dem Planeten[/b]
        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->resMetal) . "
        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->resCrystal) . "
        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->resPlastic) . "
        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->resFuel) . "
        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->resFood) . "";

        //Log Speichern
        $gameLogRepository->add(GameLogFacility::BUILD, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $cp->id, $this->buildingId, 3, $this->level);

        return;
    }

    public function getPeopleOptimized()
    {
        // TODO
        global $cp, $cu, $app;

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
            $bc['costs' . $rk] = $specialistBuildingCostFactor * $this->building->costs[$rk] * pow($this->building->costsFactor, $this->level);
        }
        $bc['costs5'] = 0;      //Energie nicht als Ressource zählen

        $bonus = $race->buildTime + $cp->typeBuildtime + $cp->starBuildtime + $specialistBuildTimeFactor - 3;

        $bc['time'] = (array_sum($bc)) / $this->config->getInt('global_time') * $this->config->getFloat('build_build_time');
        $bc['time'] *= $bonus;
        $bc['time'] /= $cu->boostBonusBuilding + 1;

        $maxReduction = $bc['time'] - $bc['time'] * $this->minBuildTimeFactor();

        return ceil($maxReduction / $this->config->getInt('people_work_done'));
    }

    public function minBuildTimeFactor()
    {
        return (0.1 - (BuildList::$GENTECH / 100));
    }

    public function demolish()
    {
        // TODO
        global $cp, $cu, $app;

        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        /** @var GameLogRepository $gameLogRepository */
        $gameLogRepository = $app[GameLogRepository::class];

        $costs = $this->getDemolishCosts();
        $this->changedFields['startTime'] = "buildlist_build_start_time";
        $this->changedFields['endTime'] = "buildlist_build_end_time";
        $this->changedFields['buildType'] = "buildlist_build_type";

        $this->startTime = time();
        $this->endTime = $this->startTime + $costs['time'];
        $this->buildType = 4;

        $buildingRepository->updateBuildingListEntry($this->id, $this->level, $this->buildType, $this->startTime, $this->endTime);
        BuildList::$underConstruction = true;

        $this->planetRepo->addResources($cp->id, -$costs['costs0'], -$costs['costs1'], -$costs['costs2'], -$costs['costs3'], -$costs['costs4']);

        //Log schreiben
        $log_text = "[b]Gebäudeabriss[/b]

        [b]Abrissdauer:[/b] " . StringUtils::formatTimespan($costs['time']) . "
        [b]Ende:[/b] " . date("d.m.Y H:i:s", $this->endTime) . "

        [b]Kosten[/b]
        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($costs['costs0']) . "
        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs['costs1']) . "
        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($costs['costs2']) . "
        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($costs['costs3']) . "
        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($costs['costs4']) . "

        [b]Restliche Rohstoffe auf dem Planeten[/b]
        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->resMetal) . "
        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->resCrystal) . "
        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->resPlastic) . "
        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->resFuel) . "
        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->resFood) . "";

        //Log Speichern
        $gameLogRepository->add(GameLogFacility::BUILD, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $cp->id, $this->buildingId, 4, $this->level);

        return;
    }

    public function cancelBuild()
    {
        if ($this->endTime > time()) {
            // TODO
            global $cp, $cu, $app;

            /** @var BuildingRepository $buildingRepository */
            $buildingRepository = $app[BuildingRepository::class];
            /** @var GameLogRepository $gameLogRepository */
            $gameLogRepository = $app[GameLogRepository::class];

            $costs = $this->getBuildCosts();
            $fac = ($this->endTime - time()) / ($this->endTime - $this->startTime);
            $this->endTime = 0;
            $this->startTime = 0;
            $this->buildType = 0;

            $buildingRepository->updateBuildingListEntry($this->id, $this->level, 0, 0, 0);

            $buildingRepository->markBuildingWorkingStatus($cu->getId(), (int) $cp->id, BuildingId::BUILDING, false);

            BuildList::$underConstruction = false;

            $this->planetRepo->addResources($cp->id, $costs['costs0'] * $fac, $costs['costs1'] * $fac, $costs['costs2'] * $fac, $costs['costs3'] * $fac, $costs['costs4'] * $fac);

            //Log schreiben
            $log_text = "[b]Gebäudebau Abbruch[/b]

[b]Start des Gebädes:[/b] " . date("d.m.Y H:i:s", $this->startTime) . "
[b]Ende des Gebädes:[/b] " . date("d.m.Y H:i:s", $this->endTime) . "

[b]Erhaltene Rohstoffe[/b]
[b]Faktor:[/b] " . $fac . "
[b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($costs['costs0'] * $fac) . "
[b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs['costs1'] * $fac) . "
[b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($costs['costs2'] * $fac) . "
[b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($costs['costs3'] * $fac) . "
[b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($costs['costs4'] * $fac) . "

[b]Rohstoffe auf dem Planeten[/b]
[b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->resMetal) . "
[b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->resCrystal) . "
[b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->resPlastic) . "
[b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->resFuel) . "
[b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->resFood) . "";

            //Log Speichern
            $gameLogRepository->add(GameLogFacility::BUILD, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $cp->id, $this->buildingId, 1, $this->level);

            return;
        } else
            return "Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!";
    }

    public function cancelDemolish()
    {
        if ($this->endTime > time()) {
            // TODO
            global $cp, $cu, $app;

            /** @var BuildingRepository $buildingRepository */
            $buildingRepository = $app[BuildingRepository::class];
            /** @var GameLogRepository $gameLogRepository */
            $gameLogRepository = $app[GameLogRepository::class];

            $costs = $this->getDemolishCosts();
            $fac = ($this->endTime - time()) / ($this->endTime - $this->startTime);
            $this->endTime = 0;
            $this->startTime = 0;
            $this->buildType = 0;

            $buildingRepository->updateBuildingListEntry($this->id, $this->level, 0, 0, 0);
            BuildList::$underConstruction = false;

            $this->planetRepo->addResources($cp->id, $costs['costs0'] * $fac, $costs['costs1'] * $fac, $costs['costs2'] * $fac, $costs['costs3'] * $fac, $costs['costs4'] * $fac);

            //Log schreiben
            $log_text = "[b]Gebäudeabriss Abbruch[/b]

            [b]Start des Gebädes:[/b] " . date("d.m.Y H:i:s", $this->startTime) . "
            [b]Ende des Gebädes:[/b] " . date("d.m.Y H:i:s", $this->endTime) . "

            [b]Erhaltene Rohstoffe[/b]
            [b]Faktor:[/b] " . $fac . "
            [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($costs['costs0'] * $fac) . "
            [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($costs['costs1'] * $fac) . "
            [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($costs['costs2'] * $fac) . "
            [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($costs['costs3'] * $fac) . "
            [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($costs['costs4'] * $fac) . "

            [b]Rohstoffe auf dem Planeten[/b]
            [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->resMetal) . "
            [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->resCrystal) . "
            [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->resPlastic) . "
            [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->resFuel) . "
            [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->resFood) . "";

            //Log Speichern
            $gameLogRepository->add(GameLogFacility::BUILD, LogSeverity::INFO, $log_text, $cu->id, $cu->allianceId, $cp->id, $this->buildingId, 2, $this->level);

            return;
        } else
            return "Abbruchauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!";
    }

    public function getWaitingTime()
    {
        // TODO
        global $cp;

        $costs = $this->getBuildCosts(0);
        $wTime = array();
        // Wartezeiten auf Ressourcen berechnen
        foreach (ResourceNames::NAMES as $rk => $rn) {
            if ($cp->getProd($rk)) {
                $wTime[$rk] = ceil(($costs['costs' . $rk] - $cp->getRes1($rk)) / $cp->getProd($rk) * 3600);
            } else
                $wTime[$rk] = 0;
        }
        return max($wTime);
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
}
