<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceStatsRepository;
use EtoA\Alliance\AllianceTechnologyListRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\AllianceStats;
use EtoA\Entity\Building;
use EtoA\Entity\BuildingPoint;
use EtoA\Entity\Defense;
use EtoA\Entity\Ship;
use EtoA\Entity\Technology;
use EtoA\Entity\TechnologyPoint;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Fleet\FleetShipRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyListItemRepository;
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
    public function __construct(
        private readonly ConfigurationService $config,
        private readonly RuntimeDataStore $runtimeDataStore,
        private readonly AllianceRepository $allianceRepository,
        private readonly AllianceStatsRepository $allianceStatsRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingRepository,
        private readonly BuildingDataRepository $buildingDataRepository,
        private readonly BuildingPointRepository $buildingPointRepository,
        private readonly TechnologyListItemRepository $technologyRepository,
        private readonly TechnologyDataRepository $technologyDataRepository,
        private readonly TechnologyPointRepository $technologyPointRepository,
       private readonly ShipDataRepository $shipDataRepository,
        private readonly FleetRepository $fleetRepository,
        private readonly DefenseRepository $defenseRepository,
        private readonly DefenseDataRepository $defenseDataRepository,
        private readonly RaceDataRepository $raceRepository,
        private readonly UserStatRepository $userStatRepository,
        private readonly UserRepository $userRepository,
        private readonly EntityRepository $entityRepository,
        private readonly AllianceBuildingRepository $allianceBuildingRepository,
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly AllianceTechnologyRepository $allianceTechnologyRepository,
        private readonly UserPointsRepository $userPointsRepository,
        private readonly ShipListRepository $shipListRepository,
        private readonly FleetShipRepository $fleetShipRepository,
        private readonly AllianceTechnologyListRepository $allianceTechnologyListRepository
    )
    {}

    //TODO: refactor
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
            $oldranks[(int) $userRanks['id']][1] = (int) $userRanks['shipPoints'];
            $oldranks[(int) $userRanks['id']][2] = (int) $userRanks['techPoints'];
            $oldranks[(int) $userRanks['id']][3] = (int) $userRanks['buildingPoints'];
            $oldranks[(int) $userRanks['id']][4] = (int) $userRanks['expPoints'];
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
                    $entity = $this->entityRepository->findIncludeCell($planet->getEntity());
                    $sx = $entity->getCell()->getSx();
                    $sy = $entity->getCell()->getSy();

                    break;
                }
            }

            $shipListItems = $this->shipListRepository->findForUser($user);
            foreach ($shipListItems as $shipListItem) {
                $p = ($shipListItem->getBunkered() + $shipListItem->getCount()) * $shipPoints[$shipListItem->getShip()->getId()];
                $points += $p;
                $points_ships += $p;
            }

            $fleets = $this->fleetRepository->findByParameters(FleetSearchParameters::create()->user($user));
            foreach ($fleets as $fleet) {
                foreach ($this->fleetShipRepository->findAllShipsInFleet($fleet) as $shipEntry) {
                    $p = $shipEntry->getCount() * $shipPoints[$shipEntry->getShip()->getId()];
                    $points += $p;
                    $points_ships += $p;
                }
            }

            $defenseListItems = $this->defenseRepository->findForUser($user);
            foreach ($defenseListItems as $defenseListItem) {
                $p = round($defenseListItem->getCount() * $defensePoints[$defenseListItem->getDefense()->getId()]);
                $points += $p;
                $points_building += $p;
            }

            foreach ($planets as $planet) {
                $buildingLevels = $this->buildingRepository->getBuildingLevels($planet);
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

            $points_exp = max(0, $this->shipListRepository->getSpecialShipExperienceSumForUser($user));
            $points_exp += max(0, $this->fleetShipRepository->getSpecialShipExperienceSumForUser($user));

            $userStats[] = UserStatistic::createFromCalculation(
                $user,
                $user->getBlockedTo() > $time,
                $user->getHmodFrom() > 0,
                $user->getLogoutTime() < $time - $inactiveTime,
                $user->getAlliance(),
                $user->getAlliance()?$user->getAlliance()->getTag():'',
                $user->getRace()?$user->getRace()->getName():'',
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
                if (isset($oldranks[$stats->user->getId()])) {
                    if ($rank < $oldranks[$stats->user->getId()][0]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->user->getId()][0]) {
                        $rankShift = 2;
                    }
                }

                $stats->rank = $rank;
                $stats->rankShift = $rankShift;
                $rank++;

                $this->userRepository->updatePointsAndRank($stats, $user_rank_highest[$stats->user->getId()]);

                $max_points = max($max_points, $stats->points);
                $points_arr[$stats->user->getId()] = $stats->points;
            }
        }

        // Calculate ship rank shift
        usort($userStats, fn (UserStatistic $a, UserStatistic $b) => $b->shipPoints <=> $a->shipPoints);
        if (count($userStats) > 0) {
            $rank = 1;
            foreach ($userStats as $stats) {
                $rankShift = 0;
                if (isset($oldranks[$stats->user->getId()])) {
                    if ($rank < $oldranks[$stats->user->getId()][1]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->user->getId()][1]) {
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
                if (isset($oldranks[$stats->user->getId()])) {
                    if ($rank < $oldranks[$stats->user->getId()][2]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->user->getId()][2]) {
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
                if (isset($oldranks[$stats->user->getId()])) {
                    if ($rank < $oldranks[$stats->user->getId()][3]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->user->getId()][3]) {
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
                if (isset($oldranks[$stats->user->getId()])) {
                    if ($rank < $oldranks[$stats->user->getId()][4]) {
                        $rankShift = 1;
                    } elseif ($rank > $oldranks[$stats->user->getId()][4]) {
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
            while ($level <= $technology->getLastLevel()) {
                $points += $baseCost * $technology->getBuildFactor() ** ($level - 1) / $this->config->param1Int('points_update');
                $technologyPoints[$technology->getId()][$level] = $points;
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
            while ($level <= $building->getlastLevel()) {
                $points += $baseCosts * $building->getBuildFactor() ** ($level - 1) / $this->config->param1Int('points_update');
                $buildingPoints[$building->getId()][$level] = $points;
                $level++;
            }
        }

        $usedAllianceShipPoints = $this->userRepository->getUsedAllianceShipPoints();

        $alliances = $this->allianceRepository->getAllianceStats();
        /** @var AllianceStats[] $allianceStats */
        $allianceStats = [];
        foreach ($alliances as $alliance) {
            $allianceId = (int) $alliance['id'];
            $upoints = 0;
            $bpoints = 0;
            $tpoints = 0;
            if ($alliance['upoints'] > 0 && $this->config->param2Int('points_update') > 0) {
                $upoints = floor((int) $alliance['upoints'] / $this->config->param2Int('points_update'));
            }

            $buildingLevels = $this->allianceBuildListRepository->getLevels($allianceId);
            foreach ($buildingLevels as $buildingId => $level) {
                $bpoints += $buildingPoints[$buildingId][$level];
            }

            $technologyLevels = $this->allianceTechnologyListRepository->getLevels($allianceId);
            foreach ($technologyLevels as $technologyId => $level) {
                $tpoints += $technologyPoints[$technologyId][$level];
            }

            $apoints = $tpoints + $bpoints + ($usedAllianceShipPoints[$allianceId] ?? 0);
            $points = $apoints + $upoints;

            $stats = new AllianceStats();
            $stats->setAlliance($this->allianceRepository->find($allianceId));
            $stats->setAllianceTag($alliance['tag']);
            $stats->setAllianceName($alliance['name']);
            $stats->setCount($alliance['cnt']);
            $stats->setPoints((int)$points);
            $stats->setShipPoints((int)($usedAllianceShipPoints[$allianceId] ?? 0));
            $stats->setUserPoints((int)$upoints);
            $stats->setAlliancePoints((int)$apoints);
            $stats->setTechnologyPoints((int)$tpoints);
            $stats->setBuildingPoints((int)$bpoints);
            $stats->setUserAverage((int)$alliance['uavg']);
            $stats->setCount($alliance['cnt']);
            $stats->setCurrentRank($alliance['currentRank']);

            $allianceStats[] = $stats;
        }

        usort($allianceStats, fn (AllianceStats $a, AllianceStats $b) => $b->getPoints() <=> $a->getPoints());
        if (count($allianceStats) > 0) {
            $rank = 1;
            foreach ($allianceStats as $stats) {
                $stats->setCurrentRank($rank);
                $this->allianceStatsRepository->add($stats);
                $this->allianceRepository->updatePointsAndRank($stats->getAlliance(), $stats->getPoints(), $stats->getCurrentRank(), $stats->getLastRank());
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
            $this->calculatePointsForBuilding($building);
        }

        return count($buildings);
    }

    /**
     * @return array<int, float>
     */
    private function calculatePointsForBuilding(Building $building): void
    {
        for ($level = $building->getLastLevel(); $level > 0; $level--) {
            $r = $building->getCostsMetal()
                + $building->getCostsCrystal()
                + $building->getCostsFuel()
                + $building->getCostsPlastic()
                + $building->getCostsFood();
            $p = ($r * (1 - $building->getBuildCostsFactor() ** $level)
                / (1 - $building->getBuildCostsFactor()))
                / $this->config->param1Int('points_update');

            $points = new BuildingPoint();
            $points->setPoints($p);
            $points->setLevel($level);
            $building->addPoint($points);
        }

        $this->buildingRepository->save();
    }

    public function calcTechPoints(): int
    {
        $technologies = $this->technologyDataRepository->getTechnologies();
        $this->technologyPointRepository->deleteAll();

        foreach ($technologies as $technology) {
            $this->calculatePointsForTechnology($technology);
        }

        return count($technologies);
    }

    /**
     * @return array<int, float>
     */
    private function calculatePointsForTechnology(Technology $technology): void
    {
        for ($level = $technology->getLastLevel(); $level > 0; $level--) {
            $r = $technology->getCostsMetal()
                + $technology->getCostsCrystal()
                + $technology->getCostsFuel()
                + $technology->getCostsPlastic()
                + $technology->getCostsFood();
            $p = ($r * (1 - $technology->getBuildCostsFactor() ** $level)
                / (1 - $technology->getBuildCostsFactor()))
                / $this->config->param1Int('points_update');

            $points = new TechnologyPoint();
            $points->setPoints($p);
            $points->setLevel($level);
            $technology->addPoint($points);
        }

        $this->technologyRepository->save();
    }

    public function calcShipPoints(): int
    {
        $ships = $this->shipDataRepository->getAllShips(true);
        foreach ($ships as $ship) {
            $ship->setPoints($this->calculatePointsForShip($ship));
        }

        $this->shipDataRepository->save();

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
            $defense->setPoints($this->calculatePointsForDefense($defense));
        }

        $this->defenseDataRepository->save();

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
