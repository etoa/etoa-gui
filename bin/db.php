#! /usr/bin/php -q
<?PHP

use EtoA\Core\Configuration\ConfigurationService;

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

if (!isset($app)) {
    $app = require __DIR__ .'/../src/app.php';
    $app->boot();
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
    $mtx = new Mutex();

    try
    {
        // Acquire mutex
        $mtx->acquire();

        if ($action == "reset") {
            echo "Dropping all tables:\n";
            DBManager::getInstance()->dropAllTables();
        }

        echo "Migrate database:\n";
        $cnt = DBManager::getInstance()->migrate();
        if ($cnt == 0) {
            echo "Database is up-to-date\n";
        }

        // Load config defaults
        if ($action == "reset") {

            /** @var ConfigurationService */
            $config = $app['etoa.config.service'];

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
    /** @var ConfigurationService */
    $config = $app['etoa.config.service'];

    $dir = DBManager::getBackupDir();
    $gzip = $config->getBoolean('backup_use_gzip');
    $mtx = new Mutex();

    try
    {
        // Acquire mutex
        $mtx->acquire();

        // Restore database
        $log = DBManager::getInstance()->backupDB($dir, $gzip);

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
            $log = DBManager::getInstance()->restoreDB($dir, $restorePoint);

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
        echo "\nUsage: ".$_SERVER['argv'][0]." ".$action." [restorepoint]\n\n";
        echo "Available restorepoints:\n\n";
        $dates = DBManager::getInstance()->getBackupImages($dir);
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
    echo "\nChecking tables:\n\n";
    try
    {
        $ores = DBManager::getInstance()->checkTables();
        while ($arr = mysql_fetch_assoc($ores))
        {
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
    echo "\nRepairing tables:\n\n";
    try
    {
        $ores = DBManager::getInstance()->repairTables(true);
        while ($arr = mysql_fetch_assoc($ores))
        {
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
// Any other action
//
else
{
    echo "\nUnknown action!\n";
    show_usage();
}

// DB schliessen
dbclose();
