<?php

declare(strict_types=1);

namespace EtoA\UI;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ship\ShipDataRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UIServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[ResourceBoxDrawer::class] = function (Container $pimple): ResourceBoxDrawer {
            return new ResourceBoxDrawer(
                $pimple[ConfigurationService::class],
                $pimple[UserPropertiesRepository::class]
            );
        };

        $pimple[EntityCoordinatesSelector::class] = function (Container $pimple): EntityCoordinatesSelector {
            return new EntityCoordinatesSelector($pimple[ConfigurationService::class]);
        };

        $pimple[UserSelector::class] = function (Container $pimple): UserSelector {
            return new UserSelector($pimple[UserRepository::class]);
        };

        $pimple[ShipSelector::class] = function (Container $pimple): ShipSelector {
            return new ShipSelector($pimple[ShipDataRepository::class]);
        };
    }
}
