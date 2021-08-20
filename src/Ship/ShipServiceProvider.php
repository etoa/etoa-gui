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

        $pimple[ShipDataRepository::class] = function (Container $pimple): ShipDataRepository {
            return new ShipDataRepository($pimple['db'], $pimple['db.cache']);
        };

        $pimple[ShipCategoryRepository::class] = function (Container $pimple): ShipCategoryRepository {
            return new ShipCategoryRepository($pimple['db']);
        };

        $pimple[ShipRequirementRepository::class] = function (Container $pimple): ShipRequirementRepository {
            return new ShipRequirementRepository($pimple['db']);
        };

        $pimple[ShipQueueRepository::class] = function (Container $pimple): ShipQueueRepository {
            return new ShipQueueRepository($pimple['db']);
        };

        $pimple[ShipTransformRepository::class] = function (Container $pimple): ShipTransformRepository {
            return new ShipTransformRepository($pimple['db']);
        };
    }
}
