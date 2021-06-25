<?php

declare(strict_types=1);

namespace EtoA\Admin;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AdminUserServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['etoa.admin.user.repository'] = function (Container $pimple): AdminUserRepository {
            return new AdminUserRepository($pimple['db']);
        };
        $pimple['etoa.admin.role.manager'] = function (): AdminRoleManager {
            return new AdminRoleManager();
        };
        $pimple['etoa.admin.notes.repository'] = function (Container $pimple): AdminNotesRepository {
            return new AdminNotesRepository($pimple['db']);
        };
        $pimple['etoa.admin.session.repository'] = function (Container $pimple): AdminSessionRepository {
            return new AdminSessionRepository($pimple['db']);
        };
        $pimple['etoa.admin.session.manager'] = function (Container $pimple): AdminSessionManager {
            return new AdminSessionManager(
                $pimple['etoa.admin.session.repository'],
                $pimple['etoa.config.service'],
                $pimple['etoa.log.service']
            );
        };
    }
}
