<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;

/**
 * Create database backup
 */
class CreateBackupTask implements IPeriodicTask
{
    private ConfigurationService $config;

    public function __construct(Container $app)
    {
        $this->config = $app[ConfigurationService::class];
    }

    function run()
    {
        $backupDir = DBManager::getBackupDir();
        $gzip = $this->config->getBoolean('backup_use_gzip');

        if ($backupDir != null) {
            // Remove old backup files
            $cleaned = DBManager::removeOldBackups($backupDir, $this->config->getInt('backup_retention_time'));

            $log = DBManager::getInstance()->backupDB($backupDir, $gzip);
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
