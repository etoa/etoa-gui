<?php

declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MessageServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[MessageRepository::class] = function (Container $pimple): MessageRepository {
            return new MessageRepository($pimple['db']);
        };

        $pimple[MessageCategoryRepository::class] = function (Container $pimple): MessageCategoryRepository {
            return new MessageCategoryRepository($pimple['db']);
        };

        $pimple[MessageIgnoreRepository::class] = function (Container $pimple): MessageIgnoreRepository {
            return new MessageIgnoreRepository($pimple['db']);
        };

        $pimple[MessageService::class] = function (Container $pimple): MessageService {
            return new MessageService(
                $pimple[MessageRepository::class],
                $pimple[ConfigurationService::class]
            );
        };

        $pimple[ReportRepository::class] = function (Container $pimple): ReportRepository {
            return new ReportRepository($pimple['db']);
        };
    }
}
