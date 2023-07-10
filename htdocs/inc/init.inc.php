<?PHP

//Fehler ausgabe definiert
ini_set('arg_separator.output', '&amp;');

// Set timezone
define('TIMEZONE', 'Europe/Zurich');
date_default_timezone_set(TIMEZONE);

// Load functions
require_once __DIR__ . '/functions.inc.php';

// Enable debug error reporting
if (isDebugEnabled()) {
    error_reporting(E_ALL);
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

// Include db config
$cbConfigFile = \EtoA\Core\DoctrineServiceProvider::CONFIG_FILE;
if (!configFileExists($cbConfigFile)) {
    if (isCLI()) {
        echo "Database configuration file $cbConfigFile does not exist!";
        exit(1);
    } else {
        require __DIR__ . "/install.inc.php";
        exit();
    }
}

