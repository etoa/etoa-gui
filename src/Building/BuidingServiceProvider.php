<?php declare(strict_types=1);

namespace EtoA\Building;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuidingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[BuildingRepository::class] = function (Container $pimple): BuildingRepository {
            return new BuildingRepository($pimple['db']);
        };

        $pimple[BuildingDataRepository::class] = function (Container $pimple): BuildingDataRepository {
            return new BuildingDataRepository($pimple['db']);
        };

        $pimple[BuildingTypeDataRepository::class] = function (Container $pimple): BuildingTypeDataRepository {
            return new BuildingTypeDataRepository($pimple['db']);
        };

        $pimple['etoa.building_type.datarepository'] = function (Container $pimple): BuildingTypeDataRepository {
            return $pimple[BuildingTypeDataRepository::class];
        };
    }
}
