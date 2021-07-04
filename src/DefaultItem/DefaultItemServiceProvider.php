<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DefaultItemServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[DefaultItemRepository::class] = function (Container $pimple): DefaultItemRepository {
            return new DefaultItemRepository($pimple['db']);
        };
    }
}
