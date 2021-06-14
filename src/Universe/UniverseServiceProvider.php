<?php

declare(strict_types=1);

namespace EtoA\Universe;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UniverseServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.universe.cell.repository'] = function (Container $pimple): CellRepository {
            return new CellRepository($pimple['db']);
        };
    }
}
