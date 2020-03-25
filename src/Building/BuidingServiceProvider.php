<?php declare(strict_types=1);

namespace EtoA\Building;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuidingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.building.repository'] = function (Container $pimple): BuildingRepository {
            return new BuildingRepository($pimple['db']);
        };
    }
}
