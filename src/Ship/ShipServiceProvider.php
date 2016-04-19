<?php

namespace EtoA\Ship;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ShipServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple)
    {
        $pimple['etoa.ship.datarepository'] = function (Container $pimple) {
            return new ShipDataRepository($pimple['db.querybuilder']);
        };
    }
}
