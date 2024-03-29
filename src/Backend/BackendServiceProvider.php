<?php

declare(strict_types=1);

namespace EtoA\Backend;

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BackendServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[BackendMessageRepository::class] = function (Container $pimple): BackendMessageRepository {
            return new BackendMessageRepository($pimple['db']);
        };

        $pimple[BackendMessageService::class] = function (Container $pimple): BackendMessageService {
            return new BackendMessageService(
                $pimple[BackendMessageRepository::class]
            );
        };

        $pimple[EventHandlerManager::class] = function (Container $pimple): EventHandlerManager {
            return new EventHandlerManager(
                $pimple[ConfigurationService::class]
            );
        };
    }
}
