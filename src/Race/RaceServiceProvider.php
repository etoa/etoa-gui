<?php declare(strict_types=1);

namespace EtoA\Race;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RaceServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple): void
    {
        $pimple['etoa.race.datarepository'] = function (Container $pimple): RaceDataRepository {
            return new RaceDataRepository($pimple['db']);
        };
    }
}
