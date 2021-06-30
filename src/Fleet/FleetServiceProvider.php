<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FleetServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple): void
    {
        $pimple[FleetRepository::class] = function (Container $pimple): FleetRepository {
            return new FleetRepository($pimple['db']);
        };
    }
}
