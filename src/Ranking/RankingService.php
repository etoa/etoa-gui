<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceStats;
use EtoA\Alliance\AllianceStatsRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\Building;
use EtoA\Entity\Defense;
use EtoA\Entity\Ship;
use EtoA\Entity\Technology;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserStatistic;
use EtoA\User\UserStatRepository;

/**
 * Provides static functions for
 * calculating and displaying
 * player ranking
 */
class RankingService
{
    private ConfigurationService $config;
    private RuntimeDataStore $runtimeDataStore;
    private AllianceRepository $allianceRepository;
    private AllianceStatsRepository $allianceStatsRepository;
    private PlanetRepository $planetRepository;
    private BuildingListItemRepository $buildingRepository;
    private BuildingDataRepository $buildingDataRepository;
    private BuildingPointRepository $buildingPointRepository;
    private TechnologyRepository $technologyRepository;
    private TechnologyDataRepository $technologyDataRepository;
    private TechnologyPointRepository $technologyPointRepository;
    private ShipRepository $shipRepository;
    private ShipDataRepository $shipDataRepository;
    private FleetRepository $fleetRepository;
    private DefenseRepository $defenseRepository;
    private DefenseDataRepository $defenseDataRepository;
    private RaceDataRepository $raceRepository;
    private UserStatRepository $userStatRepository;
    private UserRepository $userRepository;
    private EntityRepository $entityRepository;
    private AllianceBuildingRepository $allianceBuildingRepository;
    private AllianceTechnologyRepository $allianceTechnologyRepository;
    private UserPointsRepository $userPointsRepository;

    public function __construct(ConfigurationService $config, RuntimeDataStore $runtimeDataStore, AllianceRepository $allianceRepository, AllianceStatsRepository $allianceStatsRepository, PlanetRepository $planetRepository, BuildingListItemRepository $buildingRepository, BuildingDataRepository $buildingDataRepository, BuildingPointRepository $buildingPointRepository, TechnologyRepository $technologyRepository, TechnologyDataRepository $technologyDataRepository, TechnologyPointRepository $technologyPointRepository, ShipRepository $shipRepository, ShipDataRepository $shipDataRepository, FleetRepository $fleetRepository, DefenseRepository $defenseRepository, DefenseDataRepository $defenseDataRepository, RaceDataRepository $raceRepository, UserStatRepository $userStatRepository, UserRepository $userRepository, EntityRepository $entityRepository, AllianceBuildingRepository $allianceBuildingRepository, AllianceTechnologyRepository $allianceTechnologyRepository, UserPointsRepository $userPointsRepository)
    {
        $this->config = $config;
        $this->runtimeDataStore = $runtimeDataStore;
        $this->allianceRepository = $allianceRepository;
        $this->allianceStatsRepository = $allianceStatsRepository;
        $this->planetRepository = $planetRepository;
        $this->buildingRepository = $buildingRepository;
        $this->buildingDataRepository = $buildingDataRepository;
        $this->buildingPointRepository = $buildingPointRepository;
        $this->technologyRepository = $technologyRepository;
        $this->technologyDataRepository = $technologyDataRepository;
        $this->technologyPointRepository = $technologyPointRepository;
        $this->shipRepository = $shipRepository;
        $this->shipDataRepository = $shipDataRepository;
        $this->fleetRepository = $fleetRepository;
        $this->defenseRepository = $defenseRepository;
        $this->defenseDataRepository = $defenseDataRepository;
        $this->raceRepository = $raceRepository;
        $this->userStatRepository = $userStatRepository;
        $this->userRepository = $userRepository;
        $this->entityRepository = $entityRepository;
        $this->allianceBuildingRepository = $allianceBuildingRepository;
        $this->allianceTechnologyRepository = $allianceTechnologyRepository;
        $this->userPointsRepository = $userPointsRepository;
    }

    public function calc(): RankingCalculationResult
    {
        $time = time();
        $inactiveTime = 86400 * $this->config->getInt('user_inactive_days');
        $allpoints = 0;

        $shipPoints = $this->shipDataRepository->getShipPoints();
        $defensePoints = $this->defenseDataRepository->getDefensePoints();

        if (!$this->buildingPointRepository->areCalculated()) {
            $this->calcBuildingPoints();
        }
        $buildingPoints = $this->buildingPointRepository->getAllMap();

        if (!$this->technologyPointRepository->areCalculated()) {
            $this->calcTechPoints();
        }
        $techPoints = $this->technologyPointRepository->getAllMap();

        $race = $this->raceRepository->getRaceNames();

        $allianceTags = $this->allianceRepository->getAllianceTags();

        // Load 'old' ranks
        $ranks = $this->userStatRepository->getUserRanks();
        $oldranks = array();
        foreach ($ranks as $userRanks) {
            $oldranks[(int) $userRanks['id']][0] = (int) $userRanks['rank'];
            $oldranks[(int) $userRanks['id']][1] = (int) $userRanks['rank_ships'];
            $oldranks[(int) $userRanks['id']][2] = (int) $userRanks['rank_tech'];
            $oldranks[(int) $userRanks['id']][3] = (int) $userRanks['rank_buildings'];
            $oldranks[(int) $userRanks['id']][4] = (int) $userRanks['rank_exp'];
        }

        $user_rank_highest = [];
        $max_points_building = 0;
        $max_points = 0;
        $points_arr = [];

        $includeUsersInHolidas = $this->config->getBoolean('show_hmod_users_stats');
        $users = $this->userRepository->searchUsers(UserSearch::create()
            ->notGhost()
            ->inHolidays($includeUsersInHolidas ? null : false));

        /** @var UserStatistic[] $userStats */
        $userStats = [];
        foreach ($users as $user) {
            // first 24hours no highest rank calculation
            if (time() > (3600 * 24 + $this->config->param1Int("enable_login"))) {
                $user_rank_highest[$user->getId()] = $user->getRankHighest() > 0 ? $user->getRankHighest() : 9999;
            } else {
                $user_rank_highest[$user->getId()] = 0;
            }

            $points = 0.0;
            $points_ships = 0.0;
            $points_tech = 0;
            $points_building = 0;
            $sx = 0;
            $sy = 0;

            $planets = $this->planetRepository->getUserPlanets($user->getId());
            foreach ($planets as $planet) {
                if ($planet->isMainPlanet()) {
                    $entity = $this->entityRepository->findIncludeCell($planet->getId());
                    $sx = $entity->getCell()->getSx();
                    $sy = $entity->getCell()->getSy();

                    break;
                }
            }

            $shipListItems = $this->shipRepository->findForUser($user->getId());
            foreach ($shipListItems as $shipListItem) {
                $p = ($shipListItem->getBunkered() + $shipListItem->getCount()) * $shipPoints[$shipListItem->getShipId()];
                $points += $p;
                $points_ships += $p;
            }

            $fleets = $this->fleetRepository->findByParameters(FleetSearchParameters::create()->userId($user->getId()));
            foreach ($fleets as $fleet) {
                foreach ($this->fleetRepository->findAllShipsInFleet($fleet->getId()) as $shipEntry) {
                    $p = $shipEntry->count * $shipPoints[$shipEntry->shipId];
                    $points += $p;
                    $points_ships += $p;
                }
            }

            $defenseListItems = $this->defenseRepository->findForUser($user->getId());
            foreach ($defenseListItems as $defenseListItem) {
                $p = round($defenseListItem->getCount() * $defensePoints[$defenseListItem->getDefenseId()]);
                $points += $p;
                $points_building += $p;
            }

            foreach ($planets as $planet) {
                $buildingLevels = $this->buildingRepository->getBuildingLevels($planet->getId());
                foreach ($buildingLevels as $buildingId => $level) {
                    $p = round($buildingPoints[$buildingId][$level]);
                    $points += $p;
                    $points_building += $p;
                }
            }

            $techList = $this->technologyRepository->getTechnologyLevels($user->getId());
            foreach ($techList as $technologyId => $level) {
                $p = round($techPoints[$technologyId][$level] ?? 0);
                $points += $p;
                $points_tech += $p;
            }

            $points_exp = max(0, $this->shipRepository->getSpecialShipExperienceSumForUser($user->getId()));
            $points_exp += max(0, $this->fleetRepository->getSpecialShipExperienceSumForUser($user->getId()));

            $userStats[] = UserStatistic::createFromCalculation(
                $user,
                $user->getBlockedTo() > $time,
                $user->getHmodFrom() > 0,
                $user->getLogoutTime() < $time - $inactiveTime,
                $user->getAllianceId(),
                $user->getAllianceId() > 0 ? $allianceTags[$user->getAllianceId()] : null,
                $user->getRaceId() > 0 ? $race[$user->getRaceId()] : null,
                $sx,
                $sy,
                (int) $points,
                (int) $points_ships,
                (int) $points_tech,
                (int) $points_building,
                $points_exp
            );

            $allpoints += $points;

            $max_points_building = max($max_points_building, $points_building);
        }

        // Calculate rank shift
        usort($userStats, fn (UserStatistic $a, UserStatistic $b) => $b->points <=> $a->points);
        if (count($userStats) > 0) {
            $rank = 1;
            foreach ($userStats as $stats) {
                $rankShift = 0;
                if (isset($oldranks[$stats->userId])) {
                    if ($rank < $oldranks[$stats->userId][0]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->userId][0]) {
                        $rankShift = 2;
                    }
                }

                $stats->rank = $rank;
                $stats->rankShift = $rankShift;
                $rank++;

                $this->userRepository->updatePointsAndRank($stats, $user_rank_highest[$stats->userId]);

                $max_points = max($max_points, $stats->points);
                $points_arr[$stats->userId] = $stats->points;
            }
        }

        // Calculate ship rank shift
        usort($userStats, fn (UserStatistic $a, UserStatistic $b) => $b->shipPoints <=> $a->shipPoints);
        if (count($userStats) > 0) {
            $rank = 1;
            foreach ($userStats as $stats) {
                $rankShift = 0;
                if (isset($oldranks[$stats->userId])) {
                    if ($rank < $oldranks[$stats->userId][1]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->userId][1]) {
                        $rankShift = 2;
                    }
                }

                $stats->rankShips = $rank;
                $stats->rankShiftShips = $rankShift;
                $rank++;
            }
        }

        // Calculate technology rank shift
        usort($userStats, fn (UserStatistic $a, UserStatistic $b) => $b->techPoints <=> $a->techPoints);
        if (count($userStats) > 0) {
            $rank = 1;
            foreach ($userStats as $stats) {
                $rankShift = 0;
                if (isset($oldranks[$stats->userId])) {
                    if ($rank < $oldranks[$stats->userId][2]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->userId][2]) {
                        $rankShift = 2;
                    }
                }

                $stats->rankTech = $rank;
                $stats->rankShiftTech = $rankShift;
                $rank++;
            }
        }

        // Calculate building rank shift
        usort($userStats, fn (UserStatistic $a, UserStatistic $b) => $b->buildingPoints <=> $a->buildingPoints);
        if (count($userStats) > 0) {
            $rank = 1;
            foreach ($userStats as $stats) {
                $rankShift = 0;
                if (isset($oldranks[$stats->userId])) {
                    if ($rank < $oldranks[$stats->userId][3]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->userId][3]) {
                        $rankShift = 2;
                    }
                }

                $stats->rankBuildings = $rank;
                $stats->rankShiftBuilding = $rankShift;
                $rank++;
            }
        }

        // Calculate exp rank shift
        usort($userStats, fn (UserStatistic $a, UserStatistic $b) => $b->expPoints <=> $a->expPoints);
        if (count($userStats) > 0) {
            $rank = 1;
            foreach ($userStats as $stats) {
                $rankShift = 0;
                if (isset($oldranks[$stats->userId])) {
                    if ($rank < $oldranks[$stats->userId][4]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->userId][4]) {
                        $rankShift = 2;
                    }
                }

                $stats->rankExp = $rank;
                $stats->rankShiftExp = $rankShift;
                $rank++;
            }
        }

        // Statistiktabelle leeren
        $this->userStatRepository->truncate();
        // Save points in memory cached table
        $this->userStatRepository->addEntries($userStats);

        // Save points to user points table
        $this->userPointsRepository->addEntries($userStats, $time);

        // Update boost bonus
        if ($this->config->getBoolean('boost_system_enable') && $max_points_building > 0) {
            $max_prod = $this->config->getFloat('boost_system_max_res_prod_bonus');
            $max_build = $this->config->getFloat('boost_system_max_building_speed_bonus');
            foreach ($points_arr as $userId => $userPoints) {
                $this->userRepository->updateUserBoost($userId, ($max_prod * ($max_points - $userPoints) / $max_points), ($max_build * ($max_points - $userPoints) / $max_points));
            }
        } else {
            $this->userRepository->resetBoost();
        }

        unset($user_rank_highest);
        unset($oldranks);

        // Allianz Statistik generieren
        $this->allianceStatsRepository->deleteAll();

        // Technologien laden
        $technologies = $this->allianceTechnologyRepository->findAll();
        $technologyPoints = [];
        foreach ($technologies as $technology) {
            $level = 1;
            $points = 0;
            $baseCost = $technology->getCosts()->getSum();
            while ($level <= $technology->lastLevel) {
                $points += $baseCost * $technology->buildFactor ** ($level - 1) / $this->config->param1Int('points_update');
                $technologyPoints[$technology->id][$level] = $points;
                $level++;
            }
        }

        // Gebäude laden
        $buildings = $this->allianceBuildingRepository->findAll();
        $buildingPoints = array();
        foreach ($buildings as $building) {
            $level = 1;
            $points = 0;
            $baseCosts = $building->getCosts()->getSum();
            while ($level <= $building->lastLevel) {
                $points += $baseCosts * $building->buildFactor ** ($level - 1) / $this->config->param1Int('points_update');
                $buildingPoints[$building->id][$level] = $points;
                $level++;
            }
        }

        $usedAllianceShipPoints = $this->userRepository->getUsedAllianceShipPoints();

        $alliances = $this->allianceRepository->getAllianceStats();
        /** @var AllianceStats[] $allianceStats */
        $allianceStats = [];
        foreach ($alliances as $alliance) {
            $allianceId = (int) $alliance['alliance_id'];
            $upoints = 0;
            $bpoints = 0;
            $tpoints = 0;
            if ($alliance['upoints'] > 0 && $this->config->param2Int('points_update') > 0) {
                $upoints = floor((int) $alliance['upoints'] / $this->config->param2Int('points_update'));
            }

            $buildingLevels = $this->allianceBuildingRepository->getLevels($allianceId);
            foreach ($buildingLevels as $buildingId => $level) {
                $bpoints += $buildingPoints[$buildingId][$level];
            }

            $technologyLevels = $this->allianceTechnologyRepository->getLevels($allianceId);
            foreach ($technologyLevels as $technologyId => $level) {
                $tpoints += $technologyPoints[$technologyId][$level];
            }

            $apoints = $tpoints + $bpoints + ($usedAllianceShipPoints[$allianceId] ?? 0);
            $points = $apoints + $upoints;

            $stats = AllianceStats::createFromData(
                $allianceId,
                $alliance['alliance_tag'],
                $alliance['alliance_name'],
                (int) $alliance['cnt'],
                (int) $points,
                (int) $upoints,
                (int) $apoints,
                (int) $tpoints,
                (int) $bpoints,
                (int) $alliance['uavg'],
                (int) $alliance['cnt'],
                (int) $alliance['alliance_rank_current']
            );
            $allianceStats[] = $stats;
        }

        usort($allianceStats, fn (AllianceStats $a, AllianceStats $b) => $b->points <=> $a->points);
        if (count($allianceStats) > 0) {
            $rank = 1;
            foreach ($allianceStats as $stats) {
                $stats->currentRank = $rank;
                $this->allianceStatsRepository->add($stats);
                $this->allianceRepository->updatePointsAndRank($stats->allianceId, $stats->points, $stats->currentRank, $stats->lastRank);
                $rank++;
            }
        }

        unset($buildingPoints);
        unset($technologyPoints);

        // Zeit in Config speichern
        $this->runtimeDataStore->set('statsupdate', (string) time());

        return new RankingCalculationResult(count($users), $allpoints);
    }

    public function calcBuildingPoints(): int
    {
        $buildings = $this->buildingDataRepository->getBuildings();
        $this->buildingPointRepository->deleteAll();

        foreach ($buildings as $building) {
            $this->buildingPointRepository->add($building->getId(), $this->calculatePointsForBuilding($building));
        }

        return count($buildings);
    }

    /**
     * @return array<int, float>
     */
    private function calculatePointsForBuilding(Building $building)
    {
        $points = [];
        for ($level = 1; $level <= $building->getLastLevel(); $level++) {
            $r = $building->getCostsMetal()
                + $building->getCostsCrystal()
                + $building->getCostsFuel()
                + $building->getCostsPlastic()
                + $building->getCostsFood();
            $p = ($r * (1 - $building->getBuildCostsFactor() ** $level)
                / (1 - $building->getBuildCostsFactor()))
                / $this->config->param1Int('points_update');

            $points[$level] = $p;
        }

        return $points;
    }

    public function calcTechPoints(): int
    {
        $technologies = $this->technologyDataRepository->getTechnologies();
        $this->technologyPointRepository->deleteAll();

        foreach ($technologies as $technology) {
            $this->technologyPointRepository->add($technology->getId(), $this->calculatePointsForTechnology($technology));
        }

        return count($technologies);
    }

    /**
     * @return array<int, float>
     */
    private function calculatePointsForTechnology(Technology $technology): array
    {
        $points = [];
        for ($level = 1; $level <= $technology->getLastLevel(); $level++) {
            $r = $technology->getCostsMetal()
                + $technology->getCostsCrystal()
                + $technology->getCostsFuel()
                + $technology->getCostsPlastic()
                + $technology->getCostsFood();
            $p = ($r * (1 - $technology->getBuildCostsFactor() ** $level)
                / (1 - $technology->getBuildCostsFactor()))
                / $this->config->param1Int('points_update');

            $points[$level] = $p;
        }

        return $points;
    }

    public function calcShipPoints(): int
    {
        $ships = $this->shipDataRepository->getAllShips(true);
        foreach ($ships as $ship) {
            $this->shipDataRepository->updateShipPoints($ship->getId(), $this->calculatePointsForShip($ship));
        }

        return count($ships);
    }

    private function calculatePointsForShip(Ship $ship): float
    {
        return ($ship->getCostsMetal()
            + $ship->getCostsCrystal()
            + $ship->getCostsFuel()
            + $ship->getCostsPlastic()
            + $ship->getCostsFood())
            / $this->config->param1Int('points_update');
    }

    public function calcDefensePoints(): int
    {
        $defenses = $this->defenseDataRepository->getAllDefenses();
        foreach ($defenses as $defense) {
            $this->defenseDataRepository->updateDefensePoints($defense->getId(), $this->calculatePointsForDefense($defense));
        }

        return count($defenses);
    }

    private function calculatePointsForDefense(Defense $defense): float
    {
        return ($defense->getCostsMetal()
            + $defense->getCostsCrystal()
            + $defense->getCostsFuel()
            + $defense->getCostsPlastic()
            + $defense->getCostsFood())
            / $this->config->param1Int('points_update');
    }
}
