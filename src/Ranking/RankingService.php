<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceStats;
use EtoA\Alliance\AllianceStatsRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Cell\Cell;
use EtoA\Universe\Cell\CellRepository;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRatingSearch;
use EtoA\User\UserRatingSort;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserSort;
use EtoA\User\UserStatRepository;
use EtoA\User\UserStatSearch;

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
    private BuildingDataRepository $buildingDataRepository;
    private BuildingPointRepository $buildingPointRepository;
    private TechnologyRepository $technologyRepository;
    private TechnologyDataRepository $technologyDataRepository;
    private TechnologyPointRepository $technologyPointRepository;
    private ShipDataRepository $shipRepository;
    private DefenseDataRepository $defenseRepository;
    private RaceDataRepository $raceRepository;
    private UserStatRepository $userStatRepository;
    private UserRepository $userRepository;
    private UserRatingRepository $userRatingRepository;
    private CellRepository $cellRepository;

    public function __construct(
        ConfigurationService $config,
        RuntimeDataStore $runtimeDataStore,
        AllianceRepository $allianceRepository,
        AllianceStatsRepository $allianceStatsRepository,
        BuildingDataRepository $buildingDataRepository,
        BuildingPointRepository $buildingPointRepository,
        TechnologyRepository $technologyRepository,
        TechnologyDataRepository $technologyDataRepository,
        TechnologyPointRepository $technologyPointRepository,
        ShipDataRepository $shipRepository,
        DefenseDataRepository $defenseRepository,
        RaceDataRepository $raceRepository,
        UserStatRepository $userStatRepository,
        UserRepository $userRepository,
        UserRatingRepository $userRatingRepository,
        CellRepository $cellRepository
    ) {
        $this->config = $config;
        $this->runtimeDataStore = $runtimeDataStore;
        $this->allianceRepository = $allianceRepository;
        $this->allianceStatsRepository = $allianceStatsRepository;
        $this->buildingDataRepository = $buildingDataRepository;
        $this->buildingPointRepository = $buildingPointRepository;
        $this->technologyRepository = $technologyRepository;
        $this->technologyDataRepository = $technologyDataRepository;
        $this->technologyPointRepository = $technologyPointRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
        $this->raceRepository = $raceRepository;
        $this->userStatRepository = $userStatRepository;
        $this->userRepository = $userRepository;
        $this->userRatingRepository = $userRatingRepository;
        $this->cellRepository = $cellRepository;
    }

    public function getTitles(bool $admin = false): string
    {
        ob_start();

        $img_dir = ($admin == 1)
            ? "../images"
            : "images";

        tableStart("Allgemeine Titel");
        $cnt = 0;

        $titles = [
            [
                'search' => UserStatSearch::points(),
                'medal_image' => $img_dir . '/medals/medal_total.png',
                'rank_title' => $this->config->get('userrank_total'),
            ],
            [
                'search' => UserStatSearch::ships(),
                'medal_image' => $img_dir . '/medals/medal_fleet.png',
                'rank_title' => $this->config->get('userrank_fleet'),
            ],
            [
                'search' => UserStatSearch::technologies(),
                'medal_image' => $img_dir . '/medals/medal_tech.png',
                'rank_title' => $this->config->get('userrank_tech'),
            ],
            [
                'search' => UserStatSearch::buildings(),
                'medal_image' => $img_dir . '/medals/medal_buildings.png',
                'rank_title' => $this->config->get('userrank_buildings'),
            ],
            [
                'search' => UserStatSearch::exp(),
                'medal_image' => $img_dir . '/medals/medal_exp.png',
                'rank_title' => $this->config->get('userrank_exp'),
            ],
        ];
        foreach ($titles as $title) {
            $stats = $this->userStatRepository->searchStats($title['search'], null, 1);
            if (count($stats) > 0) {
                $stat = $stats[0];
                if ($stat->points > 0) {
                    $profile = ($admin == 1)
                        ? "?page=user&amp;sub=edit&amp;user_id=" . $stat->id . ""
                        : "?page=userinfo&amp;id=" . $stat->id;
                    echo "<tr>
                        <th class=\"tbltitle\" style=\"width:100px;height:100px;\">
                            <img src='" . $title['medal_image'] . "' alt=\"medal\" style=\"height:100px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:400px;\">
                            " . $title['rank_title'] . "
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $stat->nick . "</span><br/><br/>
                            " . nf($stat->points) . " Punkte<br/><br/>";
                    echo "[<a href=\"" . $profile . "\">Profil</a>]";
                    echo "</td>
                    </tr>";
                    $cnt++;
                }
            }
        }

        $titles2 = [
            [
                'results' => $this->userRatingRepository->getBattleRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_battle.png',
                'rank_title' => $this->config->get('userrank_battle'),
            ],
            [
                'results' => $this->userRatingRepository->getTradeRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_trade.png',
                'rank_title' => $this->config->get('userrank_trade'),
            ],
            [
                'results' => $this->userRatingRepository->getDiplomacyRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_diplomacy.png',
                'rank_title' => $this->config->get('userrank_diplomacy'),
            ],
        ];
        foreach ($titles2 as $title) {
            if (count($title['results']) > 0) {
                $rating = $title['results'][0];
                if ($rating->rating > 0) {
                    $profile = ($admin == 1)
                        ? "?page=user&amp;sub=edit&amp;user_id=" . $rating->userId . ""
                        : "?page=userinfo&amp;id=" . $rating->userId;
                    echo "<tr>
                        <th class=\"tbltitle\" style=\"width:100px;height:100px;\">
                            <img src='" . $title['medal_image'] . "' style=\"height:100px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:400px;\">
                            " . $title['rank_title'] . "
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $rating->userNick . "</span><br/><br/>
                            " . nf($rating->rating) . " Punkte<br/><br/>";
                    echo "[<a href=\"" . $profile . "\">Profil</a>]";
                    echo "</td>
                    </tr>";
                    $cnt++;
                }
            }
        }

        if ($cnt == 0) {
            echo "<tr><td class=\"tbldata\">Keine Titel vorhanden (kein Spieler hat die minimale Punktzahl zum Erwerb eines Titels erreicht)!</td></tr>";
        }

        tableEnd();
        tableStart("Rassenleader");
        $races = $this->raceRepository->getActiveRaces();
        foreach ($races as $race) {
            $users = $this->userRepository->searchUsers(
                UserSearch::create()
                    ->raceId($race->id)
                    ->notGhost()
                    ->hasPoints(),
                UserSort::points('desc'),
                1
            );
            if (count($users) > 0) {
                $user = array_values($users)[0];
                $profile = ($admin == 1)
                    ? "?page=user&amp;sub=edit&amp;user_id=" . $user->id . ""
                    : "?page=userinfo&amp;id=" . $user->id;
                echo "<tr>
                        <th class=\"tbltitle\" style=\"width:70px;height:70px;\">
                            <img src='" . $img_dir . "/medals/medal_race.png' style=\"height:70px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
                            <div style=\"font-size:16pt;\">" . $race->leaderTitle . "</div>
                            " . $this->raceRepository->getNumberOfUsersWithRace($race->id) . " V&ouml;lker
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $user->nick . "</span><br/><br/>
                            " . nf($user->points) . " Punkte &nbsp;&nbsp;&nbsp;";
                echo "[<a href=\"" . $profile . "\">Profil</a>]";
                echo "</td>
                    </tr>";
            }
        }
        tableEnd();

        $rtn = ob_get_contents();
        ob_end_clean();

        return $rtn;
    }

    /**
     * Writes generated titles to cache files
     */
    public function calcTitles(): void
    {
        $dir = CACHE_ROOT . "/out";
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        file_put_contents($this->getUserTitlesCacheFilePath(), $this->getTitles());
        file_put_contents($this->getUserTitlesAdminCacheFilePath(), $this->getTitles(true));
    }

    public function getUserTitlesCacheFilePath(): string
    {
        return CACHE_ROOT . "/out/usertitles.html";
    }

    public function getUserTitlesAdminCacheFilePath(): string
    {
        return CACHE_ROOT . "/out/usertitles_a.html";
    }

    public function calc(): RankingCalculationResult
    {
        $time = time();
        $inactiveTime = 86400 * USER_INACTIVE_SHOW;
        $allpoints = 0;

        $ship = $this->shipRepository->getShipPoints();
        $def = $this->defenseRepository->getDefensePoints();

        if (!$this->buildingPointRepository->areCalculated()) {
            $this->calcBuildingPoints();
        }
        $building = $this->buildingPointRepository->getAllMap();

        if (!$this->technologyPointRepository->areCalculated()) {
            $this->calcTechPoints();
        }
        $techPoints = $this->technologyPointRepository->getAllMap();

        /** @var Cell[] $cells */
        $cells = [];
        foreach ($this->cellRepository->findAllCoordinates() as $cell) {
            $cells[$cell->id] = $cell;
        }

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

            // Zelle des Hauptplaneten
            $res = dbquery("
                    SELECT
                        cell_id
                    FROM
                        entities
                    INNER JOIN
                        planets
                    ON
                        planets.id=entities.id
                        AND planets.planet_user_main=1
                        AND planets.planet_user_id='" . $user->id . "';
                ");
            if (mysql_num_rows($res)) {
                $arr = mysql_fetch_row($res);
                $sx = $cells[$arr[0]]->sx;
                $sy = $cells[$arr[0]]->sy;
            }

            // Punkte für Schiffe (aus Planeten)
            $res = dbquery("
                    SELECT
                        shiplist_ship_id,
                        shiplist_count,
                        shiplist_bunkered
                    FROM
                        shiplist
                    WHERE
                        shiplist_user_id='" . $user->id . "';
                ");
            while ($arr = mysql_fetch_assoc($res)) {
                $p = ($arr['shiplist_bunkered'] + $arr['shiplist_count']) * $ship[$arr['shiplist_ship_id']];
                $points += $p;
                $points_ships += $p;
            }

            //
            // Punkte für Schiffe (in Flotten)
            $res = dbquery("
                    SELECT
                        fs.fs_ship_id,
                        fs.fs_ship_cnt
                    FROM
                        fleet AS f
                    INNER JOIN
                        fleet_ships AS fs
                        ON f.id = fs.fs_fleet_id
                        AND fs.fs_ship_faked='0'
                        AND f.user_id='" . $user->id . "'
                ;");
            while ($arr = mysql_fetch_assoc($res)) {
                $p = $arr['fs_ship_cnt'] * $ship[$arr['fs_ship_id']];
                $points += $p;
                $points_ships += $p;
            }

            // Punkte für Verteidigung
            $res = dbquery("
                    SELECT
                        deflist_count,
                        deflist_def_id
                    FROM
                        deflist
                    WHERE
                        deflist_user_id='" . $user->id . "';
                ");
            while ($arr = mysql_fetch_assoc($res)) {
                $p = round($arr['deflist_count'] * $def[$arr['deflist_def_id']]);
                $points += $p;
                $points_building += $p;
            }

            // Punkte für Gebäude
            $res = dbquery("
                    SELECT
                        buildlist_current_level,
                        buildlist_building_id
                    FROM
                        buildlist
                    WHERE
                        buildlist_user_id='" . $user->id . "';
                ");
            if (mysql_num_rows($res) > 0) {
                while ($arr = mysql_fetch_assoc($res)) {
                    if ($arr['buildlist_current_level'] > 0) {
                        $p = round($building[$arr['buildlist_building_id']][$arr['buildlist_current_level']]);
                        $points += $p;
                        $points_building += $p;
                    }
                }
            }

            // Punkte für Forschung
            $techList = $this->technologyRepository->getTechnologyLevels($user->id);
            foreach ($techList as $technologyId => $level) {
                $p = round($techPoints[$technologyId][$level]);
                $points += $p;
                $points_tech += $p;
            }

            // Punkte für XP
            $res = dbquery("
                    SELECT
                        SUM(shiplist_special_ship_exp)
                    FROM
                        shiplist
                    WHERE
                        shiplist_user_id='" . $user->id . "'
                        AND shiplist_count=1;
                ");
            $arr = mysql_fetch_row($res);
            $points_exp = max(0, $arr[0]);

            $res = dbquery("
                    SELECT
                        SUM(fs_special_ship_exp)
                    FROM
                        fleet_ships
                    INNER JOIN
                        fleet
                    ON
                        fleet.id=fleet_ships.fs_fleet_id
                    AND
                        fleet.user_id='" . $user->id . "'
                    AND
                        fleet_ships.fs_ship_cnt='1'
                ");
            $arr = mysql_fetch_row($res);
            $points_exp += max(0, $arr[0]);

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

        // Array Löschen (Speicher freigeben)
        unset($ship);
        unset($def);
        unset($building);
        unset($techPoints);
        unset($p);
        unset($points);
        unset($points_ships);
        unset($points_tech);
        unset($points_building);
        unset($user_stats_query);
        unset($user_points_query);

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
        $res = dbquery("
                SELECT
                    alliance_tech_id,
                    alliance_tech_costs_factor,
                    alliance_tech_last_level,
                    (alliance_tech_costs_metal+alliance_tech_costs_crystal+alliance_tech_costs_plastic+alliance_tech_costs_fuel+alliance_tech_costs_food) as costs
                FROM
                    alliance_technologies;
            ");
        $techs = array();
        $level = 1;
        while ($arr = mysql_fetch_row($res)) {
            $level = 1;
            $points = 0;
            while ($level <= $arr[2]) {
                $points += $arr[3] * $arr[1] ** ($level - 1) / $this->config->param1Int('points_update');
                $techs[$arr[0]][$level] = $points;
                $level++;
            }
        }

        // Gebäude laden
        $res = dbquery("
                SELECT
                    alliance_building_id,
                    alliance_building_costs_factor,
                    alliance_building_last_level,
                    (alliance_building_costs_metal+alliance_building_costs_crystal+alliance_building_costs_plastic+alliance_building_costs_fuel+alliance_building_costs_food) as costs
                FROM
                    alliance_buildings;
            ");
        $buildings = array();
        while ($arr = mysql_fetch_row($res)) {
            $level = 1;
            $points = 0;
            while ($level <= $arr[2]) {
                $points += $arr[3] * $arr[1] ** ($level - 1) / $this->config->param1Int('points_update');
                $buildings[$arr[0]][$level] = $points;
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
                        $bpoints += $buildings[$barr[0]][$barr[1]];
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
                        $tpoints += $techs[$tarr[0]][$tarr[1]];
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

        unset($buildings);
        unset($techs);

        // Zeit in Config speichern
        $this->runtimeDataStore->set('statsupdate', (string) time());

        return new RankingCalculationResult(count($users), $allpoints);
    }

    public function calcBuildingPoints(): string
    {
        $buildings = $this->buildingDataRepository->getBuildings();
        $this->buildingPointRepository->deleteAll();

        foreach ($buildings as $building) {
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

            $this->buildingPointRepository->add($building->id, $points);
        }

        return sprintf("Die Geb&auml;udepunkte von %s Geb&auml;uden wurden aktualisiert!", count($buildings));
    }

    public function calcTechPoints(): string
    {
        $technologies = $this->technologyDataRepository->getTechnologies();
        $this->technologyPointRepository->deleteAll();

        if (count($technologies) > 0) {
            foreach ($technologies as $technology) {
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

                $this->technologyPointRepository->add($technology->id, $points);
            }
        }

        return sprintf("Die Punkte von %s Technologien wurden aktualisiert!", count($technologies));
    }

    public function calcShipPoints(): string
    {
        $res = dbquery("
          SELECT
            ship_id,
            ship_costs_metal,
            ship_costs_crystal,
            ship_costs_fuel,
            ship_costs_plastic,
            ship_costs_food
          FROM
            ships;");
        $mnr = mysql_num_rows($res);
        if ($mnr > 0) {
            while ($arr = mysql_fetch_array($res)) {
                $p = ($arr['ship_costs_metal']
                    + $arr['ship_costs_crystal']
                    + $arr['ship_costs_fuel']
                    + $arr['ship_costs_plastic']
                    + $arr['ship_costs_food'])
                    / $this->config->param1Int('points_update');
                dbquery("
              UPDATE
                ships
              SET
                ship_points=" . $p . "
              WHERE
                ship_id=" . $arr['ship_id'] . ";");
            }
        }

        return "Die Punkte von $mnr Schiffen wurden aktualisiert!";
    }

    public function calcDefensePoints(): string
    {
        $res = dbquery("
          SELECT
            def_id,
            def_costs_metal,
            def_costs_crystal,
            def_costs_fuel,
            def_costs_plastic,
            def_costs_food
          FROM
            defense;");
        $mnr = mysql_num_rows($res);
        if ($mnr > 0) {
            while ($arr = mysql_fetch_array($res)) {
                $p = ($arr['def_costs_metal'] +
                    $arr['def_costs_crystal']
                    + $arr['def_costs_fuel']
                    + $arr['def_costs_plastic']
                    + $arr['def_costs_food'])
                    / $this->config->param1Int('points_update');
                dbquery("UPDATE
              defense
               SET
                def_points=$p
              WHERE
                def_id=" . $arr['def_id'] . ";");
            }
        }

        return "Die Punkte von $mnr Verteidigungsanlagen wurden aktualisiert!";
    }
}
