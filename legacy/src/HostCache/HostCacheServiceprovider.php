<?php

declare(strict_types=1);

namespace EtoA\HostCache;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HostCacheServiceprovider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[HostCacheRepository::class] = function (Container $pimple): HostCacheRepository {
            return new HostCacheRepository($pimple['db']);
        };

        $pimple[NetworkNameService::class] = function (Container $pimple): NetworkNameService {
            return new NetworkNameService(
                $pimple[HostCacheRepository::class]
            );
        };
    }
}
