<?php

declare(strict_types=1);

namespace EtoA\Support;

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabaseManagerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple[DatabaseManagerRepository::class] = function (Container $pimple): DatabaseManagerRepository {
            return new DatabaseManagerRepository($pimple['db']);
        };

        $pimple[SchemaMigrationRepository::class] = function (Container $pimple): SchemaMigrationRepository {
            return new SchemaMigrationRepository($pimple['db']);
        };

        $pimple[DatabaseBackupService::class] = function (Container $pimple): DatabaseBackupService {
            return new DatabaseBackupService(
                $pimple[DatabaseManagerRepository::class],
                $pimple[ConfigurationService::class]
            );
        };

        $pimple[DatabaseMigrationService::class] = function (Container $pimple): DatabaseMigrationService {
            return new DatabaseMigrationService(
                $pimple[SchemaMigrationRepository::class],
                $pimple[DatabaseBackupService::class]
            );
        };
    }
}
