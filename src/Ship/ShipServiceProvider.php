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
        $pimple['etoa.ship.repository'] = function (Container $pimple) {
            return new ShipRepository($pimple['db']);
        };
    }
}
