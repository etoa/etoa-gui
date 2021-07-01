<?php declare(strict_types=1);

namespace EtoA\Chat;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ChatServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[ChatBanRepository::class] = function (Container $pimple): ChatBanRepository {
            return new ChatBanRepository($pimple['db']);
        };

        $pimple[ChatUserRepository::class] = function (Container $pimple): ChatUserRepository {
            return new ChatUserRepository($pimple['db']);
        };
    }
}
