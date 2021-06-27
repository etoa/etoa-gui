<?php

declare(strict_types=1);

namespace EtoA\Universe;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UniverseServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.universe.cell.repository'] = function (Container $pimple): CellRepository {
            return new CellRepository($pimple['db']);
        };

        $pimple['etoa.universe.entity.repository'] = function (Container $pimple): EntityRepository {
            return new EntityRepository($pimple['db']);
        };

        $pimple['etoa.universe.star.repository'] = function (Container $pimple): StarRepository {
            return new StarRepository($pimple['db']);
        };

        $pimple['etoa.universe.planet.repository'] = function (Container $pimple): PlanetRepository {
            return new PlanetRepository($pimple['db']);
        };

        $pimple['etoa.universe.asteroids.repository'] = function (Container $pimple): AsteroidsRepository {
            return new AsteroidsRepository($pimple['db']);
        };

        $pimple['etoa.universe.nebula.repository'] = function (Container $pimple): NebulaRepository {
            return new NebulaRepository($pimple['db']);
        };

        $pimple['etoa.universe.wormhole.repository'] = function (Container $pimple): WormholeRepository {
            return new WormholeRepository($pimple['db']);
        };

        $pimple['etoa.universe.empty_space.repository'] = function (Container $pimple): EmptySpaceRepository {
            return new EmptySpaceRepository($pimple['db']);
        };

        $pimple['etoa.universe.generator'] = function (Container $pimple): UniverseGenerator {
            return new UniverseGenerator(
                $pimple['etoa.config.service'],
                $pimple['etoa.user.repository'],
                $pimple['etoa.universe.solar_type.repository'],
                $pimple['etoa.universe.planet_type.repository'],
                $pimple['etoa.universe.cell.repository'],
                $pimple['etoa.universe.entity.repository'],
                $pimple['etoa.universe.star.repository'],
                $pimple['etoa.universe.planet.repository'],
                $pimple['etoa.universe.asteroids.repository'],
                $pimple['etoa.universe.nebula.repository'],
                $pimple['etoa.universe.wormhole.repository'],
                $pimple['etoa.universe.empty_space.repository'],
                $pimple['etoa.db.manager.repository']
            );
        };

        $pimple['etoa.universe.solar_type.repository'] = function (Container $pimple): SolarTypeRepository {
            return new SolarTypeRepository($pimple['db']);
        };

        $pimple['etoa.universe.planet_type.repository'] = function (Container $pimple): PlanetTypeRepository {
            return new PlanetTypeRepository($pimple['db']);
        };

    }
}
