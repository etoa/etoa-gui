<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceStats;
use EtoA\Alliance\AllianceStatsRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Building\Building;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\Defense;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\Ship;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Technology\Technology;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
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
    private BuildingRepository $buildingRepository;
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

    public function __construct(
        ConfigurationService $config,
        RuntimeDataStore $runtimeDataStore,
        AllianceRepository $allianceRepository,
        AllianceStatsRepository $allianceStatsRepository,
        PlanetRepository $planetRepository,
        BuildingRepository $buildingRepository,
        BuildingDataRepository $buildingDataRepository,
        BuildingPointRepository $buildingPointRepository,
        TechnologyRepository $technologyRepository,
        TechnologyDataRepository $technologyDataRepository,
        TechnologyPointRepository $technologyPointRepository,
        ShipRepository $shipRepository,
        ShipDataRepository $shipDataRepository,
        FleetRepository $fleetRepository,
        DefenseRepository $defenseRepository,
        DefenseDataRepository $defenseDataRepository,
        RaceDataRepository $raceRepository,
        UserStatRepository $userStatRepository,
        UserRepository $userRepository,
        EntityRepository $entityRepository
    ) {
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

        $alliance = $this->allianceRepository->getAllianceTags();

        // Load 'old' ranks
        $res = dbquery("
                SELECT
                    id,
                    rank,
                    rank_ships,
                    rank_tech,
                    rank_buildings,
                    rank_exp
                FROM
                    user_stats;
            ");
        $oldranks = array();
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_row($res)) {
                $oldranks[$arr[0]][0] = $arr[1];
                $oldranks[$arr[0]][1] = $arr[2];
                $oldranks[$arr[0]][2] = $arr[3];
                $oldranks[$arr[0]][3] = $arr[4];
                $oldranks[$arr[0]][4] = $arr[5];
            }
        }

        // Statistiktabelle leeren
        $this->userStatRepository->truncate();

        $user_stats_query = "";
        $user_points_query = "";
        $user_rank_highest = [];
        $max_points_building = 0;
        $points_building_arr = [];
        $max_points = 0;
        $points_arr = [];

        $includeUsersInHolidas = $this->config->getBoolean('show_hmod_users_stats');
        $users = $this->userRepository->searchUsers(UserSearch::create()
            ->notGhost()
            ->inHolidays($includeUsersInHolidas ? null : false));
        foreach ($users as $user) {
            // first 24hours no highest rank calculation
            if (time() > (3600 * 24 + $this->config->param1Int("enable_login"))) {
                $user_rank_highest[$user->id] = $user->rankHighest > 0 ? $user->rankHighest : 9999;
            } else {
                $user_rank_highest[$user->id] = 0;
            }

            $points = 0.0;
            $points_ships = 0.0;
            $points_tech = 0;
            $points_building = 0;
            $sx = 0;
            $sy = 0;

            $planets = $this->planetRepository->getUserPlanets($user->id);
            foreach ($planets as $planet) {
                if ($planet->mainPlanet) {
                    $entity = $this->entityRepository->findIncludeCell($planet->id);
                    $sx = $entity->sx;
                    $sy = $entity->sy;

                    break;
                }
            }

            $shipListItems = $this->shipRepository->findForUser($user->id);
            foreach ($shipListItems as $shipListItem) {
                $p = ($shipListItem->bunkered + $shipListItem->count) * $shipPoints[$shipListItem->shipId];
                $points += $p;
                $points_ships += $p;
            }

            $fleets = $this->fleetRepository->findByParameters(FleetSearchParameters::create()->userId($user->id));
            foreach ($fleets as $fleet) {
                foreach ($this->fleetRepository->findAllShipsInFleet($fleet->id) as $shipEntry) {
                    $p = $shipEntry->count * $shipPoints[$shipEntry->shipId];
                    $points += $p;
                    $points_ships += $p;
                }
            }

            $defenseListItems = $this->defenseRepository->findForUser($user->id);
            foreach ($defenseListItems as $defenseListItem) {
                $p = round($defenseListItem->count * $defensePoints[$defenseListItem->defenseId]);
                $points += $p;
                $points_building += $p;
            }

            foreach ($planets as $planet) {
                $buildingLevels = $this->buildingRepository->getBuildingLevels($planet->id);
                foreach ($buildingLevels as $buildingId => $level) {
                    $p = round($buildingPoints[$buildingId][$level]);
                    $points += $p;
                    $points_building += $p;
                }
            }

            $techList = $this->technologyRepository->getTechnologyLevels($user->id);
            foreach ($techList as $technologyId => $level) {
                $p = round($techPoints[$technologyId][$level]);
                $points += $p;
                $points_tech += $p;
            }

            $points_exp = max(0, $this->shipRepository->getSpecialShipExperienceSumForUser($user->id));
            $points_exp += max(0, $this->fleetRepository->getSpecialShipExperienceSumForUser($user->id));

            // Save part of insert query
            $user_stats_query .= ",(
                        " . $user->id . ",
                        " . $points . ",
                        " . $points_ships . ",
                        " . $points_tech . ",
                        " . $points_building . ",
                        " . $points_exp . ",
                        '" . $user->nick . "',
                        '" . ($user->allianceId > 0 ? $alliance[$user->allianceId] : '') . "',
                        '" . $user->allianceId . "',
                        '" . ($user->raceId > 0 ? $race[$user->raceId] : '') . "',
                        '" . $sx . "',
                        '" . $sy . "',
                        '" . ($user->blockedTo > $time ? 1 : 0) . "',
                        '" . ($user->logoutTime < $time - $inactiveTime ? 1 : 0) . "',
                        '" . ($user->hmodFrom > 0 ? 1 : 0) . "'
                    )";
            $user_points_query .= ",(
                        '" . $user->id . "',
                        '" . time() . "',
                        '" . $points . "',
                        '" . $points_ships . "',
                        '" . $points_tech . "',
                        '" . $points_building . "'
                    )";

            $allpoints += $points;

            $max_points_building = max($max_points_building, $points_building);
            $points_building_arr[$user->id] = $points_building;
        }

        // Save points in memory cached table
        if ($user_stats_query != "") {
            dbquery("
                    INSERT INTO
                        user_stats
                    (
                        id,
                        points,
                        points_ships,
                        points_tech,
                        points_buildings,
                        points_exp,
                        nick,
                        alliance_tag,
                        alliance_id,
                        race_name,
                        sx,
                        sy,
                        blocked,
                        inactive,
                        hmod
                    )
                    VALUES
                        " . substr($user_stats_query, 1) . "
                    ;
                ");
        }

        // Save points to user points table
        if ($user_points_query != "") {
            dbquery("
                    INSERT INTO
                    user_points
                    (
                        point_user_id,
                        point_timestamp,
                        point_points,
                        point_ship_points,
                        point_tech_points,
                        point_building_points
                    )
                    VALUES
                        " . substr($user_points_query, 1) . "
                ");
        }

        // Ranking (Total Points)
        $res = dbquery("
            SELECT
                id,
                points
            FROM
                user_stats
            ORDER BY
                points DESC;
            ");
        $cnt = 1;
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_row($res)) {
                $rs = 0;
                if (isset($oldranks[$arr[0]])) {
                    if ($cnt < $oldranks[$arr[0]][0]) {
                        $rs = 1;
                    } elseif ($cnt > $oldranks[$arr[0]][0]) {
                        $rs = 2;
                    }
                }
                dbquery("
                    UPDATE
                        user_stats
                    SET
                        rank=" . $cnt . ",
                        rankshift=" . $rs . "
                    WHERE
                        id=" . $arr[0] . ";");
                dbquery("
                    UPDATE
                        users
                    SET
                        user_rank=" . $cnt . ",
                        user_points=" . $arr[1] . ",
                        user_rank_highest=" . min($cnt, $user_rank_highest[$arr[0]]) . "
                    WHERE
                        user_id=" . $arr[0] . "
                    ");

                $max_points = max($max_points, $arr[1]);
                $points_arr[$arr[0]] = $arr[1];

                $cnt++;
            }
        }

        // Update boost bonus
        if ($this->config->getBoolean('boost_system_enable') && $max_points_building > 0) {
            $max_prod = $this->config->getFloat('boost_system_max_res_prod_bonus');
            $max_build = $this->config->getFloat('boost_system_max_building_speed_bonus');
            foreach ($points_arr as $uid => $up) {
                dbquery("
                        UPDATE
                            users
                        SET
                            boost_bonus_production=" . ($max_prod * ($max_points - $up) / $max_points) . ",
                            boost_bonus_building=" . ($max_build * ($max_points - $up) / $max_points) . "
                        WHERE
                            user_id=" . $uid . ";");
            }
        } else {
            dbquery("
                    UPDATE
                        users
                    SET
                        boost_bonus_production=0,
                        boost_bonus_building=0;");
        }

        unset($user_rank_highest);

        // Ranking (Ships)
        $res = dbquery("
            SELECT
                id
            FROM
                user_stats
            ORDER BY
                points_ships DESC;
            ");
        $cnt = 1;
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_row($res)) {
                $rs = 0;
                if (isset($oldranks[$arr[0]])) {
                    if ($cnt < $oldranks[$arr[0]][1]) {
                        $rs = 1;
                    } elseif ($cnt > $oldranks[$arr[0]][1]) {
                        $rs = 2;
                    }
                }
                dbquery("
                    UPDATE
                        user_stats
                    SET
                        rank_ships=" . $cnt . ",
                        rankshift_ships=" . $rs . "
                    WHERE
                        id=" . $arr[0] . ";");
                $cnt++;
            }
        }

        // Ranking (Tech)
        $res = dbquery("
            SELECT
                id
            FROM
                user_stats
            ORDER BY
                points_tech DESC;
            ");
        $cnt = 1;
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_row($res)) {
                $rs = 0;
                if (isset($oldranks[$arr[0]])) {
                    if ($cnt < $oldranks[$arr[0]][2]) {
                        $rs = 1;
                    } elseif ($cnt > $oldranks[$arr[0]][2]) {
                        $rs = 2;
                    }
                }
                dbquery("
                    UPDATE
                        user_stats
                    SET
                        rank_tech=" . $cnt . ",
                        rankshift_tech=" . $rs . "
                    WHERE
                        id=" . $arr[0] . ";");
                $cnt++;
            }
        }

        // Ranking (Buildings)
        $res = dbquery("
            SELECT
                id
            FROM
                user_stats
            ORDER BY
                points_buildings DESC;
            ");
        $cnt = 1;
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_row($res)) {
                $rs = 0;
                if (isset($oldranks[$arr[0]])) {
                    if ($cnt < $oldranks[$arr[0]][3]) {
                        $rs = 1;
                    } elseif ($cnt > $oldranks[$arr[0]][3]) {
                        $rs = 2;
                    }
                }
                dbquery("
                    UPDATE
                        user_stats
                    SET
                        rank_buildings=" . $cnt . ",
                        rankshift_buildings=" . $rs . "
                    WHERE
                        id=" . $arr[0] . ";");
                $cnt++;
            }
        }

        // Ranking (Exp)
        $res = dbquery("
            SELECT
                id
            FROM
                user_stats
            ORDER BY
                points_exp DESC;
            ");
        $cnt = 1;
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_row($res)) {
                $rs = 0;
                if (isset($oldranks[$arr[0]])) {
                    if ($cnt < $oldranks[$arr[0]][4]) {
                        $rs = 1;
                    } elseif ($cnt > $oldranks[$arr[0]][4]) {
                        $rs = 2;
                    }
                }
                dbquery("
                    UPDATE
                        user_stats
                    SET
                        rank_exp=" . $cnt . ",
                        rankshift_exp=" . $rs . "
                    WHERE
                        id=" . $arr[0] . ";");
                $cnt++;
            }
        }
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

        $res = dbquery("SELECT
                a.alliance_tag,
                a.alliance_name,
                a.alliance_id,
                a.alliance_rank_current,
                COUNT(*) AS cnt,
                SUM(u.points) AS upoints,
                AVG(u.points) AS uavg
            FROM
                alliances as a
            INNER JOIN
                user_stats as u
            ON
                u.alliance_id=a.alliance_id
            GROUP BY
                a.alliance_id
            ORDER BY
                SUM(u.points) DESC
            ;");

        /** @var AllianceStats[] $allianceStats */
        $allianceStats = [];
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                $upoints = 0;
                $bpoints = 0;
                $tpoints = 0;
                if ($arr['upoints'] > 0 && $this->config->param2Int('points_update') > 0) {
                    $upoints = floor($arr['upoints'] / $this->config->param2Int('points_update'));
                }

                $bres = dbquery("SELECT
                                     alliance_buildlist_building_id,
                                    alliance_buildlist_current_level
                                FROM
                                    alliance_buildlist
                                WHERE
                                    alliance_buildlist_alliance_id='" . $arr['alliance_id'] . "'
                                    AND alliance_buildlist_current_level>0;");
                if (mysql_num_rows($bres) > 0) {
                    while ($barr = mysql_fetch_row($bres)) {
                        $bpoints += $buildingPoints[$barr[0]][$barr[1]];
                    }
                }

                $tres = dbquery("SELECT
                                     alliance_techlist_tech_id,
                                    alliance_techlist_current_level
                                FROM
                                    alliance_techlist
                                WHERE
                                    alliance_techlist_alliance_id='" . $arr['alliance_id'] . "'
                                    AND alliance_techlist_current_level>0;");
                if (mysql_num_rows($tres) > 0) {
                    while ($tarr = mysql_fetch_row($tres)) {
                        $tpoints += $technologyPoints[$tarr[0]][$tarr[1]];
                    }
                }

                $sres = dbquery("SELECT
                                      SUM(`user_alliace_shippoints_used`)
                                FROM
                                    users
                                WHERE
                                    user_alliance_id='" . $arr['alliance_id'] . "'
                                GROUP BY
                                    user_alliance_id
                                LIMIT 1;");
                $sarr = mysql_fetch_row($sres);

                $apoints = $tpoints + $bpoints + $sarr[0];
                $points = $apoints + $upoints;

                $stats = AllianceStats::createFromData(
                    (int) $arr['alliance_id'],
                    $arr['alliance_tag'],
                    $arr['alliance_name'],
                    (int) $arr['cnt'],
                    (int) $points,
                    (int) $upoints,
                    (int) $apoints,
                    (int) $tpoints,
                    (int) $bpoints,
                    (int) $arr['uavg'],
                    (int) $arr['cnt'],
                    (int) $arr['alliance_rank_current']
                );
                $allianceStats[] = $stats;
            }
        }

        usort($allianceStats, fn (AllianceStats $a, AllianceStats $b) => $b->points <=> $a->points);
        if (count($allianceStats) > 0) {
            $rank = 1;
            foreach ($allianceStats as $stats) {
                $stats->currentRank = $rank;
                $this->allianceStatsRepository->add($stats);
                dbquery("UPDATE
                                alliances
                            SET
                                alliance_points='" . $stats->points . "',
                                alliance_rank_current='" . $stats->currentRank . "',
                                alliance_rank_last='" . $stats->lastRank . "'
                            WHERE
                                alliance_id='" . $stats->allianceId . "';");
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
            $this->buildingPointRepository->add($building->id, $this->calculatePointsForBuilding($building));
        }

        return count($buildings);
    }

    /**
     * @return array<int, float>
     */
    private function calculatePointsForBuilding(Building $building)
    {
        $points = [];
        for ($level = 1; $level <= $building->lastLevel; $level++) {
            $r = $building->costsMetal
                + $building->costsCrystal
                + $building->costsFuel
                + $building->costsPlastic
                + $building->costsFood;
            $p = ($r * (1 - $building->buildCostsFactor ** $level)
                / (1 - $building->buildCostsFactor))
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
            $this->technologyPointRepository->add($technology->id, $this->calculatePointsForTechnology($technology));
        }

        return count($technologies);
    }

    /**
     * @return array<int, float>
     */
    private function calculatePointsForTechnology(Technology $technology): array
    {
        $points = [];
        for ($level = 1; $level <= $technology->lastLevel; $level++) {
            $r = $technology->costsMetal
                + $technology->costsCrystal
                + $technology->costsFuel
                + $technology->costsPlastic
                + $technology->costsFood;
            $p = ($r * (1 - $technology->buildCostsFactor ** $level)
                / (1 - $technology->buildCostsFactor))
                / $this->config->param1Int('points_update');

            $points[$level] = $p;
        }

        return $points;
    }

    public function calcShipPoints(): int
    {
        $ships = $this->shipDataRepository->getAllShips(true);
        foreach ($ships as $ship) {
            $this->shipDataRepository->updateShipPoints($ship->id, $this->calculatePointsForShip($ship));
        }

        return count($ships);
    }

    private function calculatePointsForShip(Ship $ship): float
    {
        return ($ship->costsMetal
            + $ship->costsCrystal
            + $ship->costsFuel
            + $ship->costsPlastic
            + $ship->costsFood)
            / $this->config->param1Int('points_update');
    }

    public function calcDefensePoints(): int
    {
        $defenses = $this->defenseDataRepository->getAllDefenses();
        foreach ($defenses as $defense) {
            $this->defenseDataRepository->updateDefensePoints($defense->id, $this->calculatePointsForDefense($defense));
        }

        return count($defenses);
    }

    private function calculatePointsForDefense(Defense $defense): float
    {
        return ($defense->costsMetal
            + $defense->costsCrystal
            + $defense->costsFuel
            + $defense->costsPlastic
            + $defense->costsFood)
            / $this->config->param1Int('points_update');
    }
}
