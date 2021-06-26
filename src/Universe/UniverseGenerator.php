<?PHP

declare(strict_types=1);

namespace EtoA\Universe;

use DirectoryIterator;
use EtoA\Core\Configuration\ConfigurationService;
use Mutex;
use UserToXml;

/**
 * @todo Create and use repositories for planets, stars etc.
 */
class UniverseGenerator
{
    private ConfigurationService $config;

    /**
     * @var array<int>
     */
    private array $sol_types = [];

    /**
     * @var array<int>
     */
    private array $planet_types = [];

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;

        $this->init();
    }

    private function init(): void
    {
        $res = dbquery("
            SELECT
                  sol_type_id
            FROM
                sol_types
            WHERE
                sol_type_consider=1;");
        while ($arr = mysql_fetch_array($res)) {
            $this->sol_types[] = $arr['sol_type_id'];
        }

        $res = dbquery("
            SELECT
            type_id
            FROM
                planet_types
            WHERE
                type_consider=1;");
        while ($arr = mysql_fetch_array($res)) {
            $this->planet_types[] = $arr['type_id'];
        }
    }

    /**
     * Create the universe.
     * And there was light!
     */
    public function create($mapImage = "", $mapPrecision = 95): void
    {
        $mtx = new Mutex();
        $mtx->acquire();

        $mapPrecision = max(0, $mapPrecision);
        $mapPrecision = min($mapPrecision, 100);

        echo "Lade Schöpfungs-Einstellungen...!<br>";
        $sx_num = $this->config->param1Int('num_of_sectors');
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cx_num = $this->config->param1Int('num_of_cells');
        $cy_num = $this->config->param2Int('num_of_cells');
        $perc_solsys = $this->config->getInt('space_percent_solsys');
        $perc_asteroids = $this->config->getInt('space_percent_asteroids');
        $perc_nebulas = $this->config->getInt('space_percent_nebulas');
        $perc_wormholes = $this->config->getInt('space_percent_wormholes');

        $planet_count = 0;
        $sol_count = 0;
        $nebula_count = 0;
        $asteroids_count = 0;
        $wormhole_count = 0;

        echo "Erstelle Universum mit " . $sx_num * $sy_num . " Sektoren à " . $cx_num * $cy_num . " Zellen, d.h. " . $sx_num * $sy_num * $cx_num * $cy_num . " Zellen total<br>";

        $type = array();

        //
        // Set cell types
        //

        // by image
        $imgpath = "../images/galaxylayouts/" . $mapImage;
        if ($mapImage != "" && is_file($imgpath)) {
            $im = imagecreatefrompng($imgpath);
            $w = imagesx($im);
            $h = imagesy($im);

            echo "Bildvorlage gefunden, verwende diese: <img src=\"" . $imgpath . "\" /><br/>";

            for ($x = 1; $x <= $w; $x++) {
                for ($y = 1; $y <= $h; $y++) {
                    $o = imagecolorat($im, $x - 1, $h - $y);
                    $pr = mt_rand(0, 100);

                    if (($o > 0 && $pr <= $mapPrecision) || ($o == 0 && $pr >= $mapPrecision)) {
                        $ct = mt_rand(1, 100);

                        if ($ct <= $perc_solsys) {
                            $type[$x][$y] = 's';
                        } elseif ($ct <= $perc_solsys + $perc_asteroids) {
                            $type[$x][$y] = 'a';
                        } elseif ($ct <= $perc_solsys + $perc_asteroids + $perc_nebulas) {
                            $type[$x][$y] = 'n';
                        } elseif ($ct <= $perc_solsys + $perc_asteroids + $perc_nebulas + $perc_wormholes) {
                            $type[$x][$y] = 'w';
                        } else {
                            $type[$x][$y] = 'e';
                        }
                    } else {
                        $type[$x][$y] = 'e';
                    }
                }
            }
        }
        // by randomizer with config values
        else {
            for ($x = 1; $x <= ($sx_num * $cx_num); $x++) {
                for ($y = 1; $y <= ($sy_num * $cy_num); $y++) {
                    $ct = mt_rand(1, 100);
                    if ($ct <= $perc_solsys) {
                        $type[$x][$y] = 's';
                    } elseif ($ct <= $perc_solsys + $perc_asteroids) {
                        $type[$x][$y] = 'a';
                    } elseif ($ct <= $perc_solsys + $perc_asteroids + $perc_nebulas) {
                        $type[$x][$y] = 'n';
                    } elseif ($ct <= $perc_solsys + $perc_asteroids + $perc_nebulas + $perc_wormholes) {
                        $type[$x][$y] = 'w';
                    } else {
                        $type[$x][$y] = 'e';
                    }
                }
            }
        }

        // Save cell info
        $sql = "";
        for ($sx = 1; $sx <= $sx_num; $sx++) {
            for ($sy = 1; $sy <= $sy_num; $sy++) {
                for ($cx = 1; $cx <= $cx_num; $cx++) {
                    for ($cy = 1; $cy <= $cy_num; $cy++) {
                        // Save cell
                        if ($sql == "") {
                            $sql .= "(
                                    '" . $sx . "',
                                    '" . $sy . "',
                                    '" . $cx . "',
                                    '" . $cy . "'
                                )";
                        } else {
                            $sql .= ",(
                                    '" . $sx . "',
                                    '" . $sy . "',
                                    '" . $cx . "',
                                    '" . $cy . "'
                                )";
                        }
                    }
                }
            }
        }
        echo "Zellen geneiert, speichere sie...<br/>";
        dbquery("
                INSERT INTO
                    cells
                (
                    sx,
                    sy,
                    cx,
                    cy
                )
                VALUES " . $sql);

        echo "Zellen gespeichert, fülle Objekte rein...<br/>";
        $res = dbquery("
            SELECT
                id,
                sx,
                sy,
                cx,
                cy
            FROM
                cells;");
        while ($arr = mysql_fetch_row($res)) {
            $cell_id = $arr[0];
            $x = (($arr[1] - 1) * 10) + $arr[3];
            $y = (($arr[2] - 1) * 10) + $arr[4];

            // Star system
            if ($type[$x][$y] == 's') {
                $this->createStarSystem($cell_id);
                $sol_count++;
            }

            // Asteroid Fields
            elseif ($type[$x][$y] == 'a') {
                $this->createAsteroids($cell_id);
                $asteroids_count++;
            }

            // Nebulas
            elseif ($type[$x][$y] == 'n') {
                $this->createNebula($cell_id);
                $nebula_count++;
            }

            // Wormholes
            elseif ($type[$x][$y] == 'w') {
                $this->createWormhole($cell_id);
                $wormhole_count++;
            }

            // Empty space
            else {
                $this->createEmptySpace($cell_id);
            }
        }
        echo "Universum erstellt, prüfe Wurmlöcher...<br/>";

        // Delete one wormhole if total count is odd
        // Replace it with empty space
        $nwres = dbquery("
            SELECT
                COUNT(id)
            FROM
                wormholes
            ");
        $nwarr = mysql_fetch_row($nwres);
        if (fmod((int) $nwarr[0], 2) != 0) {
            echo "<br>Ein Wurmloch ist zuviel, lösche es!<br>";
            $res = dbquery("
                SELECT
                    id
                FROM
                    wormholes
                ");
            $arr = mysql_fetch_array($res);
            dbquery("
                    UPDATE
                        entities
                    SET
                        code='e'
                    WHERE
                        id='" . $arr['id'] . "'
                ");
            dbquery("
                    DELETE FROM
                        wormholes
                    WHERE
                        id='" . $arr['id'] . "'
                ");
            dbquery("
                    INSERT INTO
                        space
                    (
                        id,
                        lastvisited
                    )
                    VALUES
                    (
                        " . $arr['id'] . ",
                        0
                    );
                ");
        }

        //
        // Wormhole-Linking
        //

        // Get all wormholes
        $wh = array();
        $wh_persistent = array();
        $res = dbquery("
            SELECT
                id,
                target_id,
                persistent
            FROM
                wormholes
            ");
        $wormhole_count = mysql_num_rows($res);
        while ($arr = mysql_fetch_array($res)) {
            if ($arr['persistent'] == 1) {
                array_push($wh_persistent, $arr['id']);
            } else {
                array_push($wh, $arr['id']);
            }
        }

        // Shuffle wormholes
        shuffle($wh);
        shuffle($wh_persistent);

        // Reduce list of persistent wormholes if uneven
        if (fmod(count($wh_persistent), 2) != 0) {
            $lastWormHole = array_pop($wh_persistent);
            dbquery("
                UPDATE
                    wormholes
                SET
                    persistent=0
                WHERE
                    id='" . $lastWormHole . "';
                ");
            array_push($wh, $lastWormHole);
        }

        $wh_new = array();
        while (sizeof($wh) > 0) {
            $wh_new[array_shift($wh)] = array_pop($wh);
        }
        foreach ($wh_new as $k => $v) {
            dbquery("
                UPDATE
                    wormholes
                SET
                    target_id='" . $k . "'
                WHERE
                    id='" . $v . "';
                ");
            dbquery("
                UPDATE
                    wormholes
                SET
                    target_id='" . $v . "'
                WHERE
                    id='" . $k . "';
                ");
        }

        $wh_persistent_new = array();
        while (sizeof($wh_persistent) > 0) {
            $wh_persistent_new[array_shift($wh_persistent)] = array_pop($wh_persistent);
        }
        foreach ($wh_persistent_new as $k => $v) {
            dbquery("
                UPDATE
                    wormholes
                SET
                    target_id='" . $k . "'
                WHERE
                    id='" . $v . "';
                ");
            dbquery("
                UPDATE
                    wormholes
                SET
                    target_id='" . $v . "'
                WHERE
                    id='" . $k . "';
                ");
        }

        echo "Platziere Marktplatz...<br />";
        dbquery("
                UPDATE
                    entities
                SET
                    code='m'
                WHERE
                    code='e'
                ORDER BY
                    RAND()
                LIMIT
                    1;");
        dbquery("
                DELETE FROM
                    space
                WHERE
                    id='" . mysql_insert_id() . "'
                LIMIT
                    1;");


        echo "Erstelle Markt und Allianz entity...<br />";
        dbquery("
                    UPDATE
                        entities
                    SET
                        code='m'
                    WHERE
                        code='e'
                    ORDER BY
                        RAND()
                    LIMIT 1;");

        dbquery("
                DELETE FROM
                    space
                WHERE
                    id='" . mysql_insert_id() . "'
                LIMIT
                    1;");

        dbquery("
                    UPDATE
                        entities
                    SET
                        code='x'
                    WHERE
                        code='e'
                    ORDER BY
                        RAND()
                    LIMIT 1;");

        dbquery("
                DELETE FROM
                    space
                WHERE
                    id='" . mysql_insert_id() . "'
                LIMIT
                    1;");

        $mtx->release();
        echo "Universum erstellt!<br> $sol_count Sonnensysteme, $asteroids_count Asteroidenfelder, $nebula_count Nebel und $wormhole_count Wurmlöcher!";
    }

    private function createStarSystem($cell_id, $id = -1): void
    {
        $num_planets_min = $this->config->param1Int('num_planets');
        $num_planets_max = $this->config->param2Int('num_planets');

        // The Star
        $st = $this->sol_types[array_rand($this->sol_types)];

        if (-1 === $id) {
            $sql = "
                    INSERT INTO
                        entities
                    (
                        cell_id,
                        code,
                        pos
                    )
                    VALUES
                    (
                        " . $cell_id . ",
                        's',
                        0
                    );
                ";
            dbquery($sql);
            $eid = mysql_insert_id();
        } else {
            dbquery("UPDATE entities SET code = 's' WHERE id = " . $id . ";");
            $eid = $id;
        }
        $sql = "
                INSERT INTO
                    stars
                (
                    id,
                    type_id
                )
                VALUES
                (
                    " . $eid . ",
                    " . $st . "
                );
            ";
        dbquery($sql);

        // The planets
        $np = mt_rand($num_planets_min, $num_planets_max);
        for ($cnp = 1; $cnp <= $np; $cnp++) {
            $r = mt_rand(0, 100);
            if ($r <= $this->config->getInt('solsys_percent_planet')) {
                $this->createPlanet($cell_id, $cnp, $np);
            } elseif ($r <= $this->config->getInt('solsys_percent_planet') + $this->config->getInt('solsys_percent_asteroids')) {
                $this->createAsteroids($cell_id, $cnp);
            } else {
                $this->createEmptySpace($cell_id, $cnp);
            }
        }
    }

    private function createPlanet($cell_id, $pos, $np): void
    {
        $planet_fields_min = $this->config->param1Int('planet_fields');
        $planet_fields_max = $this->config->param2Int('planet_fields');

        $planet_temp_min = $this->config->param1Int('planet_temp');
        $planet_temp_max = $this->config->param2Int('planet_temp');
        $planet_temp_diff = $this->config->getInt('planet_temp');
        $planet_temp_totaldiff = abs($planet_temp_min) + abs($planet_temp_max);

        $num_planet_images = $this->config->getInt('num_planet_images');

        $sql = "
                INSERT INTO
                    entities
                (
                    cell_id,
                    code,
                    pos
                )
                VALUES
                (
                    " . $cell_id . ",
                    'p',
                    " . $pos . "
                );
            ";
        dbquery($sql);
        $eid = mysql_insert_id();

        $pt = $this->planet_types[array_rand($this->planet_types)];
        $img_nr = $pt . "_" . mt_rand(1, $num_planet_images);
        $fields = mt_rand($planet_fields_min, $planet_fields_max);
        $tblock =  (int) round($planet_temp_totaldiff / $np);
        $temp = mt_rand($planet_temp_max - ($tblock * $pos), ($planet_temp_max - ($tblock * $pos) + $tblock));
        $tmin = $temp - $planet_temp_diff;
        $tmax = $temp + $planet_temp_diff;
        $sql = "
                INSERT INTO
                    planets
                (
                    id,
                    planet_type_id,
                    planet_fields,
                    planet_image,
                    planet_temp_from,
                    planet_temp_to
                )
                VALUES
                (
                    '" . $eid . "',
                    '" . $pt . "',
                    '" . $fields . "',
                    '" . $img_nr . "',
                    '" . $tmin . "',
                    '" . $tmax . "'
                )";
        dbquery($sql);    // Planet speichern
    }

    private function createAsteroids($cell_id, $pos = 0): void
    {
        $sql = "
                INSERT INTO
                    entities
                (
                    cell_id,
                    code,
                    pos
                )
                VALUES
                (
                    " . $cell_id . ",
                    'a',
                    " . $pos . "
                );
            ";
        dbquery($sql);
        $eid = mysql_insert_id();

        $asteroid_metal = mt_rand($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $asteroid_crystal = mt_rand($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $asteroid_plastic = mt_rand($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $sql = "
                INSERT INTO
                    asteroids
                (
                    id,
                    res_metal,
                    res_crystal,
                    res_plastic
                )
                VALUES
                (
                    " . $eid . ",
                    " . $asteroid_metal . ",
                    " . $asteroid_crystal . ",
                    " . $asteroid_plastic . "
                );
            ";
        dbquery($sql);
    }

    private function createNebula($cell_id, $pos = 0): void
    {
        $sql = "
                INSERT INTO
                    entities
                (
                    cell_id,
                    code,
                    pos
                )
                VALUES
                (
                    " . $cell_id . ",
                    'n',
                    " . $pos . "
                );
            ";
        dbquery($sql);
        $eid = mysql_insert_id();

        $nebula_ress = mt_rand($this->config->param1Int('nebula_ress'), $this->config->param2Int('nebula_ress'));
        $sql = "
                INSERT INTO
                    nebulas
                (
                    id,
                    res_crystal
                )
                VALUES
                (
                    " . $eid . ",
                    " . $nebula_ress . "
                );
            ";
        dbquery($sql);
    }

    private function createWormhole($cell_id, $pos = 0): void
    {
        $persistent_wormholes_ratio = $this->config->getInt('persistent_wormholes_ratio');
        $sql = "
                INSERT INTO
                    entities
                (
                    cell_id,
                    code,
                    pos
                )
                VALUES
                (
                    " . $cell_id . ",
                    'w',
                    " . $pos . "
                );
            ";
        dbquery($sql);
        $eid = mysql_insert_id();

        $persistent = (mt_rand(0, 100) <= $persistent_wormholes_ratio) ? 1 : 0;
        $sql = "
                INSERT INTO
                    wormholes
                (
                    id,
                    changed,
                    persistent
                )
                VALUES
                (
                    " . $eid . ",
                    " . time() . ",
                    " . $persistent . "
                );
            ";
        dbquery($sql);
    }

    private function createEmptySpace($cell_id, $pos = 0): void
    {
        $sql = "
                INSERT INTO
                    entities
                (
                    cell_id,
                    code,
                    pos
                )
                VALUES
                (
                    " . $cell_id . ",
                    'e',
                    " . $pos . "
                );
            ";
        dbquery($sql);
        $eid = mysql_insert_id();

        $sql = "
                INSERT INTO
                    space
                (
                    id
                )
                VALUES
                (
                    " . $eid . "
                );
            ";
        dbquery($sql);
    }

    /**
     * Replaces n asteroid/emptyspace cells
     * with new star systems
     */
    public function addStarSystems($n = 0): int
    {
        $res = dbquery("SELECT id, cell_id, code FROM entities WHERE code in ('e', 'a') AND pos=0 ORDER BY RAND() LIMIT " . $n . ";");
        $added = 0;
        while ($row = mysql_fetch_array($res)) {
            $sql = '';
            if ($row['code'] === 'e') {
                $sql = "DELETE FROM space where id='" . $row['id'] . "';";
            } elseif ($row['code'] === 'a') {
                $sql = "DELETE FROM asteroids where id='" . $row['id'] . "';";
            }
            if ('' !== $sql) {
                dbquery($sql);
                $this->createStarSystem($row['cell_id'], $row['id']);
                $added++;
            }
        }
        return $added;
    }

    /**
     * Resets the universe and all user data
     * The Anti-Big-Bang
     */
    public function reset($all = true): void
    {
        $mtx = new Mutex();
        $mtx->acquire();

        $tbl = [];
        $tbl[] = "cells";
        $tbl[] = "entities";
        $tbl[] = "stars";
        $tbl[] = "planets";
        $tbl[] = "asteroids";
        $tbl[] = "nebulas";
        $tbl[] = "wormholes";
        $tbl[] = "space";

        $res = dbquery("SELECT COUNT(id) FROM planets WHERE planet_user_id>0;");
        $arr = mysql_fetch_row($res);
        if ($arr[0] > 0) {
            $tbl[] = "buildlist";
            $tbl[] = "deflist";
            $tbl[] = "def_queue";
            $tbl[] = "fleet";
            $tbl[] = "fleet_ships";
            $tbl[] = "market_auction";
            $tbl[] = "market_ship";
            $tbl[] = "market_ressource";
            $tbl[] = "missilelist";
            $tbl[] = "missile_flights";
            $tbl[] = "missile_flights_obj";
            $tbl[] = "shiplist";
            $tbl[] = "ship_queue";
            $tbl[] = "techlist";
        }

        if ($all) {
            $tbl[] = "alliances";
            $tbl[] = "alliance_bnd";
            $tbl[] = "alliance_applications";
            $tbl[] = "alliance_history";
            $tbl[] = "alliance_news";
            $tbl[] = "alliance_ranks";
            $tbl[] = "alliance_poll_votes";
            $tbl[] = "alliance_rankrights";
            $tbl[] = "allianceboard_cat";
            $tbl[] = "allianceboard_posts";
            $tbl[] = "allianceboard_catranks";
            $tbl[] = "allianceboard_topics";
            $tbl[] = "alliance_stats";
            $tbl[] = "alliance_polls";
            $tbl[] = "alliance_points";
            $tbl[] = "alliance_buildlist";
            $tbl[] = "alliance_spends";
            $tbl[] = "alliance_techlist";

            $tbl[] = "users";
            $tbl[] = "user_multi";
            $tbl[] = "user_log";
            $tbl[] = "user_sessionlog";
            $tbl[] = "user_points";
            $tbl[] = "user_sitting";
            $tbl[] = "user_stats";
            $tbl[] = "user_ratings";
            $tbl[] = "user_onlinestats";
            $tbl[] = "user_comments";
            $tbl[] = "user_warnings";
            $tbl[] = "user_properties";
            $tbl[] = "user_sessions";
            $tbl[] = "user_surveillance";

            $tbl[] = "buddylist";
            $tbl[] = "messages";
            $tbl[] = "message_data";
            $tbl[] = "message_ignore";
            $tbl[] = "notepad";
            $tbl[] = "notepad_data";
            $tbl[] = "bookmarks";
            $tbl[] = "fleet_bookmarks";
            $tbl[] = "chat_log";
            $tbl[] = "reports";
            $tbl[] = "reports_other";
            $tbl[] = "reports_battle";
            $tbl[] = "reports_spy";
            $tbl[] = "reports_market";

            $tbl[] = "logs";
            $tbl[] = "logs_alliance";
            $tbl[] = "logs_battle";
            $tbl[] = "logs_fleet";
            $tbl[] = "logs_game";

            $tbl[] = "login_failures";
            $tbl[] = "admin_user_log";
            $tbl[] = "admin_user_sessionlog";
            $tbl[] = "tickets";
            $tbl[] = "ticket_msg";
            $tbl[] = "chat";
            $tbl[] = "chat_users";
            $tbl[] = "hostname_cache";
            $tbl[] = "backend_message_queue";
        } else {
            dbquery("
                UPDATE
                    users
                SET
                    discoverymask='',
                    user_setup = 0
                ");
        }

        dbquery("SET FOREIGN_KEY_CHECKS=0;");
        foreach ($tbl as $t) {
            dbquery("TRUNCATE $t;");
            echo "Leere Tabelle <b>$t</b><br/>";
        }
        dbquery("SET FOREIGN_KEY_CHECKS=1;");

        dbquery("
                    UPDATE
                        config
                    SET
                        config_value='0',
                        config_param1='0'
                    WHERE
                        config_name LIKE '%logger%';");
        dbquery("
                    UPDATE
                        config
                    SET
                        config_value='1'
                    WHERE
                        config_name IN ('market_metal_factor','market_crystal_factor','market_plastic_factor','market_fuel_factor','market_food_factor');");

        // Remove user XML backups
        $userXmlPath = UserToXml::getDataDirectory();
        foreach (new DirectoryIterator($userXmlPath) as $fileInfo) {
            if (!$fileInfo->isDot()) {
                unlink($fileInfo->getPathname());
            }
        }

        $mtx->release();
    }
}
