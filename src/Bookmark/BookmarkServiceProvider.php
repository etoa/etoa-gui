<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BookmarkServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[BookmarkRepository::class] = function (Container $pimple): BookmarkRepository {
            return new BookmarkRepository($pimple['db']);
        };

        $pimple[FleetBookmarkRepository::class] = function (Container $pimple): FleetBookmarkRepository {
            return new FleetBookmarkRepository($pimple['db']);
        };

        $pimple[BookmarkService::class] = function (Container $pimple): BookmarkService {
            return new BookmarkService(
                $pimple[BookmarkRepository::class],
                $pimple[PlanetService::class],
                $pimple[EntityRepository::class],
            );
        };
    }
}
