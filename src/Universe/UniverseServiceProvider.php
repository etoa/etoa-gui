<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\DatabaseManagerRepository;
use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use EtoA\Universe\Wormhole\WormholeService;
use EtoA\User\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UniverseServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[CellRepository::class] = function (Container $pimple): CellRepository {
            return new CellRepository($pimple['db']);
        };

        $pimple[EntityRepository::class] = function (Container $pimple): EntityRepository {
            return new EntityRepository($pimple['db']);
        };

        $pimple[StarRepository::class] = function (Container $pimple): StarRepository {
            return new StarRepository($pimple['db']);
        };

        $pimple[PlanetRepository::class] = function (Container $pimple): PlanetRepository {
            return new PlanetRepository($pimple['db']);
        };

        $pimple[AsteroidRepository::class] = function (Container $pimple): AsteroidRepository {
            return new AsteroidRepository($pimple['db']);
        };

        $pimple[NebulaRepository::class] = function (Container $pimple): NebulaRepository {
            return new NebulaRepository($pimple['db']);
        };

        $pimple[WormholeRepository::class] = function (Container $pimple): WormholeRepository {
            return new WormholeRepository($pimple['db']);
        };

        $pimple[EmptySpaceRepository::class] = function (Container $pimple): EmptySpaceRepository {
            return new EmptySpaceRepository($pimple['db']);
        };

        $pimple[WormholeService::class] = function (Container $pimple): WormholeService {
            return new WormholeService(
                $pimple[WormholeRepository::class],
                $pimple[EntityRepository::class],
                $pimple[EmptySpaceRepository::class],
                $pimple[ConfigurationService::class]
            );
        };

        $pimple[PlanetService::class] = function (Container $pimple): PlanetService {
            return new PlanetService(
                $pimple[PlanetRepository::class],
                $pimple[BuildingRepository::class],
                $pimple[ShipRepository::class],
                $pimple[DefenseRepository::class],
                $pimple[ConfigurationService::class],
            );
        };

        $pimple[UniverseGenerator::class] = function (Container $pimple): UniverseGenerator {
            return new UniverseGenerator(
                $pimple[ConfigurationService::class],
                $pimple[SolarTypeRepository::class],
                $pimple[PlanetTypeRepository::class],
                $pimple[CellRepository::class],
                $pimple[EntityRepository::class],
                $pimple[StarRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[AsteroidRepository::class],
                $pimple[NebulaRepository::class],
                $pimple[WormholeRepository::class],
                $pimple[EmptySpaceRepository::class]
            );
        };

        $pimple[UniverseResetService::class] = function (Container $pimple): UniverseResetService {
            return new UniverseResetService(
                $pimple[ConfigurationService::class],
                $pimple[UserRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[DatabaseManagerRepository::class]
            );
        };

        $pimple[SolarTypeRepository::class] = function (Container $pimple): SolarTypeRepository {
            return new SolarTypeRepository($pimple['db']);
        };

        $pimple[PlanetTypeRepository::class] = function (Container $pimple): PlanetTypeRepository {
            return new PlanetTypeRepository($pimple['db']);
        };

        $pimple[EntityService::class] = function (Container $pimple): EntityService {
            return new EntityService(
                $pimple[UserRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[StarRepository::class]
            );
        };
    }
}
