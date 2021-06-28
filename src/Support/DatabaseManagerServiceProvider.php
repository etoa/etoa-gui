<?php

declare(strict_types=1);

namespace EtoA\Support;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabaseManagerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.db.manager.repository'] = function (Container $pimple): DatabaseManagerRepository {
            return new DatabaseManagerRepository($pimple['db']);
        };
    }
}
