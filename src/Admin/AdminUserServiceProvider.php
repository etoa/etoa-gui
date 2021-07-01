<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AdminUserServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[AdminUserRepository::class] = function (Container $pimple): AdminUserRepository {
            return new AdminUserRepository($pimple['db']);
        };
        $pimple[AdminRoleManager::class] = function (): AdminRoleManager {
            return new AdminRoleManager();
        };
        $pimple[AdminNotesRepository::class] = function (Container $pimple): AdminNotesRepository {
            return new AdminNotesRepository($pimple['db']);
        };
        $pimple['etoa.admin.session.repository'] = function (Container $pimple): AdminSessionRepository {
            return new AdminSessionRepository($pimple['db']);
        };
        $pimple['etoa.admin.session.manager'] = function (Container $pimple): AdminSessionManager {
            return new AdminSessionManager(
                $pimple['etoa.admin.session.repository'],
                $pimple[ConfigurationService::class]
            );
        };
    }
}
