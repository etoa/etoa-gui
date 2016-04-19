<?php

namespace EtoA\Building;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuildingServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple)
    {
        $pimple['etoa.building.datarepository'] = function (Container $pimple) {
            return new BuildingDataRepository($pimple['db.querybuilder']);
        };
    }
}
