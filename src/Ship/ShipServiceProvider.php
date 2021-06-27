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
        $pimple[ShipRepository::class] = function (Container $pimple): ShipRepository {
            return new ShipRepository($pimple['db']);
        };

        $pimple['etoa.ship.repository'] = function (Container $pimple): ShipRepository {
            return $pimple[ShipRepository::class];
        };

        $pimple[ShipDataRepository::class] = function (Container $pimple): ShipDataRepository {
            return new ShipDataRepository($pimple['db'], $pimple['db.cache']);
        };

        $pimple['etoa.ship.datarepository'] = function (Container $pimple): ShipDataRepository {
            return $pimple[ShipDataRepository::class];
        };

        $pimple[ShipCategoryRepository::class] = function (Container $pimple): ShipCategoryRepository {
            return new ShipCategoryRepository($pimple['db']);
        };

        $pimple['etoa.ship_category.repository'] = function (Container $pimple): ShipCategoryRepository {
            return $pimple[ShipCategoryRepository::class];
        };

        $pimple[ShipRequirementRepository::class] = function (Container $pimple): ShipRequirementRepository {
            return new ShipRequirementRepository($pimple['db']);
        };

        $pimple['etoa.ship_requirement.repository'] = function (Container $pimple): ShipRequirementRepository {
            return $pimple[ShipRequirementRepository::class];
        };
    }
}
