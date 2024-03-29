<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Universe\Planet\PlanetRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MarketServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[MarketRateRepository::class] = function (Container $pimple): MarketRateRepository {
            return new MarketRateRepository($pimple['db']);
        };

        $pimple[MarketAuctionRepository::class] = function (Container $pimple): MarketAuctionRepository {
            return new MarketAuctionRepository($pimple['db']);
        };

        $pimple[MarketResourceRepository::class] = function (Container $pimple): MarketResourceRepository {
            return new MarketResourceRepository($pimple['db']);
        };

        $pimple[MarketShipRepository::class] = function (Container $pimple): MarketShipRepository {
            return new MarketShipRepository($pimple['db']);
        };

        $pimple[MarketRepository::class] = function (Container $pimple): MarketRepository {
            return new MarketRepository($pimple['db']);
        };

        $pimple[MarketHandler::class] = function (Container $pimple): MarketHandler {
            return new MarketHandler(
                $pimple[MarketRateRepository::class],
                $pimple[RuntimeDataStore::class],
                $pimple[PlanetRepository::class],
                $pimple[FleetRepository::class],
                $pimple[ConfigurationService::class]
            );
        };
    }
}
