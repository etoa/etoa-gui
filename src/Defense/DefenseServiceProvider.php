<?php declare(strict_types=1);

namespace EtoA\Defense;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DefenseServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[DefenseRepository::class] = function (Container $pimple): DefenseRepository {
            return new DefenseRepository($pimple['db']);
        };

        $pimple[DefenseDataRepository::class] = function (Container $pimple): DefenseDataRepository {
            return new DefenseDataRepository($pimple['db']);
        };

        $pimple[DefenseCategoryRepository::class] = function (Container $pimple): DefenseCategoryRepository {
            return new DefenseCategoryRepository($pimple['db']);
        };

        $pimple[DefenseQueueRepository::class] = function (Container $pimple): DefenseQueueRepository {
            return new DefenseQueueRepository($pimple['db']);
        };

        $pimple[DefenseRequirementRepository::class] = function (Container $pimple): DefenseRequirementRepository {
            return new DefenseRequirementRepository($pimple['db']);
        };
    }
}
