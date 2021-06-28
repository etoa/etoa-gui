<?php declare(strict_types=1);

namespace EtoA\Market;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MarketServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[MarketRateRepository::class] = function (Container $pimple): MarketRateRepository {
            return new MarketRateRepository($pimple['db']);
        };
    }
}
