<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseBackupService;
use EtoA\Support\StringUtils;
use Symfony\Component\Lock\LockFactory;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var DatabaseBackupService $databaseBackupService */
$databaseBackupService = $app[DatabaseBackupService::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

// Backup erstellen
$successMessage = null;
$errorMessage = null;
if (isset($_POST['create'])) {
    /** @var LockFactory $lockFactory */
    $lockFactory = $app[LockFactory::class];
    $lock = $lockFactory->createLock('db');

    try {
        $dir = $databaseBackupService->getBackupDir();
        $gzip = $config->getBoolean('backup_use_gzip');

        $lock->acquire(true);

        // Do the backup
        $log = $databaseBackupService->backupDB($dir, $gzip);

        $lock->release();

        // Write log
        $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "[b]Datenbank-Backup[/b]\n" . $log);

        // Show message
        $successMessage = $log;
    } catch (Exception $e) {
        $lock->release();

        // Write log
        $logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Backup[/b]\nFehler: " . $e->getMessage());

        // Show message
        $errorMessage = 'Beim Ausführen des Backup-Befehls trat ein Fehler auf: ' . $e->getMessage();
    }
}

// Backup wiederherstellen
elseif (isset($_GET['action']) && $_GET['action'] === "backuprestore" && $_GET['date'] != "") {
    // Sicherungskopie anlegen
    try {
        $dir = $databaseBackupService->getBackupDir();
        $restorePoint = $_GET['date'];
        $gzip = $config->getBoolean('backup_use_gzip');

        /** @var LockFactory $lockFactory */
        $lockFactory = $app[LockFactory::class];
        $lock = $lockFactory->createLock('db');

        try {
            $lock->acquire(true);

            // Backup current database
            $log = 'Anlegen einer Sicherungskopie: ';
            $log .= $databaseBackupService->backupDB($dir, $gzip);

            // Restore database
            $log .= "\nWiederherstellen der Datenbank: ";
            $log .= $databaseBackupService->restoreDB($dir, $restorePoint);

            // Write log
            $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "[b]Datenbank-Restore[/b]\n" . $log);

            // Show message
            $successMessage = 'Das Backup ' . $restorePoint . ' wurde wiederhergestellt und es wurde eine Sicherungskopie der vorherigen Daten angelegt!';
        } catch (Exception $e) {
            // Write log
            $logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Restore[/b]\nDie Datenbank konnte nicht vom Backup [b]" . $restorePoint . "[/b] aus dem Verzeichnis [b]" . $dir . "[/b] wiederhergestellt werden: " . $e->getMessage());

            // Show message
            $errorMessage = 'Beim Ausf&uuml;hren des Restore-Befehls trat ein Fehler auf! ' . $e->getMessage();
        } finally {
            $lock->release();
        }
    } catch (Exception $e) {
        $errorMessage = 'Beim Ausf&uuml;hren des Backup-Befehls trat ein Fehler auf! ' . $e->getMessage();
    }
}

if (isset($_POST['submit_changes'])) {
    $config->set("backup_dir", $_POST['backup_dir']);
    $config->set("backup_retention_time", $_POST['backup_retention_time']);
    $config->set("backup_use_gzip", $_POST['backup_use_gzip']);
    $successMessage = 'Einstellungen gespeichert';
}

$dir = $databaseBackupService->getBackupDir();
$backupDir = null;
$backups = null;
if ($dir !== null) {
    $backupDir = realpath($dir);

    $backupFiles = $databaseBackupService->getBackupImages($dir, false);

    $backups = [];
    foreach ($backupFiles as $f) {
        $backups[] = [
            'filename' => $f,
            'date' => substr($f, strpos($f, '-') + 1, 16),
            'createdAt' => StringUtils::formatDate(filectime($dir . '/' . $f)),
            'size' => StringUtils::formatBytes(filesize($dir . '/' . $f)),
            'downloadLink' => createDownloadLink($dir . '/' . $f),
        ];
    }
}

echo $twig->render('admin/database/backups.html.twig', [
    'errorMessage' => $errorMessage,
    'successMessage' => $successMessage,
    'backupDir' => $backupDir,
    'backups' => $backups,
]);
exit();
