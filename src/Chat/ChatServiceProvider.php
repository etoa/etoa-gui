<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Text\TextRepository;
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

        $pimple[ChatLogRepository::class] = function (Container $pimple): ChatLogRepository {
            return new ChatLogRepository($pimple['db']);
        };

        $pimple[ChatRepository::class] = function (Container $pimple): ChatRepository {
            return new ChatRepository($pimple['db']);
        };

        $pimple[ChatManager::class] = function (Container $pimple): ChatManager {
            return new ChatManager(
                $pimple[ChatRepository::class],
                $pimple[ChatUserRepository::class],
                $pimple[TextRepository::class],
                $pimple[ConfigurationService::class],
            );
        };
    }
}
