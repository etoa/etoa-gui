<?PHP

$successMessage = null;
$errorMessage = null;
$infoMessage = null;
$persistentTables = fetchJsonConfig("persistent-tables.conf");

$action = $_POST['action'] ?? null;
if (isset($_POST['submit'])) {
    try {
        // Do the backup
        $dir = DBManager::getBackupDir();
        $gzip = Config::getInstance()->backup_use_gzip=="1";

        // Acquire mutex
        $mtx = new Mutex();
        $mtx->acquire();

        // Do the backup
        $log = DBManager::getInstance()->backupDB($dir, $gzip);

        // Release mutex
        $mtx->release();

        // Truncate tables
        if ($action === "truncate") {
            $mtx = new Mutex();
            $mtx->acquire();

            $tbls = DBManager::getInstance()->getAllTables();

            // Empty tables
            dbquery("SET FOREIGN_KEY_CHECKS=0;");
            $tc = 0;
            $emptyTables = [];
            foreach ($tbls as $t) {
                if (!in_array($t, $persistentTables['definitions']) && $t !== DBManager::SCHEMA_MIGRATIONS_TABLE) {
                    dbquery("TRUNCATE $t;");
                    $emptyTables[] = $t;
                    $tc++;
                }
            }
            if (count($emptyTables) > 0) {
                $infoMessage = 'Leere Tabellen: ' . implode(', ', $emptyTables);
            }
            dbquery('SET FOREIGN_KEY_CHECKS=1;');

            // Restore default config
            $cr = Config::restoreDefaults();
            $cfg->reload();

            $mtx->release();

            $successMessage = "$tc Tabellen geleert, $cr Einstellungen auf Standard zurückgesetzt!";
        }

        // Drop tables
        else if ($action === "drop") {
            $mtx = new Mutex();
            $mtx->acquire();

            // Drop tables
            $tc = DBManager::getInstance()->dropAllTables();

            // Load schema
            DBManager::getInstance()->migrate();

            // Load config default
            Config::restoreDefaults();
            $cfg->reload();

            $mtx->release();

            $successMessage = $tc . ' Tabellen gelöscht, Datenbankschema neu initialisiert!';
        }
    } catch (Exception $e) {
        // Release mutex
        $mtx->release();

        // Write log
        Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Reset fehlgeschlagen[/b]\nFehler: ".$e->getMessage());

        // Show message
        $errorMessage = 'Beim Ausführen des Resaet-Befehls trat ein Fehler auf: ' . $e->getMessage();
    }
}
echo $twig->render('admin/database/reset.html.twig', [
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'infoMessage' => $infoMessage,
]);
exit();
