<?php declare(strict_types=1);

namespace EtoA\Defense;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DefenseServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.defense.repository'] = function (Container $pimple): DefenseRepository {
            return new DefenseRepository($pimple['db']);
        };
        $pimple['etoa.defense.datarepository'] = function (Container $pimple): DefenseDataRepository {
            return new DefenseDataRepository($pimple['db'], $pimple['db.cache']);
        };
    }
}
