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
            return new DefenseDataRepository($pimple['db'], $pimple['db.cache']);
        };

        $pimple[DefenseCategoryRepository::class] = function (Container $pimple): DefenseCategoryRepository {
            return new DefenseCategoryRepository($pimple['db']);
        };

        $pimple['etoa.defense_category.repository'] = function (Container $pimple): DefenseCategoryRepository {
            return $pimple[DefenseCategoryRepository::class];
        };
    }
}
