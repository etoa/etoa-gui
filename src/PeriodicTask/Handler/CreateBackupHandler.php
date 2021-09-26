<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\PeriodicTask\Result\ResultInterface;
use EtoA\PeriodicTask\Result\SkipResult;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CreateBackupTask;
use EtoA\Support\DB\DatabaseBackupService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateBackupHandler implements MessageHandlerInterface
{
    private DatabaseBackupService $databaseBackupService;
    private ConfigurationService $config;

    public function __construct(DatabaseBackupService $databaseBackupService, ConfigurationService $config)
    {
        $this->databaseBackupService = $databaseBackupService;
        $this->config = $config;
    }

    public function __invoke(CreateBackupTask $task): ResultInterface
    {
        $backupDir = $this->databaseBackupService->getBackupDir();
        $gzip = $this->config->getBoolean('backup_use_gzip');

        if ($backupDir === null) {
            return SkipResult::create("Backup konnte nicht erstellt werden, Backup Verzeichnis existiert nicht!");
        }

        // Remove old backup files
        $cleaned = $this->databaseBackupService->removeOldBackups($backupDir, $this->config->getInt('backup_retention_time'));

        $log = $this->databaseBackupService->backupDB($backupDir, $gzip);

        return SuccessResult::create($log . ", $cleaned alte Backup-Dateien gelöscht");
    }
}
