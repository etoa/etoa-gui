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
    private SolarTypeRepository $solarTypes;
    private PlanetTypeRepository $planetTypes;
    private CellRepository $cellRepo;
    private EntityRepository $entityRepo;
    private StarRepository $starRepo;
    private AsteroidsRepository $asteroidsRepo;
    private NebulaRepository $nebulaRepo;
    private WormholeRepository $wormholeRepo;
    private EmptySpaceRepository $emptySpaceRepo;

    /**
     * @var array<int>
     */
    private array $sol_types = [];

    /**
     * @var array<int>
     */
    private array $planet_types = [];

    public function __construct(
        ConfigurationService $config,
        SolarTypeRepository $solarTypes,
        PlanetTypeRepository $planetTypes,
        CellRepository $cellRepo,
        EntityRepository $entityRepo,
        StarRepository $starRepo,
        AsteroidsRepository $asteroidsRepo,
        NebulaRepository $nebulaRepo,
        WormholeRepository $wormholeRepo,
        EmptySpaceRepository $emptySpaceRepo
    )
    {
        $this->config = $config;
        $this->solarTypes = $solarTypes;
        $this->planetTypes = $planetTypes;
        $this->cellRepo = $cellRepo;
        $this->entityRepo = $entityRepo;
        $this->starRepo = $starRepo;
        $this->asteroidsRepo = $asteroidsRepo;
        $this->nebulaRepo = $nebulaRepo;
        $this->wormholeRepo = $wormholeRepo;
        $this->emptySpaceRepo = $emptySpaceRepo;

        $this->init();
    }

    private function init(): void
    {
        $this->sol_types = array_keys($this->solarTypes->getSolarTypeNames());
        $this->planet_types = array_keys($this->planetTypes->getPlanetTypeNames());
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
                            $type[$x][$y] = EntityType::STAR;
                        } elseif ($ct <= $perc_solsys + $perc_asteroids) {
                            $type[$x][$y] = EntityType::ASTEROIDS;
                        } elseif ($ct <= $perc_solsys + $perc_asteroids + $perc_nebulas) {
                            $type[$x][$y] = EntityType::NEBULA;
                        } elseif ($ct <= $perc_solsys + $perc_asteroids + $perc_nebulas + $perc_wormholes) {
                            $type[$x][$y] = EntityType::WORMHOLE;
                        } else {
                            $type[$x][$y] = EntityType::EMPTY_SPACE;
                        }
                    } else {
                        $type[$x][$y] = EntityType::EMPTY_SPACE;
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
                        $type[$x][$y] = EntityType::STAR;
                    } elseif ($ct <= $perc_solsys + $perc_asteroids) {
                        $type[$x][$y] = EntityType::ASTEROIDS;
                    } elseif ($ct <= $perc_solsys + $perc_asteroids + $perc_nebulas) {
                        $type[$x][$y] = EntityType::NEBULA;
                    } elseif ($ct <= $perc_solsys + $perc_asteroids + $perc_nebulas + $perc_wormholes) {
                        $type[$x][$y] = EntityType::WORMHOLE;
                    } else {
                        $type[$x][$y] = EntityType::EMPTY_SPACE;
                    }
                }
            }
        }

        // Save cell info
        $coordinates = $this->generateCoordinates($sx_num, $sy_num, $cx_num, $cy_num);
        echo "Zellen geneiert, speichere sie...<br/>";
        $this->cellRepo->addMultiple($coordinates);

        echo "Zellen gespeichert, fülle Objekte rein...<br/>";
        $cells = $this->cellRepo->findAllCoordinates();
        foreach ($cells as $cell) {
            $x = (($cell['sx'] - 1) * $cx_num) + $cell['cx'];
            $y = (($cell['sy'] - 1) * $cy_num) + $cell['cy'];

            // Star system
            if ($type[$x][$y] == EntityType::STAR) {
                $this->createStarSystem((int) $cell['id']);
                $sol_count++;
            }

            // Asteroid Fields
            elseif ($type[$x][$y] == EntityType::ASTEROIDS) {
                $this->createAsteroids((int) $cell['id']);
                $asteroids_count++;
            }

            // Nebulas
            elseif ($type[$x][$y] == EntityType::NEBULA) {
                $this->createNebula((int) $cell['id']);
                $nebula_count++;
            }

            // Wormholes
            elseif ($type[$x][$y] == EntityType::WORMHOLE) {
                $this->createWormhole((int) $cell['id']);
                $wormhole_count++;
            }

            // Empty space
            else {
                $this->createEmptySpace((int) $cell['id']);
            }
        }
        echo "Universum erstellt, prüfe Wurmlöcher...<br/>";

        // Delete one wormhole if total count is odd
        // Replace it with empty space
        $numWormholes = $this->wormholeRepo->count();
        if (fmod((int) $numWormholes, 2) != 0) {
            echo "<br>Ein Wurmloch ist zuviel, lösche es!<br>";
            $wormholeId = $this->wormholeRepo->getOneId();
            if ($wormholeId !== null) {
                $this->entityRepo->updateCode($wormholeId, EntityType::EMPTY_SPACE);
                $this->wormholeRepo->remove($wormholeId);
                $this->emptySpaceRepo->add($wormholeId);
            }
        }

        //
        // Wormhole-Linking
        //

        // Get all wormholes
        $wh = array();
        $wh_persistent = array();
        $wormholes = $this->wormholeRepo->findAll();
        $wormhole_count = count($wormholes);
        foreach ($wormholes as $wormhole) {
            if ($wormhole['persistent'] == 1) {
                array_push($wh_persistent, (int) $wormhole['id']);
            } else {
                array_push($wh, (int) $wormhole['id']);
            }
        }

        // Shuffle wormholes
        shuffle($wh);
        shuffle($wh_persistent);

        // Reduce list of persistent wormholes if uneven
        if (fmod(count($wh_persistent), 2) != 0) {
            $lastWormHole = array_pop($wh_persistent);
            $this->wormholeRepo->setPersistent($lastWormHole, false);
            array_push($wh, $lastWormHole);
        }

        $wh_new = [];
        while (sizeof($wh) > 0) {
            $wh_new[array_shift($wh)] = array_pop($wh);
        }
        foreach ($wh_new as $k => $v) {
            $this->wormholeRepo->updateTarget($v, $k);
            $this->wormholeRepo->updateTarget($k, $v);
        }

        $wh_persistent_new = [];
        while (sizeof($wh_persistent) > 0) {
            $wh_persistent_new[array_shift($wh_persistent)] = array_pop($wh_persistent);
        }
        foreach ($wh_persistent_new as $k => $v) {
            $this->wormholeRepo->updateTarget($v, $k);
            $this->wormholeRepo->updateTarget($k, $v);
        }

        echo "Platziere Marktplatz...<br />";
        dbquery("
                UPDATE
                    entities
                SET
                    code='m'
                WHERE
                    code='".EntityType::EMPTY_SPACE."'
                ORDER BY
                    RAND()
                LIMIT
                    1;");
        $this->emptySpaceRepo->remove(mysql_insert_id());

        echo "Erstelle Markt und Allianz entity...<br />";
        dbquery("
                    UPDATE
                        entities
                    SET
                        code='m'
                    WHERE
                        code='".EntityType::EMPTY_SPACE."'
                    ORDER BY
                        RAND()
                    LIMIT 1;");
        $this->emptySpaceRepo->remove(mysql_insert_id());

        dbquery("
                    UPDATE
                        entities
                    SET
                        code='x'
                    WHERE
                        code='".EntityType::EMPTY_SPACE."'
                    ORDER BY
                        RAND()
                    LIMIT 1;");
        $this->emptySpaceRepo->remove(mysql_insert_id());

        $mtx->release();
        echo "Universum erstellt!<br> $sol_count Sonnensysteme, $asteroids_count Asteroidenfelder, $nebula_count Nebel und $wormhole_count Wurmlöcher!";
    }

    private function generateCoordinates(int $sx_num, int $sy_num, int $cx_num, int $cy_num): array
    {
        $coordinates = [];
        for ($sx = 1; $sx <= $sx_num; $sx++) {
            for ($sy = 1; $sy <= $sy_num; $sy++) {
                for ($cx = 1; $cx <= $cx_num; $cx++) {
                    for ($cy = 1; $cy <= $cy_num; $cy++) {
                        $coordinates[] = [
                            'sx' => $sx,
                            'sy' => $sy,
                            'cx' => $cx,
                            'cy' => $cy,
                        ];
                    }
                }
            }
        }
        return $coordinates;
    }

    private function createStarSystem(int $cellId, ?int $id = null): void
    {
        $num_planets_min = $this->config->param1Int('num_planets');
        $num_planets_max = $this->config->param2Int('num_planets');

        // The Star
        $type = $this->sol_types[array_rand($this->sol_types)];

        if ($id === null) {
            $entityId = $this->entityRepo->add($cellId, EntityType::STAR, 0);
        } else {
            $this->entityRepo->updateCode($id, EntityType::STAR);
            $entityId = $id;
        }
        $this->starRepo->add($entityId, $type);

        // The planets
        $np = mt_rand($num_planets_min, $num_planets_max);
        for ($cnp = 1; $cnp <= $np; $cnp++) {
            $r = mt_rand(0, 100);
            if ($r <= $this->config->getInt('solsys_percent_planet')) {
                $this->createPlanet($cellId, $cnp, $np);
            } elseif ($r <= $this->config->getInt('solsys_percent_planet') + $this->config->getInt('solsys_percent_asteroids')) {
                $this->createAsteroids($cellId, $cnp);
            } else {
                $this->createEmptySpace($cellId, $cnp);
            }
        }
    }

    private function createPlanet(int $cellId, int $pos, $np): void
    {
        $planet_fields_min = $this->config->param1Int('planet_fields');
        $planet_fields_max = $this->config->param2Int('planet_fields');

        $planet_temp_min = $this->config->param1Int('planet_temp');
        $planet_temp_max = $this->config->param2Int('planet_temp');
        $planet_temp_diff = $this->config->getInt('planet_temp');
        $planet_temp_totaldiff = abs($planet_temp_min) + abs($planet_temp_max);

        $num_planet_images = $this->config->getInt('num_planet_images');

        $id = $this->entityRepo->add($cellId, EntityType::PLANET, $pos);

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
                    '" . $id . "',
                    '" . $pt . "',
                    '" . $fields . "',
                    '" . $img_nr . "',
                    '" . $tmin . "',
                    '" . $tmax . "'
                )";
        dbquery($sql);    // Planet speichern
    }

    private function createAsteroids(int $cellId, int $pos = 0): void
    {
        $metal = mt_rand($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $crystal = mt_rand($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $plastic = mt_rand($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));

        $id = $this->entityRepo->add($cellId, EntityType::ASTEROIDS, $pos);
        $this->asteroidsRepo->add($id, $metal, $crystal, $plastic);
    }

    private function createNebula(int $cellId, int $pos = 0): void
    {
        $crystal = mt_rand($this->config->param1Int('nebula_ress'), $this->config->param2Int('nebula_ress'));

        $id = $this->entityRepo->add($cellId, EntityType::NEBULA, $pos);
        $this->nebulaRepo->add($id, $crystal);
    }

    private function createWormhole(int $cellId): void
    {
        $persistent = (mt_rand(0, 100) <= $this->config->getInt('persistent_wormholes_ratio'));

        $id = $this->entityRepo->add($cellId, EntityType::WORMHOLE);
        $this->wormholeRepo->add($id, $persistent);
    }

    private function createEmptySpace(int $cellId, int $pos = 0): void
    {
        $id = $this->entityRepo->add($cellId, EntityType::EMPTY_SPACE, $pos);
        $this->emptySpaceRepo->add($id);
    }

    /**
     * Replaces n asteroid/emptyspace cells
     * with new star systems
     */
    public function addStarSystems($n = 0): int
    {
        $res = dbquery("SELECT id, cell_id, code FROM entities WHERE code in ('".EntityType::EMPTY_SPACE."', '".EntityType::ASTEROIDS."') AND pos=0 ORDER BY RAND() LIMIT " . $n . ";");
        $added = 0;
        while ($row = mysql_fetch_array($res)) {
            $sql = '';
            if ($row['code'] === EntityType::EMPTY_SPACE) {
                $sql = "DELETE FROM space where id='" . $row['id'] . "';";
            } elseif ($row['code'] === EntityType::ASTEROIDS) {
                $sql = "DELETE FROM asteroids where id='" . $row['id'] . "';";
            }
            if ('' !== $sql) {
                dbquery($sql);
                $this->createStarSystem((int) $row['cell_id'], (int) $row['id']);
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
