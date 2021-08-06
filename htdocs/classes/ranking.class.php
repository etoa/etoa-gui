<?PHP

use EtoA\Alliance\AlliancePointsRepository;
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

/**
 * Provides static functions for
 * calculating and displaying
 * player ranking
 */
class Ranking
{

    /**
     * Shows player tytles
     */
    private static function getTitles($admin = 0, $extern = 0)
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        ob_start();

        $img_dir = ($admin == 1) ? "../images" : "images";

        $titles = array(
            "total" => "",
            "fleet" => "_ships",
            "tech" => "_tech",
            "buildings" => "_buildings",
            "exp" => "_exp"
        );

        $titles2 = array('battle', 'trade', 'diplomacy');

        tableStart("Allgemeine Titel");
        $cnt = 0;
        foreach ($titles as $k => $v) {
            $res = dbquery("
                SELECT
                    nick,
                    points" . $v . ",
                    id
                FROM
                    user_stats
                WHERE
                    rank" . $v . ">0
                    AND points" . $v . ">0
                ORDER BY
                    rank" . $v . " ASC
                LIMIT 1;");
            if (mysql_num_rows($res) > 0) {
                $arr = mysql_fetch_row($res);
                $profile = ($admin == 1) ? "?page=user&amp;sub=edit&amp;user_id=" . $arr[2] . "" : "?page=userinfo&amp;id=" . $arr[2];
                echo "<tr>
                        <th class=\"tbltitle\" style=\"width:100px;height:100px;\">
                            <img src='" . $img_dir . "/medals/medal_" . $k . ".png' alt=\"medal\" style=\"height:100px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:400px;\">
                            " . $config->get('userrank_' . $k) . "
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $arr[0] . "</span><br/><br/>
                            " . nf($arr[1]) . " Punkte<br/><br/>";
                if (!$extern) {
                    echo "[<a href=\"" . $profile . "\">Profil</a>]";
                }
                echo "</td>
                    </tr>";
            }
            $cnt++;
        }
        foreach ($titles2 as $v) {
            $res = dbquery("
                SELECT
                    user_nick,
                    " . $v . "_rating,
                    user_id
                FROM
                    users
                INNER JOIN
                    user_ratings
                ON user_id=id
                AND
                    " . $v . "_rating>0
                AND user_ghost=0
                ORDER BY
                    " . $v . "_rating DESC
                LIMIT 1;");
            if (mysql_num_rows($res) > 0) {
                $arr = mysql_fetch_row($res);
                $profile = ($admin == 1) ? "?page=user&amp;sub=edit&amp;user_id=" . $arr[2] . "" : "?page=userinfo&amp;id=" . $arr[2];
                echo "<tr>
                        <th class=\"tbltitle\" style=\"width:100px;height:100px;\">
                            <img src='" . $img_dir . "/medals/medal_" . $v . ".png' style=\"height:100px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:400px;\">
                            " . $config->get('userrank_' . $v) . "
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $arr[0] . "</span><br/><br/>
                            " . nf($arr[1]) . " Punkte<br/><br/>";
                if (!$extern) {
                    echo "[<a href=\"" . $profile . "\">Profil</a>]";
                }
                echo "</td>
                    </tr>";
            }
            $cnt++;
        }
        if ($cnt == 0) {
            echo "<tr><td class=\"tbldata\">Keine Titel vorhanden (kein Spieler hat die minimale Punktzahl zum Erwerb eines Titels erreicht)!</td></tr>";
        }
        tableEnd();
        tableStart("Rassenleader");
        $rres = dbquery("
            SELECT
                race_id,
                race_leadertitle,
                race_name
            FROM
                races
            ORDER BY
                race_name;
            ");
        while ($rarr = mysql_fetch_assoc($rres)) {
            $res = dbquery("
                SELECT
                    user_nick,
                    user_points,
                    user_id
                FROM
                    users
                WHERE
                    user_race_id=" . $rarr['race_id'] . "
                    AND user_ghost=0
                ORDER BY
                    user_points DESC
                LIMIT 1;
                ");
            if (mysql_num_rows($res) > 0) {
                $arr = mysql_fetch_row($res);
                $cres = dbquery("SELECT COUNT(user_race_id) FROM users WHERE user_race_id=" . $rarr['race_id'] . "");
                $carr = mysql_fetch_row($cres);
                $profile = ($admin == 1) ? "?page=user&amp;sub=edit&amp;user_id=" . $arr[2] . "" : "?page=userinfo&amp;id=" . $arr[2];

                echo "<tr>
                        <th class=\"tbltitle\" style=\"width:70px;height:70px;\">
                            <img src='" . $img_dir . "/medals/medal_race.png' style=\"height:70px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
                            <div style=\"font-size:16pt;\">" . $rarr['race_leadertitle'] . "</div>
                            " . $carr[0] . " V&ouml;lker
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $arr[0] . "</span><br/><br/>
                            " . nf($arr[1]) . " Punkte &nbsp;&nbsp;&nbsp;";
                if (!$extern) {
                    echo "[<a href=\"" . $profile . "\">Profil</a>]";
                }
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
    static function calcTitles()
    {
        $dir = CACHE_ROOT . "/out";
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $file_u = $dir . "/usertitles.gen";
        $file_a = $dir . "/usertitles_a.gen";
        $file_ex = $dir . "/usertitles_ex.gen";
        $titles_u = Ranking::getTitles();
        $titles_a = Ranking::getTitles(1);
        $titles_ex = Ranking::getTitles(0, 1);
        if ($d = fopen($file_u, "w+")) {
            fwrite($d, $titles_u);
            fclose($d);
        }
        if ($d = fopen($file_a, "w+")) {
            fwrite($d, $titles_a);
            fclose($d);
        }
        if ($d = fopen($file_ex, "w+")) {
            fwrite($d, $titles_ex);
            fclose($d);
        }
    }

    /**
     * Punkteberechnung
     */
    static function calc($manual = false)
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        $time = time();
        $inactivetime = 86400 * USER_INACTIVE_SHOW;
        $allpoints = 0;
        $res_amount_per_point = $config->param1Int('points_update');

        // Schiffe laden
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $ship = $shipRepository->getShipPoints();

        // Verteidigung laden
        /** @var DefenseDataRepository $defenseRepository */
        $defenseRepository = $app[DefenseDataRepository::class];
        $def = $defenseRepository->getDefensePoints();

        // Gebäudepunkte berechnen falls nocht nicht vorhanden
        /** @var BuildingPointRepository $buildingPointRepository */
        $buildingPointRepository = $app[BuildingPointRepository::class];
        if (!$buildingPointRepository->areCalculated()) {
            self::calcBuildingPoints();
        }

        // Gebäude laden
        $building = $buildingPointRepository->getAllMap();

        /** @var TechnologyPointRepository $technologyPointRepository */
        $technologyPointRepository = $app[TechnologyPointRepository::class];

        // Technologiepunkte berechnen falls nocht nicht vorhanden
        if (!$technologyPointRepository->areCalculated()) {
            self::calcTechPoints();
        }

        // Technologien laden
        $techPoints = $technologyPointRepository->getAllMap();

        // Cells laden
        $res = dbquery("
                SELECT
                    id,
                    sx,
                    sy
                FROM
                    cells;
            ");
        $cells = array();
        while ($arr = mysql_fetch_row($res)) {
            $cells[$arr[0]][0] = $arr[1];
            $cells[$arr[0]][1] = $arr[2];
        }

        // Rassen laden
        /** @var RaceDataRepository $raceRepository */
        $raceRepository = $app[RaceDataRepository::class];
        $race = $raceRepository->getRaceNames();

        // Allianzen laden
        /** @var AllianceRepository $allianceRepository */
        $allianceRepository = $app[AllianceRepository::class];
        $alliance = $allianceRepository->getAllianceTags();
        /** @var AllianceStatsRepository $allianceStatsRepository */
        $allianceStatsRepository = $app[AllianceStatsRepository::class];
        /** @var AlliancePointsRepository $alliancePointsRepository */
        $alliancePointsRepository = $app[AlliancePointsRepository::class];

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
        dbquery("TRUNCATE TABLE user_stats;");

        // User-ID's laden
        $ures =    dbquery("
                SELECT
                    user_id,
                    user_nick,
                    user_race_id,
                    user_alliance_id,
                    user_rank_highest,
                    user_blocked_to,
                    user_hmode_from,
                    user_logouttime
                FROM
                    users
                WHERE
                    user_ghost=0" .
            // same check as below to set `user_stats`.`hmod` field
            ($config->getBoolean('show_hmod_users_stats') ? '' : ' AND (user_hmode_from = 0)') .
            ';');

        $user_stats_query = "";
        $user_points_query = "";
        $user_rank_highest = array();
        $max_points_building = 0;
        $points_building_arr = array();
        $max_points = 0;
        $points_arr = array();

        while ($uarr = mysql_fetch_assoc($ures)) {
            $user_id = $uarr['user_id'];

            // first 24hours no highest rank calculation
            if (time() > (3600 * 24 + $config->param1Int("enable_login"))) {
                $user_rank_highest[$user_id] = $uarr['user_rank_highest'] > 0 ? $uarr['user_rank_highest'] : 9999;
            } else {
                $user_rank_highest[$user_id] = 0;
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
                        AND planets.planet_user_id='" . $user_id . "';
                ");
            if (mysql_num_rows($res)) {
                $arr = mysql_fetch_row($res);
                $sx = $cells[$arr[0]][0];
                $sy = $cells[$arr[0]][1];
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
                        shiplist_user_id='" . $user_id . "';
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
                        AND f.user_id='" . $user_id . "'
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
                        deflist_user_id='" . $user_id . "';
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
                        buildlist_user_id='" . $user_id . "';
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
            /** @var TechnologyRepository $technologyRepository */
            $technologyRepository = $app[TechnologyRepository::class];
            $techList = $technologyRepository->getTechnologyLevels($user_id);
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
                        shiplist_user_id='" . $user_id . "'
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
                        fleet.user_id='" . $user_id . "'
                    AND
                        fleet_ships.fs_ship_cnt='1'
                ");
            $arr = mysql_fetch_row($res);
            $points_exp += max(0, $arr[0]);

            // Save part of insert query
            $user_stats_query .= ",(
                        " . $user_id . ",
                        " . $points . ",
                        " . $points_ships . ",
                        " . $points_tech . ",
                        " . $points_building . ",
                        " . $points_exp . ",
                        '" . $uarr['user_nick'] . "',
                        '" . ($uarr['user_alliance_id'] > 0 ? $alliance[$uarr['user_alliance_id']] : '') . "',
                        '" . $uarr['user_alliance_id'] . "',
                        '" . ($uarr['user_race_id'] > 0 ? $race[$uarr['user_race_id']] : '') . "',
                        '" . $sx . "',
                        '" . $sy . "',
                        '" . ($uarr['user_blocked_to'] > $time ? 1 : 0) . "',
                        '" . ($uarr['user_logouttime'] < $time - $inactivetime ? 1 : 0) . "',
                        '" . ($uarr['user_hmode_from'] > 0 ? 1 : 0) . "'
                    )";
            $user_points_query .= ",(
                        '" . $user_id . "',
                        '" . time() . "',
                        '" . $points . "',
                        '" . $points_ships . "',
                        '" . $points_tech . "',
                        '" . $points_building . "'
                    )";

            $allpoints += $points;

            $max_points_building = max($max_points_building, $points_building);
            $points_building_arr[$user_id] = $points_building;
        }
        unset($user_id);

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
                    if ($cnt < $oldranks[$arr[0]][0])
                        $rs = 1;
                    elseif ($cnt > $oldranks[$arr[0]][0])
                        $rs = 2;
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
        if ($config->getBoolean('boost_system_enable') && $max_points_building > 0) {
            $max_prod = $config->getFloat('boost_system_max_res_prod_bonus');
            $max_build = $config->getFloat('boost_system_max_building_speed_bonus');
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
                    if ($cnt < $oldranks[$arr[0]][1])
                        $rs = 1;
                    elseif ($cnt > $oldranks[$arr[0]][1])
                        $rs = 2;
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
                    if ($cnt < $oldranks[$arr[0]][2])
                        $rs = 1;
                    elseif ($cnt > $oldranks[$arr[0]][2])
                        $rs = 2;
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
                    if ($cnt < $oldranks[$arr[0]][3])
                        $rs = 1;
                    elseif ($cnt > $oldranks[$arr[0]][3])
                        $rs = 2;
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
                    if ($cnt < $oldranks[$arr[0]][4])
                        $rs = 1;
                    elseif ($cnt > $oldranks[$arr[0]][4])
                        $rs = 2;
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
        $allianceStatsRepository->deleteAll();

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
                $points += $arr[3] * pow($arr[1], $level - 1) / $config->param1Int('points_update');
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
                $points += $arr[3] * pow($arr[1], $level - 1) / $config->param1Int('points_update');
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
                if ($arr['upoints'] > 0 && $config->param2Int('points_update') > 0) {
                    $upoints = floor($arr['upoints'] / $config->param2Int('points_update'));
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
                    $arr['alliance_id'],
                    $arr['alliance_tag'],
                    $arr['alliance_name'],
                    (int) $arr['cnt'],
                    $points,
                    $upoints,
                    $apoints,
                    $tpoints,
                    $bpoints,
                    $arr['uavg'],
                    $arr['cnt'],
                    $arr['alliance_rank_current']
                );
                $allianceStats[] = $stats;
            }
        }

        usort($allianceStats, fn (AllianceStats $a, AllianceStats $b) => $b->points <=> $a->points);
        if (count($allianceStats) > 0) {
            $rank = 1;
            foreach ($allianceStats as $stats) {
                $stats->currentRank = $rank;
                $allianceStatsRepository->add($stats);
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

        /** @var RuntimeDataStore */
        $runtimeDataStore = $app[RuntimeDataStore::class];

        // Zeit in Config speichern
        $runtimeDataStore->set('statsupdate', (string) time());

        $num = mysql_num_rows($ures);

        // Arrays löschen (Speicher freigeben)
        mysql_free_result($res);
        unset($arr);

        return array($num, $allpoints);
    }

    static function createUserBanner()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        $dir = USERBANNER_DIR;
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $createdFiles = array();

        $res = dbquery("
            SELECT
                u.user_nick,
                a.alliance_tag,
                a.alliance_name,
                r.race_name,
                u.user_points,
                u.user_id,
                u.admin,
                u.user_ghost,
                u.user_rank
            FROM
                users u
            LEFT JOIN
                alliances a
                ON u.user_alliance_id=a.alliance_id
            LEFT JOIN
                races r
                On u.user_race_id=r.race_id
            ");
        while ($arr = mysql_fetch_assoc($res)) {
            if ($arr['admin'] == 1) {
                $pt = "  -  Game-Admin";
            } elseif ($arr['user_ghost'] == 1) {
                $pt = "";
            } else {
                $pt = "  -  " . nf($arr['user_points']) . " Punkte, Platz " . $arr['user_rank'] . "";
            }
            $text = $config->get('roundname') . $pt;

            $im = self::createUserBannerImage(
                USERBANNER_WIDTH,
                USERBANNER_HEIGTH,
                USERBANNER_BACKGROUND_IMAGE,
                USERBANNER_FONT,
                $arr['user_nick'],
                $arr['alliance_tag'],
                $arr['alliance_name'],
                $arr['race_name'],
                $text
            );

            $file = self::getUserBannerPath($arr['user_id']);
            if (file_exists($file)) {
                unlink($file);
            }
            imagepng($im, $file);
            chmod($file, 0777);
            imagedestroy($im);
            $createdFiles[] = $file;
        }

        // Remove old banner images
        $dh = opendir($dir);
        while ($f = readdir($dh)) {
            $fp = $dir . '/' . $f;
            if (is_file($fp) && !in_array($fp, $createdFiles, true)) {
                unlink($fp);
            }
        }
        closedir($dh);
    }

    static function getUserBannerPath($userId)
    {
        return USERBANNER_DIR . '/' . md5('user' . $userId) . '.png';
    }

    static function createUserBannerImage($w, $h, $backgroundImage, $font, $nick, $allianceTag, $allianceName, $race, $text)
    {
        $im = imagecreatefrompng($backgroundImage);

        $colBlack = imagecolorallocate($im, 0, 0, 0);
        $colGrey = imagecolorallocate($im, 120, 120, 120);
        $colYellow = imagecolorallocate($im, 255, 255, 0);
        $colOrange = imagecolorallocate($im, 255, 100, 0);
        $colWhite = imagecolorallocate($im, 255, 255, 255);
        $colGreen = imagecolorallocate($im, 0, 255, 0);
        $colBlue = imagecolorallocate($im, 150, 150, 240);
        $colViolett = imagecolorallocate($im, 200, 0, 200);
        $colRe = imagecolorallocate($im, 200, 0, 200);

        $nsize = imagettfbbox(16, 0, $font, $nick);

        // Nick
        imagettftext($im, 16, 0, 6, 21, $colBlack, $font, $nick);
        imagettftext($im, 16, 0, 5, 20, $colWhite, $font, $nick);

        // Race
        imagettftext($im, 11, 0, $nsize[2] - $nsize[0] + 16, 21, $colBlack, $font, $race);
        imagettftext($im, 11, 0, $nsize[2] - $nsize[0] + 15, 20, $colWhite, $font, $race);

        // Alliance
        if ($allianceTag) {
            imagettftext($im, 9, 0, 9, 39, $colBlack, $font, "<" . $allianceTag . "> " . $allianceName);
            imagettftext($im, 9, 0, 8, 38, $colWhite, $font, "<" . $allianceTag . "> " . $allianceName);
        }

        // Text
        if ($text) {
            imagettftext($im, 9, 0, 9, 54, $colBlack, $font, $text);
            imagettftext($im, 9, 0, 8, 53, $colWhite, $font, $text);
        }

        return $im;
    }

    static function calcBuildingPoints()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        /** @var BuildingDataRepository $buildingRepository */
        $buildingRepository = $app[BuildingDataRepository::class];
        /** @var BuildingPointRepository $buildingPointRepository */
        $buildingPointRepository = $app[BuildingPointRepository::class];

        $buildings = $buildingRepository->getBuildings();
        $buildingPointRepository->deleteAll();

        foreach ($buildings as $building) {
            $points = [];
            for ($level = 1; $level <= $building->lastLevel; $level++) {
                $r = $building->costsMetal
                    + $building->costsCrystal
                    + $building->costsFuel
                    + $building->costsPlastic
                    + $building->costsFood;
                $p = ($r * (1 - pow($building->buildCostsFactor, $level))
                    / (1 - $building->buildCostsFactor))
                    / $config->param1Int('points_update');

                $points[$level] = $p;
            }

            $buildingPointRepository->add($building->id, $points);
        }

        return sprintf("Die Geb&auml;udepunkte von %s Geb&auml;uden wurden aktualisiert!", count($buildings));
    }

    static function calcTechPoints()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];
        /** @var TechnologyDataRepository $technologyRepository */
        $technologyRepository = $app[TechnologyDataRepository::class];
        /** @var TechnologyPointRepository $technologyPointRepository */
        $technologyPointRepository = $app[TechnologyPointRepository::class];

        $technologies = $technologyRepository->getTechnologies();
        $technologyPointRepository->deleteAll();

        if (count($technologies) > 0) {
            foreach ($technologies as $technology) {
                $points = [];
                for ($level = 1; $level <= $technology->lastLevel; $level++) {
                    $r = $technology->costsMetal
                        + $technology->costsCrystal
                        + $technology->costsFuel
                        + $technology->costsPlastic
                        + $technology->costsFood;
                    $p = ($r * (1 - pow($technology->buildCostsFactor, $level))
                        / (1 - $technology->buildCostsFactor))
                        / $config->param1Int('points_update');

                    $points[$level] = $p;
                }

                $technologyPointRepository->add($technology->id, $points);
            }
        }

        return sprintf("Die Punkte von %s Technologien wurden aktualisiert!", count($technologies));
    }

    static function calcShipPoints()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

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
                    / $config->param1Int('points_update');
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

    static function calcDefensePoints()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

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
                    / $config->param1Int('points_update');
                dbquery("UPDATE
              defense
               SET
                def_points=$p
              WHERE
                def_id=" . $arr['def_id'] . ";");
            }
        }
        return "Die Battlepoints von $mnr Verteidigungsanlagen wurden aktualisiert!";
    }
}
