#! /usr/bin/php -q
<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\DatabaseManagerRepository;
use EtoA\Support\DatabaseMigrationService;

require_once __DIR__ . '/../vendor/autoload.php';

//
// Database maintenance
//

function show_usage() {
    echo "\nUsage: ".basename($_SERVER['argv'][0])." [action]\n\n";
    echo "Actions:\n";
    echo "  migrate    Migrate schema updates\n";
    echo "  reset      Drop all tables and rebuild database from scratch\n";
    echo "  backup     Backup database\n";
    echo "  restore    Restore database from backup\n";
    echo "  check      Check tables\n";
    echo "  repair     Repair defect tables\n";
    exit(1);
}

// Gamepfad feststellen
$grd = chdir(realpath(dirname(__FILE__)."/../htdocs/"));

// Check for command line
if (!isset($_SERVER['argv']))
{
    echo "Script has to be executed on command line!";
    exit(1);
}

// Initialisieren
$init = "inc/init.inc.php";
if (!@include($init))
{
    echo "Could not load bootstrap file ".getcwd()."/".($init)."\n";
    exit(1);
}

// Connect to database
try {
    dbconnect();
} catch (DBException $ex) {
    echo $ex;
    exit(1);
}

$args = array_splice($_SERVER['argv'], 1);
$action = array_shift($args);

if (!$action)
{
    show_usage();
}

$verbose = in_array("-v", $args, true);

//
// Migrate schema updates
//
if ($action == "migrate" || $action == "reset")
{
    if (!isset($app)) {
        $app = require __DIR__ .'/../src/app.php';
        $app->boot();
    }

    $mtx = new Mutex();

    try
    {
        // Acquire mutex
        $mtx->acquire();

        if ($action == "reset") {
            echo "Dropping all tables:\n";
            DBManager::getInstance()->dropAllTables();
        }

        /** @var DatabaseMigrationService */
        $databaseMigrationService = $app[DatabaseMigrationService::class];

        echo "Migrate database:\n";
        $cnt = $databaseMigrationService->migrate();
        if ($cnt == 0) {
            echo "Database is up-to-date\n";
        }

        // Load config defaults
        if ($action == "reset") {

            /** @var ConfigurationService */
            $config = $app[ConfigurationService::class];

            $config->restoreDefaults();
            $config->reload();
        }

        // Release mutex
        $mtx->release();

        exit(0);
    }
    catch (Exception $e)
    {
        // Release mutex
        $mtx->release();

        // Show output
        echo "Fehler: ".$e->getMessage();

        // Return code
        exit(1);
    }
}

//
// Backup database
//
else if ($action == "backup")
{
    if (!isset($app)) {
        $app = require __DIR__ .'/../src/app.php';
        $app->boot();
    }

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];

    $dir = DBManager::getBackupDir();
    $gzip = $config->getBoolean('backup_use_gzip');
    $mtx = new Mutex();

    try
    {
        // Acquire mutex
        $mtx->acquire();

        /** @var DatabaseManagerRepository */
        $databaseManager = $app[DatabaseManagerRepository::class];

        // Restore database
        $log = $databaseManager->backupDB($dir, $gzip);

        // Release mutex
        $mtx->release();

        // Write log
        Log::add(Log::F_SYSTEM, Log::INFO, "[b]Datenbank-Backup Skript[/b]\n".$log);

        // Show output
        if ($verbose) {
            echo $log;
        }

        exit(0);
    }
    catch (Exception $e)
    {
        // Release mutex
        $mtx->release();

        // Write log
        Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Backup Skript[/b]\nDie Datenbank konnte nicht in das Verzeichnis [b]".$dir."[/b] gesichert werden: ".$e->getMessage());

        // Show output
        echo "Fehler: ".$e->getMessage();

        // Return code
        exit(1);
    }
}

//
// Restore database
//
else if ($action == "restore")
{
    if (!isset($app)) {
        $app = require __DIR__ .'/../src/app.php';
        $app->boot();
    }

    /** @var DatabaseManagerRepository */
    $databaseManager = $app[DatabaseManagerRepository::class];

    $dir = DBManager::getBackupDir();

    // Check if restore point specified
    if (isset($args[0]))
    {
        $restorePoint = $args[0];
        $mtx = new Mutex();

        try
        {
            // Acquire mutex
            $mtx->acquire();

            // Restore database
            $log = $databaseManager->restoreDB($dir, $restorePoint);

            // Release mutex
            $mtx->release();

            // Write log
            Log::add(Log::F_SYSTEM, Log::INFO, "[b]Datenbank-Restore Skript[/b]\n".$log);

            // Show output
            if ($verbose) {
                echo $log;
            }

            exit(0);
        }
        catch (Exception $e)
        {
            // Release mutex
            $mtx->release();

            // Write log
            Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Restore Skript[/b]\nDie Datenbank konnte nicht vom Backup [b]".$restorePoint."[/b] aus dem Verzeichnis [b]".$dir."[/b] wiederhergestellt werden: ".$e->getMessage());

            // Show output
            echo "Fehler: ".$e->getMessage();

            // Return code
            exit(1);
        }
    }
    else
    {
        echo "\nUsage: ".$_SERVER['argv'][0]." ".$action." [restore_point]\n\n";
        echo "Available restore points:\n\n";
        $dates = $databaseManager->getBackupImages($dir);
        foreach ($dates as $f)
        {
            echo "$f\n";
        }
        exit(1);
    }
}

//
// Check database
//
else if ($action == "check")
{
    if (!isset($app)) {
        $app = require __DIR__ .'/../src/app.php';
        $app->boot();
    }

    /** @var DatabaseManagerRepository */
    $databaseManager = $app[DatabaseManagerRepository::class];

    echo "\nChecking tables:\n\n";
    try
    {
        $result = $databaseManager->checkTables();
        foreach ($result as $arr) {
            echo implode("\t", $arr)."\n";
        }
    }
    catch (Exception $e)
    {
        echo "Fehler: ".$e->getMessage();
        exit(1);
    }
}

//
// Repair database
//
else if ($action == "repair")
{
    if (!isset($app)) {
        $app = require __DIR__ .'/../src/app.php';
        $app->boot();
    }

    /** @var DatabaseManagerRepository */
    $databaseManager = $app[DatabaseManagerRepository::class];

    echo "\nRepairing tables:\n\n";
    try
    {
        $result = $databaseManager->repairTables();
        foreach ($result as $arr) {
            echo implode("\t", $arr)."\n";
        }
        Log::add(Log::F_SYSTEM, Log::INFO, count($result) . " Tabellen wurden manuell repariert!");
    }
    catch (Exception $e)
    {
        echo "Fehler: ".$e->getMessage();
        exit(1);
    }
}

//
// Any other action
//
else
{
    echo "\nUnknown action!\n";
    show_usage();
}

// DB schliessen
dbclose();
