<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\Configuration\ConfigurationService;
use Mutex;

class UniverseGenerator
{
    private ConfigurationService $config;
    private SolarTypeRepository $solarTypesRepo;
    private PlanetTypeRepository $planetTypesRepo;
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
    private array $solTypes = [];

    /**
     * @var array<int>
     */
    private array $planetTypes = [];

    private const GALAXY_IMAGE_DIR_PATH = "../images/galaxylayouts";

    public function __construct(
        ConfigurationService $config,
        SolarTypeRepository $solarTypesRepo,
        PlanetTypeRepository $planetTypesRepo,
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
        $this->solarTypesRepo = $solarTypesRepo;
        $this->planetTypesRepo = $planetTypesRepo;
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
        $this->solTypes = array_keys($this->solarTypesRepo->getSolarTypeNames());
        $this->planetTypes = array_keys($this->planetTypesRepo->getPlanetTypeNames());
    }

    /**
     * Create the universe.
     * And there was light!
     *
     * @return array<string>
     */
    public function create(string $mapImage = "", int $mapPrecision = 95): array
    {
        $output = [];

        $lock = new Mutex();
        $lock->acquire();

        $mapPrecision = max(0, $mapPrecision);
        $mapPrecision = min($mapPrecision, 100);

        $output[] = "Lade Schöpfungs-Einstellungen...";

        $numberOfSectorsX = $this->config->param1Int('num_of_sectors');
        $numberOfSectorsY = $this->config->param2Int('num_of_sectors');
        $numberOfCellsX = $this->config->param1Int('num_of_cells');
        $numberOfCellsY = $this->config->param2Int('num_of_cells');

        $starCount = 0;
        $nebulaCount = 0;
        $asteroidsCount = 0;
        $wormholeCount = 0;

        $output[] = "Erstelle Universum mit " . $numberOfSectorsX * $numberOfSectorsY . " Sektoren à " . $numberOfCellsX * $numberOfCellsY . " Zellen, d.h. " . $numberOfSectorsX * $numberOfSectorsY * $numberOfCellsX * $numberOfCellsY . " Zellen total.";

        $imagePath = self::GALAXY_IMAGE_DIR_PATH . "/" . $mapImage;
        if ($mapImage != "" && is_file($imagePath)) {
            $output[] = "Bildvorlage gefunden, verwende diese: <img src=\"" . $imagePath . "\" />";
            $type = $this->getTypeMatrixFromImage($imagePath, $mapPrecision);
        } else {
            $type = $this->getRandomTypeMatrix($numberOfSectorsX, $numberOfSectorsY, $numberOfCellsX, $numberOfCellsY);
        }

        // Save cell info
        $coordinates = $this->generateCoordinates($numberOfSectorsX, $numberOfSectorsY, $numberOfCellsX, $numberOfCellsY);
        $output[] = "Zellen geneiert, speichere sie...";
        $this->cellRepo->addMultiple($coordinates);

        $output[] = "Zellen gespeichert, fülle Objekte rein...";
        $cells = $this->cellRepo->findAllCoordinates();
        foreach ($cells as $cell) {
            $x = (($cell['sx'] - 1) * $numberOfCellsX) + $cell['cx'];
            $y = (($cell['sy'] - 1) * $numberOfCellsY) + $cell['cy'];

            // Star system
            if ($type[$x][$y] == EntityType::STAR) {
                $this->createStarSystem((int) $cell['id']);
                $starCount++;
            }

            // Asteroid Fields
            elseif ($type[$x][$y] == EntityType::ASTEROIDS) {
                $this->createAsteroids((int) $cell['id']);
                $asteroidsCount++;
            }

            // Nebulas
            elseif ($type[$x][$y] == EntityType::NEBULA) {
                $this->createNebula((int) $cell['id']);
                $nebulaCount++;
            }

            // Wormholes
            elseif ($type[$x][$y] == EntityType::WORMHOLE) {
                $this->createWormhole((int) $cell['id']);
                $wormholeCount++;
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
        $wormholes = [];
        $persistentWormholes = [];
        $allWormholes = $this->wormholeRepo->findAll();
        $wormholeCount = count($allWormholes);
        foreach ($allWormholes as $wormhole) {
            if ($wormhole['persistent'] == 1) {
                array_push($persistentWormholes, (int) $wormhole['id']);
            } else {
                array_push($wormholes, (int) $wormhole['id']);
            }
        }

        // Shuffle wormholes
        shuffle($wormholes);
        shuffle($persistentWormholes);

        // Reduce list of persistent wormholes if uneven
        if (fmod(count($persistentWormholes), 2) != 0) {
            $lastWormHole = array_pop($persistentWormholes);
            $this->wormholeRepo->setPersistent($lastWormHole, false);
            array_push($wormholes, $lastWormHole);
        }

        $newWormholes = [];
        while (sizeof($wormholes) > 0) {
            $newWormholes[array_shift($wormholes)] = array_pop($wormholes);
        }
        foreach ($newWormholes as $k => $v) {
            $this->wormholeRepo->updateTarget($v, $k);
            $this->wormholeRepo->updateTarget($k, $v);
        }

        $newPersistentWormholes = [];
        while (sizeof($persistentWormholes) > 0) {
            $newPersistentWormholes[array_shift($persistentWormholes)] = array_pop($persistentWormholes);
        }
        foreach ($newPersistentWormholes as $k => $v) {
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

        $lock->release();

        $output[] = "Universum erstellt!";
        $output[] = "$starCount Sonnensysteme, $asteroidsCount Asteroidenfelder, $nebulaCount Nebel und $wormholeCount Wurmlöcher!";

        return $output;
    }

    private function getTypeMatrixFromImage(string $imagePath, int $mapPrecision): array
    {
        $type = [];

        $percentageStars = $this->config->getInt('space_percent_solsys');
        $percentageAsteroids = $this->config->getInt('space_percent_asteroids');
        $percentageNebulas = $this->config->getInt('space_percent_nebulas');
        $percentageWormholes = $this->config->getInt('space_percent_wormholes');

        $image = imagecreatefrompng($imagePath);
        $width = imagesx($image);
        $height = imagesy($image);

        for ($x = 1; $x <= $width; $x++) {
            for ($y = 1; $y <= $height; $y++) {
                $color = imagecolorat($image, $x - 1, $height - $y);
                $pr = random_int(0, 100);

                if (($color > 0 && $pr <= $mapPrecision) || ($color == 0 && $pr >= $mapPrecision)) {
                    $ct = random_int(1, 100);

                    if ($ct <= $percentageStars) {
                        $type[$x][$y] = EntityType::STAR;
                    } elseif ($ct <= $percentageStars + $percentageAsteroids) {
                        $type[$x][$y] = EntityType::ASTEROIDS;
                    } elseif ($ct <= $percentageStars + $percentageAsteroids + $percentageNebulas) {
                        $type[$x][$y] = EntityType::NEBULA;
                    } elseif ($ct <= $percentageStars + $percentageAsteroids + $percentageNebulas + $percentageWormholes) {
                        $type[$x][$y] = EntityType::WORMHOLE;
                    } else {
                        $type[$x][$y] = EntityType::EMPTY_SPACE;
                    }
                } else {
                    $type[$x][$y] = EntityType::EMPTY_SPACE;
                }
            }
        }

        return $type;
    }

    private function getRandomTypeMatrix(int $numberOfSectorsX, int $numberOfSectorsY, int $numberOfCellsX, int $numberOfCellsY): array
    {
        $type = [];

        $percentageStars = $this->config->getInt('space_percent_solsys');
        $percentageAsteroids = $this->config->getInt('space_percent_asteroids');
        $percentageNebulas = $this->config->getInt('space_percent_nebulas');
        $percentageWormholes = $this->config->getInt('space_percent_wormholes');

        for ($x = 1; $x <= ($numberOfSectorsX * $numberOfCellsX); $x++) {
            for ($y = 1; $y <= ($numberOfSectorsY * $numberOfCellsY); $y++) {
                $ct = random_int(1, 100);
                if ($ct <= $percentageStars) {
                    $type[$x][$y] = EntityType::STAR;
                } elseif ($ct <= $percentageStars + $percentageAsteroids) {
                    $type[$x][$y] = EntityType::ASTEROIDS;
                } elseif ($ct <= $percentageStars + $percentageAsteroids + $percentageNebulas) {
                    $type[$x][$y] = EntityType::NEBULA;
                } elseif ($ct <= $percentageStars + $percentageAsteroids + $percentageNebulas + $percentageWormholes) {
                    $type[$x][$y] = EntityType::WORMHOLE;
                } else {
                    $type[$x][$y] = EntityType::EMPTY_SPACE;
                }
            }
        }

        return $type;
    }

    private function generateCoordinates(int $numberOfSectorsX, int $numberOfSectorsY, int $numberOfCellsX, int $numberOfCellsY): array
    {
        $coordinates = [];
        for ($sx = 1; $sx <= $numberOfSectorsX; $sx++) {
            for ($sy = 1; $sy <= $numberOfSectorsY; $sy++) {
                for ($cx = 1; $cx <= $numberOfCellsX; $cx++) {
                    for ($cy = 1; $cy <= $numberOfCellsY; $cy++) {
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
        $type = $this->solTypes[array_rand($this->solTypes)];

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

        $typeId = $this->planetTypes[array_rand($this->planetTypes)];
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
