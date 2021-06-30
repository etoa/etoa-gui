<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\Configuration\ConfigurationService;
use Mutex;

class UniverseGenerator
{
    private ConfigurationService $config;
    private SolarTypeRepository $solarTypes;
    private PlanetTypeRepository $planetTypes;
    private CellRepository $cellRepo;
    private EntityRepository $entityRepo;
    private StarRepository $starRepo;
    private PlanetRepository $planetRepo;
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
        PlanetRepository $planetRepo,
        AsteroidsRepository $asteroidsRepo,
        NebulaRepository $nebulaRepo,
        WormholeRepository $wormholeRepo,
        EmptySpaceRepository $emptySpaceRepo
    ) {
        $this->config = $config;
        $this->solarTypes = $solarTypes;
        $this->planetTypes = $planetTypes;
        $this->cellRepo = $cellRepo;
        $this->entityRepo = $entityRepo;
        $this->starRepo = $starRepo;
        $this->planetRepo = $planetRepo;
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
     *
     * @return array<string>
     */
    public function create($mapImage = "", $mapPrecision = 95): array
    {
        $output = [];

        $mtx = new Mutex();
        $mtx->acquire();

        $mapPrecision = max(0, $mapPrecision);
        $mapPrecision = min($mapPrecision, 100);

        $output[] = "Lade Schöpfungs-Einstellungen...";

        $sx_num = $this->config->param1Int('num_of_sectors');
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cx_num = $this->config->param1Int('num_of_cells');
        $cy_num = $this->config->param2Int('num_of_cells');
        $perc_solsys = $this->config->getInt('space_percent_solsys');
        $perc_asteroids = $this->config->getInt('space_percent_asteroids');
        $perc_nebulas = $this->config->getInt('space_percent_nebulas');
        $perc_wormholes = $this->config->getInt('space_percent_wormholes');

        $sol_count = 0;
        $nebula_count = 0;
        $asteroids_count = 0;
        $wormhole_count = 0;

        $output[] = "Erstelle Universum mit " . $sx_num * $sy_num . " Sektoren à " . $cx_num * $cy_num . " Zellen, d.h. " . $sx_num * $sy_num * $cx_num * $cy_num . " Zellen total.";

        $type = [];

        //
        // Set cell types
        //

        // by image
        $imgpath = "../images/galaxylayouts/" . $mapImage;
        if ($mapImage != "" && is_file($imgpath)) {
            $im = imagecreatefrompng($imgpath);
            $w = imagesx($im);
            $h = imagesy($im);

            $output[] = "Bildvorlage gefunden, verwende diese: <img src=\"" . $imgpath . "\" />";

            for ($x = 1; $x <= $w; $x++) {
                for ($y = 1; $y <= $h; $y++) {
                    $o = imagecolorat($im, $x - 1, $h - $y);
                    $pr = random_int(0, 100);

                    if (($o > 0 && $pr <= $mapPrecision) || ($o == 0 && $pr >= $mapPrecision)) {
                        $ct = random_int(1, 100);

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
                    $ct = random_int(1, 100);
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
        $output[] = "Zellen geneiert, speichere sie...";
        $this->cellRepo->addMultiple($coordinates);

        $output[] = "Zellen gespeichert, fülle Objekte rein...";
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
        $output[] = "Universum erstellt, prüfe Wurmlöcher...";

        // Delete one wormhole if total count is odd
        // Replace it with empty space
        $numWormholes = $this->wormholeRepo->count();
        if (fmod($numWormholes, 2) != 0) {
            $output[] = "Ein Wurmloch ist zuviel, lösche es!";
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
        $wh = [];
        $wh_persistent = [];
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

        $output[] = "Platziere Marktplatz...";
        $id = $this->entityRepo->findRandomId(EntityType::EMPTY_SPACE);
        $this->entityRepo->updateCode($id, EntityType::MARKET);
        $this->emptySpaceRepo->remove($id);

        $output[] = "Erstelle Markt und Allianz entity...";
        $id = $this->entityRepo->findRandomId(EntityType::EMPTY_SPACE);
        $this->entityRepo->updateCode($id, EntityType::ALLIANCE_MARKET);
        $this->emptySpaceRepo->remove($id);

        $mtx->release();
        $output[] = "Universum erstellt!";
        $output[] = "$sol_count Sonnensysteme, $asteroids_count Asteroidenfelder, $nebula_count Nebel und $wormhole_count Wurmlöcher!";

        return $output;
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
        $np = random_int($num_planets_min, $num_planets_max);
        for ($cnp = 1; $cnp <= $np; $cnp++) {
            $r = random_int(0, 100);
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

        $typeId = $this->planet_types[array_rand($this->planet_types)];
        $imageNumber = $typeId . "_" . random_int(1, $num_planet_images);

        $fields = random_int($planet_fields_min, $planet_fields_max);

        $tblock = (int) round($planet_temp_totaldiff / $np);
        $temp = random_int($planet_temp_max - ($tblock * $pos), ($planet_temp_max - ($tblock * $pos) + $tblock));
        $tempMin = $temp - $planet_temp_diff;
        $tempMax = $temp + $planet_temp_diff;

        $this->planetRepo->add(
            $id,
            $typeId,
            $fields,
            $imageNumber,
            $tempMin,
            $tempMax
        );
    }

    private function createAsteroids(int $cellId, int $pos = 0): void
    {
        $metal = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $crystal = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $plastic = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));

        $id = $this->entityRepo->add($cellId, EntityType::ASTEROIDS, $pos);
        $this->asteroidsRepo->add($id, $metal, $crystal, $plastic);
    }

    private function createNebula(int $cellId, int $pos = 0): void
    {
        $crystal = random_int($this->config->param1Int('nebula_ress'), $this->config->param2Int('nebula_ress'));

        $id = $this->entityRepo->add($cellId, EntityType::NEBULA, $pos);
        $this->nebulaRepo->add($id, $crystal);
    }

    private function createWormhole(int $cellId): void
    {
        $persistent = (random_int(0, 100) <= $this->config->getInt('persistent_wormholes_ratio'));

        $id = $this->entityRepo->add($cellId, EntityType::WORMHOLE);
        $this->wormholeRepo->add($id, $persistent);
    }

    private function createEmptySpace(int $cellId, int $pos = 0): void
    {
        $id = $this->entityRepo->add($cellId, EntityType::EMPTY_SPACE, $pos);
        $this->emptySpaceRepo->add($id);
    }

    /**
     * Replaces n asteroid/empty space cells
     * with new star systems
     */
    public function addStarSystems($quantity = 0): int
    {
        $entities = $this->entityRepo->findRandomByCodes([
            EntityType::EMPTY_SPACE,
            EntityType::ASTEROIDS,
        ], $quantity);

        $added = 0;
        foreach ($entities as $entity) {
            if ($entity['code'] === EntityType::EMPTY_SPACE) {
                $this->emptySpaceRepo->remove((int) $entity['id']);
            } elseif ($entity['code'] === EntityType::ASTEROIDS) {
                $this->asteroidsRepo->remove((int) $entity['id']);
            }
            $this->createStarSystem((int) $entity['cell_id'], (int) $entity['id']);
            $added++;
        }

        return $added;
    }
}
