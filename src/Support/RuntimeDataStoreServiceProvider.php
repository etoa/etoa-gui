<?php

declare(strict_types=1);

namespace EtoA\Support;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RuntimeDataStoreServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.runtime.datastore'] = function (Container $pimple): RuntimeDataStore {
            return new RuntimeDataStore($pimple['db']);
        };
    }
}
