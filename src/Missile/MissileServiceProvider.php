<?php declare(strict_types=1);

namespace EtoA\Missile;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MissileServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[MissileRepository::class] = function (Container $pimple): MissileRepository {
            return new MissileRepository($pimple['db']);
        };

        $pimple[MissileDataRepository::class] = function (Container $pimple): MissileDataRepository {
            return new MissileDataRepository($pimple['db'], $pimple['db.cache']);
        };

        $pimple[MissileRequirementRepository::class] = function (Container $pimple): MissileRequirementRepository {
            return new MissileRequirementRepository($pimple['db']);
        };

        $pimple[MissileFlightRepository::class] = function (Container $pimple): MissileFlightRepository {
            return new MissileFlightRepository($pimple['db']);
        };
    }
}
