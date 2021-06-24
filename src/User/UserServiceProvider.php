<?php declare(strict_types=1);

namespace EtoA\User;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.user.repository'] = function (Container $pimple): UserRepository {
            return new UserRepository($pimple['db']);
        };

        $pimple['etoa.user.session.manager'] = function (Container $pimple): UserSessionManager {
            return new UserSessionManager($pimple['etoa.config.service']);
        };
    }
}
