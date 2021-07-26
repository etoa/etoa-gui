<?php declare(strict_types=1);

namespace EtoA\BuddyList;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuddyListServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[BuddyListRepository::class] = function (Container $pimple): BuddyListRepository {
            return new BuddyListRepository($pimple['db']);
        };
    }
}
