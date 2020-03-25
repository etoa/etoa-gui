<?php declare(strict_types=1);

namespace EtoA\Missile;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MissileServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.missile.repository'] = function (Container $pimple): MissileRepository {
            return new MissileRepository($pimple['db']);
        };

        $pimple['etoa.missile.datarepository'] = function (Container $pimple): MissileDataRepository {
            return new MissileDataRepository($pimple['db'], $pimple['db.cache']);
        };
    }
}
