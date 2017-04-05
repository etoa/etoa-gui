<?php

namespace EtoA\Building;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuidingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['etoa.building.repository'] = function (Container $pimple) {
            return new BuildingRepository($pimple['db']);
        };
    }
}
