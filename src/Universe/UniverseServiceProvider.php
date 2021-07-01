<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\DatabaseManagerRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\Universe\Star\StarRepository;
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

        $pimple[AsteroidsRepository::class] = function (Container $pimple): AsteroidsRepository {
            return new AsteroidsRepository($pimple['db']);
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

        $pimple[UniverseGenerator::class] = function (Container $pimple): UniverseGenerator {
            return new UniverseGenerator(
                $pimple[ConfigurationService::class],
                $pimple[SolarTypeRepository::class],
                $pimple[PlanetTypeRepository::class],
                $pimple[CellRepository::class],
                $pimple[EntityRepository::class],
                $pimple[StarRepository::class],
                $pimple[PlanetRepository::class],
                $pimple[AsteroidsRepository::class],
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
    }
}
