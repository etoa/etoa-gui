<?php

declare(strict_types=1);

namespace EtoA\Message;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MessageServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.user.message.repository'] = function (Container $pimple): MessageRepository {
            return new MessageRepository($pimple['db']);
        };
    }
}
