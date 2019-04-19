<?php declare(strict_types=1);

namespace EtoA\User;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['etoa.user.repository'] = function (Container $pimple) {
            return new UserRepository($pimple['db']);
        };
    }
}
