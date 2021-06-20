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

        $pimple['etoa.universe.solar_type.repository'] = function (Container $pimple): SolarTypeRepository {
            return new SolarTypeRepository($pimple['db']);
        };

        $pimple['etoa.universe.planet_type.repository'] = function (Container $pimple): PlanetTypeRepository {
            return new PlanetTypeRepository($pimple['db']);
        };
    }
}
