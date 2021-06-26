<?php declare(strict_types=1);

namespace EtoA\User;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[UserRepository::class] = function (Container $pimple): UserRepository {
            return new UserRepository($pimple['db']);
        };

        $pimple['etoa.user.repository'] = function (Container $pimple): UserRepository {
            return $pimple[UserRepository::class];
        };

        $pimple[UserSessionRepository::class] = function (Container $pimple): UserSessionRepository {
            return new UserSessionRepository($pimple['db']);
        };

        $pimple['etoa.user.session.repository'] = function (Container $pimple): UserSessionRepository {
            return $pimple[UserSessionRepository::class];
        };

        $pimple['etoa.user.session.manager'] = function (Container $pimple): UserSessionManager {
            return new UserSessionManager(
                $pimple['etoa.user.session.repository'],
                $pimple['etoa.config.service'],
                $pimple['etoa.user.repository']
            );
        };
    }
}
