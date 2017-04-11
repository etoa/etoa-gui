<?php

namespace EtoA\Planet;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PlanetServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple)
    {
        $pimple['etoa.planet.repository'] = function (Container $pimple) {
            return new PlanetRepository($pimple['db']);
        };
    }
}
