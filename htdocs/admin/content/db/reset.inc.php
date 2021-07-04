<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\DatabaseManagerRepository;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

$successMessage = null;
$errorMessage = null;
$infoMessage = null;
$persistentTables = fetchJsonConfig("persistent-tables.conf");

$action = $_POST['action'] ?? null;
if (isset($_POST['submit'])) {
    $mtx = new Mutex();

    try {
        // Do the backup
        $dir = DBManager::getBackupDir();
        $gzip = $config->getBoolean('backup_use_gzip');

        // Acquire mutex
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
            $emptyTables = [];
            foreach ($tbls as $t) {
                if (!in_array($t, $persistentTables['definitions'], true) && $t !== DBManager::SCHEMA_MIGRATIONS_TABLE) {
                    $emptyTables[] = $t;
                }
            }

            if (count($emptyTables) > 0) {
                /** @var DatabaseManagerRepository $dbManagerRepository */
                $dbManagerRepository = $app[DatabaseManagerRepository::class];
                $dbManagerRepository->truncateTables($emptyTables);

                $infoMessage = 'Leere Tabellen: ' . implode(', ', $emptyTables);
            }

            // Restore default config
            $cr = $config->restoreDefaults();
            $config->reload();

            $mtx->release();

            $successMessage = count($emptyTables) . " Tabellen geleert, $cr Einstellungen auf Standard zurückgesetzt!";
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
            $config->restoreDefaults();
            $config->reload();

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
