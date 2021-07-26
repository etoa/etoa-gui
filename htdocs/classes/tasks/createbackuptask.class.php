<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\DatabaseManagerRepository;
use Pimple\Container;

/**
 * Create database backup
 */
class CreateBackupTask implements IPeriodicTask
{
    private ConfigurationService $config;
    private DatabaseManagerRepository $databaseManager;

    public function __construct(Container $app)
    {
        $this->config = $app[ConfigurationService::class];
        $this->databaseManager = $app[DatabaseManagerRepository::class];
    }

    function run()
    {
        $backupDir = DBManager::getBackupDir();
        $gzip = $this->config->getBoolean('backup_use_gzip');

        if ($backupDir != null) {
            // Remove old backup files
            $cleaned = DBManager::removeOldBackups($backupDir, $this->config->getInt('backup_retention_time'));

            $log = $this->databaseManager->backupDB($backupDir, $gzip);
            return $log . ", $cleaned alte Backup-Dateien gelöscht";
        } else {
            return "Backup konnte nicht erstellt werden, Backup Verzeichnis existiert nicht!";
        }
    }

    function getDescription()
    {
        return "Backup erstellen";
    }
}
