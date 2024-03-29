<?PHP

//Fehler ausgabe definiert
ini_set('arg_separator.output',  '&amp;');

// Set timezone
define('TIMEZONE', 'Europe/Zurich');
date_default_timezone_set(TIMEZONE);

// Load constants
if (!defined('ADMIN_MODE')) {
    define('ADMIN_MODE', false);
}
// Load functions
require_once __DIR__ . '/functions.inc.php';

// Load specific admin functions
if (ADMIN_MODE) {
    require_once __DIR__ . '/../admin/inc/admin_functions.inc.php';
}

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
        if (ADMIN_MODE) {
            forward('../');
        }
        require __DIR__ . "/install.inc.php";
        exit();
    }
}

