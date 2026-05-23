<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Cell;
use EtoA\Entity\Entity;
use EtoA\Entity\PlanetType;
use EtoA\Entity\SolarType;
use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use Symfony\Component\Lock\LockFactory;

class UniverseGenerator
{
    private ConfigurationService $config;
    private SolarTypeRepository $solarTypesRepo;
    private PlanetTypeRepository $planetTypesRepo;
    private CellRepository $cellRepo;
    private EntityRepository $entityRepo;
    private StarRepository $starRepo;
    private PlanetRepository $planetRepo;
    private AsteroidRepository $asteroidRepo;
    private NebulaRepository $nebulaRepo;
    private WormholeRepository $wormholeRepo;
    private EmptySpaceRepository $emptySpaceRepo;
    private LockFactory $lockFactory;

    /**
     * @var array<SolarType>
     */
    private array $solTypes = [];

    /**
     * @var array<PlanetType>
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
        AsteroidRepository $asteroidRepo,
        NebulaRepository $nebulaRepo,
        WormholeRepository $wormholeRepo,
        EmptySpaceRepository $emptySpaceRepo,
        LockFactory $lockFactory
    ) {
        $this->config = $config;
        $this->solarTypesRepo = $solarTypesRepo;
        $this->planetTypesRepo = $planetTypesRepo;
        $this->cellRepo = $cellRepo;
        $this->entityRepo = $entityRepo;
        $this->starRepo = $starRepo;
        $this->planetRepo = $planetRepo;
        $this->asteroidRepo = $asteroidRepo;
        $this->nebulaRepo = $nebulaRepo;
        $this->wormholeRepo = $wormholeRepo;
        $this->emptySpaceRepo = $emptySpaceRepo;
        $this->lockFactory = $lockFactory;

        $this->init();
    }

    private function init(): void
    {
        $this->solTypes = $this->solarTypesRepo->getSolarTypeNames();
        $this->planetTypes = $this->planetTypesRepo->getPlanetTypeNames();
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

        $lock = $this->lockFactory->createLock('universe');
        $lock->acquire(true);

        $mapPrecision = max(0, $mapPrecision);
        $mapPrecision = min($mapPrecision, 100);

        $output[] = "Lade Schöpfungs-Einstellungen...";

        //2
        $numberOfSectorsX = $this->config->param1Int('num_of_sectors');
        //2
        $numberOfSectorsY = $this->config->param2Int('num_of_sectors');
        //10
        $numberOfCellsX = $this->config->param1Int('num_of_cells');
        //10
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
        $batchSize = 100;
        $connection = $this->entityRepo->getDatabaseConnection();
        $processedCells = 0;
        $totalCells = $numberOfSectorsX * $numberOfSectorsY * $numberOfCellsX * $numberOfCellsY;

        $connection->beginTransaction();

        try {
            foreach ($this->cellRepo->createQueryBuilder('c')->getQuery()->toIterable() as $cell) {
                [$x, $y] = $cell->getAbsoluteCoordinates($numberOfCellsX, $numberOfCellsY);
                $cellId = $cell->getId();

                if ($type[$x][$y] == EntityType::STAR) {
                    $this->createStarSystemDirect($connection, $cellId);
                    $starCount++;
                } elseif ($type[$x][$y] == EntityType::ASTEROID) {
                    $this->createAsteroidDirect($connection, $cellId, 0);
                    $asteroidsCount++;
                } elseif ($type[$x][$y] == EntityType::NEBULA) {
                    $this->createNebulaDirect($connection, $cellId, 0);
                    $nebulaCount++;
                } elseif ($type[$x][$y] == EntityType::WORMHOLE) {
                    $this->createWormholeDirect($connection, $cellId);
                } else {
                    $this->createEmptySpaceDirect($connection, $cellId, 0);
                }

                $processedCells++;

                if ($processedCells % $batchSize === 0) {
                    $output[] = "Fortschritt: $processedCells / $totalCells Zellen verarbeitet...";
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $output[] = "Universum erstellt, prüfe Wurmlöcher...";
        $wormholeCount = $this->removeOddWormhole();

        $output[] = "Verknüpfe Wurmlöcher...";
        $this->linkWormholes();

        $output[] = "Platziere Marktplatz...";
        $entity = $this->entityRepo->findRandom(EntityType::EMPTY_SPACE);
        $this->entityRepo->updateCode($entity, EntityType::MARKET);
        $this->emptySpaceRepo->remove($entity->getEmptySpace());

        $output[] = "Erstelle Markt und Allianz entity...";
        $entity = $this->entityRepo->findRandom(EntityType::EMPTY_SPACE);
        $this->entityRepo->updateCode($entity, EntityType::ALLIANCE_MARKET);
        $this->emptySpaceRepo->remove($entity->getEmptySpace());

        $this->entityRepo->save();

        $lock->release();

        $output[] = "Universum erstellt!";
        $output[] = "$starCount Sonnensysteme, $asteroidsCount Asteroidenfelder, $nebulaCount Nebel und $wormholeCount Wurmlöcher!";

        return $output;
    }

    /**
     * @return array<int, array<int, string>>
     */
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
                        $type[$x][$y] = EntityType::ASTEROID;
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

    /**
     * @return array<int, array<int, string>>
     */
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
                    $type[$x][$y] = EntityType::ASTEROID;
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

    /**
     * @return array<array<string, int>>
     */
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

    private function createStarSystem(Cell $cell, ?Entity $entity = null): void
    {
        $num_planets_min = $this->config->param1Int('num_planets');
        $num_planets_max = $this->config->param2Int('num_planets');

        // The Star
        $type = $this->solTypes[array_rand($this->solTypes)];

        if (!$entity) {
            $entity = $this->entityRepo->add($cell, EntityType::STAR);
        } else {
            $this->entityRepo->updateCode($entity, EntityType::STAR);
        }
        $this->starRepo->add($entity, $type);

        // The planets
        $np = random_int($num_planets_min, $num_planets_max);
        for ($cnp = 1; $cnp <= $np; $cnp++) {
            $r = random_int(0, 100);
            if ($r <= $this->config->getInt('solsys_percent_planet')) {
                $this->createPlanet($cell, $cnp, $np);
            } elseif ($r <= $this->config->getInt('solsys_percent_planet') + $this->config->getInt('solsys_percent_asteroids')) {
                $this->createAsteroid($cell, $cnp);
            } else {
                $this->createEmptySpace($cell, $cnp);
            }
        }
    }

    private function createPlanet(Cell $cell, int $pos, int $np): void
    {
        $planet_fields_min = $this->config->param1Int('planet_fields');
        $planet_fields_max = $this->config->param2Int('planet_fields');

        $planet_temp_min = $this->config->param1Int('planet_temp');
        $planet_temp_max = $this->config->param2Int('planet_temp');
        $planet_temp_diff = $this->config->getInt('planet_temp');
        $planet_temp_totaldiff = abs($planet_temp_min) + abs($planet_temp_max);

        $num_planet_images = $this->config->getInt('num_planet_images');

        $entity = $this->entityRepo->add($cell, EntityType::PLANET, $pos);

        $type = $this->planetTypes[array_rand($this->planetTypes)];
        $imageNumber = $type->getId() . "_" . random_int(1, $num_planet_images);

        $fields = random_int($planet_fields_min, $planet_fields_max);

        $tblock = (int) round($planet_temp_totaldiff / $np);
        $temp = random_int($planet_temp_max - ($tblock * $pos), ($planet_temp_max - ($tblock * $pos) + $tblock));
        $tempMin = $temp - $planet_temp_diff;
        $tempMax = $temp + $planet_temp_diff;

        $this->planetRepo->add(
            $entity,
            $type,
            $fields,
            $imageNumber,
            $tempMin,
            $tempMax
        );
    }

    private function createAsteroid(Cell $cell, int $pos = 0): void
    {
        $metal = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $crystal = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $plastic = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));

        $entity = $this->entityRepo->add($cell, EntityType::ASTEROID, $pos);
        $this->asteroidRepo->add($entity, $metal, $crystal, $plastic);
    }

    private function createNebula(Cell $cell, int $pos = 0): void
    {
        $crystal = random_int($this->config->param1Int('nebula_ress'), $this->config->param2Int('nebula_ress'));

        $entity = $this->entityRepo->add($cell, EntityType::NEBULA, $pos);
        $this->nebulaRepo->add($entity, $crystal);
    }

    private function createWormhole(Cell $cell): void
    {
        $persistent = (random_int(0, 100) <= $this->config->getInt('persistent_wormholes_ratio'));

        $entity = $this->entityRepo->add($cell, EntityType::WORMHOLE);
        $this->wormholeRepo->add($entity, $persistent);
    }

    private function createEmptySpace(Cell $cell, int $pos = 0): void
    {
        $entity = $this->entityRepo->add($cell, EntityType::EMPTY_SPACE, $pos);
        $this->emptySpaceRepo->add($entity);
    }

    private function removeOddWormhole(): int
    {
        $numWormholes = $this->wormholeRepo->count([]);
        if (fmod($numWormholes, 2) != 0) {
            $wormhole = $this->wormholeRepo->getOne();
            if ($wormhole) {
                $entity = $wormhole->getEntity();
                $this->entityRepo->updateCode($entity, EntityType::EMPTY_SPACE);
                $this->wormholeRepo->remove($wormhole);

                $this->emptySpaceRepo->add($entity);
                $numWormholes--;
            }
        }

        return $numWormholes;
    }

    private function linkWormholes(): void
    {
        $wormholes = [];
        $persistentWormholes = [];

        foreach ($this->wormholeRepo->findAll() as $wormhole) {
            if ($wormhole->isPersistent()) {
                $persistentWormholes[] = $wormhole;
            } else {
                $wormholes[] = $wormhole;
            }
        }

        // Shuffle wormholes
        shuffle($wormholes);
        shuffle($persistentWormholes);

        // Reduce list of persistent wormholes if uneven
        if (fmod(count($persistentWormholes), 2) != 0) {
            $lastWormHole = array_pop($persistentWormholes);
            $this->wormholeRepo->setPersistent($lastWormHole, false);
            $wormholes[] = $lastWormHole;
        }

        while (sizeof($wormholes) > 0) {
            $w1 = array_shift($wormholes);
            $w2 = array_pop($wormholes);

            $this->wormholeRepo->updateTarget($w1, $w2);
            $this->wormholeRepo->updateTarget($w2, $w1);
        }
    }

    /**
     * Replaces n asteroid/empty space cells
     * with new star systems
     */
    public function addStarSystems(int $quantity = 0): int
    {
        $entities = $this->entityRepo->findRandomByCodes([
            EntityType::EMPTY_SPACE,
            EntityType::ASTEROID,
        ], $quantity);

        $added = 0;
        foreach ($entities as $entity) {
            if ($entity->getCode() === EntityType::EMPTY_SPACE) {
                $this->emptySpaceRepo->remove($entity->getType());
            } elseif ($entity->getCode() === EntityType::ASTEROID) {
                $this->asteroidRepo->remove($entity->getType());
            }
            $this->entityRepo->save();

            $this->createStarSystem($entity->getCell(), $entity);
            $added++;
        }

        return $added;
    }

    private function createStarSystemBatch(Cell $cell): void
    {
        $num_planets_min = $this->config->param1Int('num_planets');
        $num_planets_max = $this->config->param2Int('num_planets');

        $type = $this->solTypes[array_rand($this->solTypes)];

        $entity = $this->addEntityBatch($cell, EntityType::STAR);
        $this->starRepo->add($entity, $type, false);

        $np = random_int($num_planets_min, $num_planets_max);
        for ($cnp = 1; $cnp <= $np; $cnp++) {
            $r = random_int(0, 100);
            if ($r <= $this->config->getInt('solsys_percent_planet')) {
                $this->createPlanetBatch($cell, $cnp, $np);
            } elseif ($r <= $this->config->getInt('solsys_percent_planet') + $this->config->getInt('solsys_percent_asteroids')) {
                $this->createAsteroidBatch($cell, $cnp);
            } else {
                $this->createEmptySpaceBatch($cell, $cnp);
            }
        }
    }

    private function createPlanetBatch(Cell $cell, int $pos, int $np): void
    {
        $planet_fields_min = $this->config->param1Int('planet_fields');
        $planet_fields_max = $this->config->param2Int('planet_fields');

        $planet_temp_min = $this->config->param1Int('planet_temp');
        $planet_temp_max = $this->config->param2Int('planet_temp');
        $planet_temp_diff = $this->config->getInt('planet_temp');
        $planet_temp_totaldiff = abs($planet_temp_min) + abs($planet_temp_max);

        $num_planet_images = $this->config->getInt('num_planet_images');

        $entity = $this->addEntityBatch($cell, EntityType::PLANET, $pos);

        $type = $this->planetTypes[array_rand($this->planetTypes)];
        $imageNumber = $type->getId() . "_" . random_int(1, $num_planet_images);

        $fields = random_int($planet_fields_min, $planet_fields_max);

        $tblock = (int) round($planet_temp_totaldiff / $np);
        $temp = random_int($planet_temp_max - ($tblock * $pos), ($planet_temp_max - ($tblock * $pos) + $tblock));
        $tempMin = $temp - $planet_temp_diff;
        $tempMax = $temp + $planet_temp_diff;

        $this->planetRepo->add($entity, $type, $fields, $imageNumber, $tempMin, $tempMax, false);
    }

    private function createAsteroidBatch(Cell $cell, int $pos = 0): void
    {
        $metal = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $crystal = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $plastic = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));

        $entity = $this->addEntityBatch($cell, EntityType::ASTEROID, $pos);
        $this->asteroidRepo->add($entity, $metal, $crystal, $plastic, false);
    }

    private function createNebulaBatch(Cell $cell, int $pos = 0): void
    {
        $crystal = random_int($this->config->param1Int('nebula_ress'), $this->config->param2Int('nebula_ress'));

        $entity = $this->addEntityBatch($cell, EntityType::NEBULA, $pos);
        $this->nebulaRepo->add($entity, $crystal, false);
    }

    private function createWormholeBatch(Cell $cell): void
    {
        $persistent = (random_int(0, 100) <= $this->config->getInt('persistent_wormholes_ratio'));

        $entity = $this->addEntityBatch($cell, EntityType::WORMHOLE);
        $this->wormholeRepo->add($entity, $persistent, null, false);
    }

    private function createEmptySpaceBatch(Cell $cell, int $pos = 0): void
    {
        $entity = $this->addEntityBatch($cell, EntityType::EMPTY_SPACE, $pos);
        $this->emptySpaceRepo->add($entity, 0, false);
    }

    private function addEntityBatch(Cell $cell, string $code, int $pos = 0): Entity
    {
        $entity = new Entity();
        $entity->setCell($cell);
        $entity->setCode($code);
        $entity->setPos($pos);

        $this->entityRepo->persist($entity);
        return $entity;
    }

    private function createStarSystemDirect($connection, int $cellId): void
    {
        $num_planets_min = $this->config->param1Int('num_planets');
        $num_planets_max = $this->config->param2Int('num_planets');

        $type = $this->solTypes[array_rand($this->solTypes)];
        
        $connection->insert('entities', ['cell_id' => $cellId, 'code' => EntityType::STAR, 'pos' => 0]);
        $entityId = (int) $connection->lastInsertId();
        $connection->insert('stars', ['id' => $entityId, 'type_id' => $type->getId()]);

        $np = random_int($num_planets_min, $num_planets_max);
        for ($cnp = 1; $cnp <= $np; $cnp++) {
            $r = random_int(0, 100);
            if ($r <= $this->config->getInt('solsys_percent_planet')) {
                $this->createPlanetDirect($connection, $cellId, $cnp, $np);
            } elseif ($r <= $this->config->getInt('solsys_percent_planet') + $this->config->getInt('solsys_percent_asteroids')) {
                $this->createAsteroidDirect($connection, $cellId, $cnp);
            } else {
                $this->createEmptySpaceDirect($connection, $cellId, $cnp);
            }
        }
    }

    private function createPlanetDirect($connection, int $cellId, int $pos, int $np): void
    {
        $planet_fields_min = $this->config->param1Int('planet_fields');
        $planet_fields_max = $this->config->param2Int('planet_fields');
        $planet_temp_min = $this->config->param1Int('planet_temp');
        $planet_temp_max = $this->config->param2Int('planet_temp');
        $planet_temp_diff = $this->config->getInt('planet_temp');
        $planet_temp_totaldiff = abs($planet_temp_min) + abs($planet_temp_max);
        $num_planet_images = $this->config->getInt('num_planet_images');

        $type = $this->planetTypes[array_rand($this->planetTypes)];
        $imageNumber = $type->getId() . "_" . random_int(1, $num_planet_images);
        $fields = random_int($planet_fields_min, $planet_fields_max);

        $tblock = (int) round($planet_temp_totaldiff / $np);
        $temp = random_int($planet_temp_max - ($tblock * $pos), ($planet_temp_max - ($tblock * $pos) + $tblock));
        $tempMin = $temp - $planet_temp_diff;
        $tempMax = $temp + $planet_temp_diff;

        $connection->insert('entities', ['cell_id' => $cellId, 'code' => EntityType::PLANET, 'pos' => $pos]);
        $entityId = (int) $connection->lastInsertId();
        $connection->insert('planets', [
            'id' => $entityId,
            'planet_type_id' => $type->getId(),
            'planet_fields' => $fields,
            'planet_image' => $imageNumber,
            'planet_temp_from' => $tempMin,
            'planet_temp_to' => $tempMax
        ]);
    }

    private function createAsteroidDirect($connection, int $cellId, int $pos): void
    {
        $metal = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $crystal = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));
        $plastic = random_int($this->config->param1Int('asteroid_ress'), $this->config->param2Int('asteroid_ress'));

        $connection->insert('entities', ['cell_id' => $cellId, 'code' => EntityType::ASTEROID, 'pos' => $pos]);
        $entityId = (int) $connection->lastInsertId();
        $connection->insert('asteroids', [
            'id' => $entityId,
            'res_metal' => $metal,
            'res_crystal' => $crystal,
            'res_plastic' => $plastic
        ]);
    }

    private function createNebulaDirect($connection, int $cellId, int $pos): void
    {
        $crystal = random_int($this->config->param1Int('nebula_ress'), $this->config->param2Int('nebula_ress'));

        $connection->insert('entities', ['cell_id' => $cellId, 'code' => EntityType::NEBULA, 'pos' => $pos]);
        $entityId = (int) $connection->lastInsertId();
        $connection->insert('nebulas', ['id' => $entityId, 'res_crystal' => $crystal]);
    }

    private function createWormholeDirect($connection, int $cellId): void
    {
        $persistent = (random_int(0, 100) <= $this->config->getInt('persistent_wormholes_ratio')) ? 1 : 0;

        $connection->insert('entities', ['cell_id' => $cellId, 'code' => EntityType::WORMHOLE, 'pos' => 0]);
        $entityId = (int) $connection->lastInsertId();
        $connection->insert('wormholes', ['id' => $entityId, 'changed' => time(), 'persistent' => $persistent]);
    }

    private function createEmptySpaceDirect($connection, int $cellId, int $pos): void
    {
        $connection->insert('entities', ['cell_id' => $cellId, 'code' => EntityType::EMPTY_SPACE, 'pos' => $pos]);
        $entityId = (int) $connection->lastInsertId();
        $connection->insert('space', ['id' => $entityId, 'lastvisited' => 0]);
    }
}
