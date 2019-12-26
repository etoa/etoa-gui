<?php declare(strict_types=1);

namespace EtoA\Planet;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PlanetServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.planet.repository'] = function (Container $pimple): PlanetRepository {
            return new PlanetRepository($pimple['db']);
        };
    }
}
