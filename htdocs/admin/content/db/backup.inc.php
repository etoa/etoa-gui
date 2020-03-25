<?PHP

// Backup erstellen
$successMessage = null;
$errorMessage = null;
if (isset($_POST['create'])) {
    try {
        $dir = DBManager::getBackupDir();
        $gzip = Config::getInstance()->backup_use_gzip=="1";

        // Acquire mutex
        $mtx = new Mutex();
        $mtx->acquire();

        // Do the backup
        $log = DBManager::getInstance()->backupDB($dir, $gzip);

        // Release mutex
        $mtx->release();

        // Write log
        Log::add(Log::F_SYSTEM, Log::INFO, "[b]Datenbank-Backup[/b]\n".$log);

        // Show message
        $successMessage = $log;
    } catch (Exception $e) {
        // Release mutex
        $mtx->release();

        // Write log
        Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Backup[/b]\nFehler: ".$e->getMessage());

        // Show message
        $errorMessage = 'Beim Ausführen des Backup-Befehls trat ein Fehler auf: ' . $e->getMessage();
    }
}

// Backup wiederherstellen
elseif (isset($_GET['action']) && $_GET['action'] === "backuprestore" && $_GET['date']!="") {
    // Sicherungskopie anlegen
    try {
        $dir = DBManager::getBackupDir();
        $restorePoint = $_GET['date'];
        $gzip = Config::getInstance()->backup_use_gzip=="1";

        try {
            // Acquire mutex
            $mtx = new Mutex();
            $mtx->acquire();

            // Backup current database
            $log = 'Anlegen einer Sicherungskopie: ';
            $log.= DBManager::getInstance()->backupDB($dir, $gzip);

            // Restore database
            $log.= "\nWiederherstellen der Datenbank: ";
            $log.= DBManager::getInstance()->restoreDB($dir, $restorePoint);

            // Release mutex
            $mtx->release();

            // Write log
            Log::add(Log::F_SYSTEM, Log::INFO, "[b]Datenbank-Restore[/b]\n".$log);

            // Show message
            $successMessage = 'Das Backup ' . $restorePoint . ' wurde wiederhergestellt und es wurde eine Sicherungskopie der vorherigen Daten angelegt!';
        } catch (Exception $e) {
            // Release mutex
            $mtx->release();

            // Write log
            Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Restore[/b]\nDie Datenbank konnte nicht vom Backup [b]".$restorePoint."[/b] aus dem Verzeichnis [b]".$dir."[/b] wiederhergestellt werden: ".$e->getMessage());

            // Show message
            $errorMessage = 'Beim Ausf&uuml;hren des Restore-Befehls trat ein Fehler auf! ' . $e->getMessage();
        }
    } catch (Exception $e) {
        $errorMessage = 'Beim Ausf&uuml;hren des Backup-Befehls trat ein Fehler auf! ' . $e->getMessage();
    }
}

$frm = new Form("bustn","?page=$page&amp;sub=$sub");
if (isset($_POST['submit_changes'])) {
    $cfg->set("backup_dir", $_POST['backup_dir']);
    $cfg->set("backup_retention_time", $_POST['backup_retention_time']);
    $cfg->set("backup_use_gzip", $_POST['backup_use_gzip']);
    $successMessage = 'Einstellungen gespeichert';
}

$dir = DBManager::getBackupDir();
$backupDir = null;
$backups = null;
if ($dir) {
    $backupDir = realpath($dir);

    $bfiles = DBManager::getInstance()->getBackupImages($dir, 0);

    $backups = [];
    foreach ($bfiles as $f) {
        $backups[] = [
            'filename' => $f,
            'date' => substr($f, strpos($f, '-') + 1, 16),
            'createdAt' => df(filectime($dir . '/' . $f)),
            'size' => byte_format(filesize($dir . '/' . $f)),
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
