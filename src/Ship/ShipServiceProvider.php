<?php declare(strict_types=1);

namespace EtoA\Ship;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ShipServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple): void
    {
        $pimple['etoa.ship.repository'] = function (Container $pimple): ShipRepository {
            return new ShipRepository($pimple['db']);
        };

        $pimple['etoa.ship.datarepository'] = function (Container $pimple): ShipDataRepository {
            return new ShipDataRepository($pimple['db'], $pimple['db.cache']);
        };
    }
}
