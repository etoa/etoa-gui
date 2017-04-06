<?php

namespace EtoA\Missile;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MissileServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple)
    {
        $pimple['etoa.missile.repository'] = function (Container $pimple) {
            return new MissileRepository($pimple['db']);
        };

        $pimple['etoa.missile.datarepository'] = function (Container $pimple) {
            return new MissileDataRepository($pimple['db'], $pimple['db.cache']);
        };
    }
}
