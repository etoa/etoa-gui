<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\DB\DatabaseBackupService;
use Pimple\Container;

/**
 * Create database backup
 */
class CreateBackupTask implements IPeriodicTask
{
    private ConfigurationService $config;
    private DatabaseBackupService $databaseBackupService;

    public function __construct(Container $app)
    {
        $this->config = $app[ConfigurationService::class];
        $this->databaseBackupService = $app[DatabaseBackupService::class];
    }

    function run()
    {
        $backupDir = $this->databaseBackupService->getBackupDir();
        $gzip = $this->config->getBoolean('backup_use_gzip');

        if ($backupDir != null) {
            // Remove old backup files
            $cleaned = $this->databaseBackupService->removeOldBackups($backupDir, $this->config->getInt('backup_retention_time'));

            $log = $this->databaseBackupService->backupDB($backupDir, $gzip);
            return $log . ", $cleaned alte Backup-Dateien gelöscht";
        } else {
            return "Backup konnte nicht erstellt werden, Backup Verzeichnis existiert nicht!";
        }
    }

    public static function getDescription()
    {
        return "Backup erstellen";
    }
}
