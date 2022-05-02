<?php declare(strict_types=1);

namespace EtoA\Support\Balancing;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BalancingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.balancing_folder'] = __DIR__ . '/../../../data/balancing/';
        $pimple[Exporter::class] = function (Container $pimple): Exporter {
            return new Exporter($pimple['db'], $pimple['etoa.balancing_folder']);
        };
    }
}
