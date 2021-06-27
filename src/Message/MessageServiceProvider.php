<?php

declare(strict_types=1);

namespace EtoA\Message;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MessageServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[MessageRepository::class] = function (Container $pimple): MessageRepository {
            return new MessageRepository($pimple['db']);
        };

        $pimple['etoa.user.message.repository'] = function (Container $pimple): MessageRepository {
            return $pimple[MessageRepository::class];
        };

        $pimple[MessageService::class] = function (Container $pimple): MessageService {
            return new MessageService(
                $pimple[MessageRepository::class],
                $pimple['etoa.config.service']
            );
        };

        $pimple[ReportRepository::class] = function (Container $pimple): ReportRepository {
            return new ReportRepository($pimple['db']);
        };
    }
}
