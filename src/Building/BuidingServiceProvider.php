<?php

namespace EtoA\Building;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuidingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['etoa.building.buildlistrepository'] = function (Container $pimple) {
            return new BuildListRepository($pimple['db']);
        };
    }
}
