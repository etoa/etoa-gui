#! /usr/bin/php -q
<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;

require_once __DIR__ . '/../vendor/autoload.php';

//
// Diese Datei führt Aktionen aus die einmal pro Minute erledigt werden müssen
// Die Datei wird auf einer Shell aufgerufen (via Cron-Job realisiert)
// Sie wird jede Minute einmal aufgerufen
//

// Gamepfad feststellen
$grd = chdir(realpath(__DIR__ ."/../htdocs/"));

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
    DBManager::getInstance()->connect();
} catch (DBException $ex) {
    echo $ex;
    exit(1);
}

if (!isset($app)) {
    $app = require __DIR__ .'/../src/app.php';
    $app->boot();
}

// Load default values
require_once(RELATIVE_ROOT."inc/def.inc.php");

$args = array_splice($_SERVER['argv'], 1);

$verbose = in_array("-v", $args, true);

try {

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];
    /** @var LogRepository $logRepository */
    $logRepository = $app[LogRepository::class];

    // Prüfen ob Updates eingeschaltet sind
    if ($config->getBoolean('update_enabled'))
    {
        $time = time();

        // Execute tasks
        $tr = new PeriodicTaskRunner($app);
        $log = '';
        foreach (PeriodicTaskRunner::getScheduleFromConfig() as $tc) {
            if (PeriodicTaskRunner::shouldRun($tc['schedule'], $time)) {
                $log.= $tc['name'].': '.$tr->runTask($tc['name']);
            }
        }
        $log.= "\nTotal: ".$tr->getTotalDuration().' sec';

        // Write log
        if (LOG_UPDATES) {
            $severity = LogSeverity::INFO;
        } elseif ($tr->getTotalDuration() > LOG_UPDATES_THRESHOLD) {
            $severity = LogSeverity::WARNING;
        } else {
            $severity = LogSeverity::DEBUG;
        }
        $text = "Periodische Tasks (".date("d.m.Y H:i:s",$time)."):\n\n".$log;
        $logRepository->add(LogFacility::UPDATES, $severity, $text);

        if ($verbose) {
            echo $text;
        }
    }
} catch (DBException $ex) {
    echo $ex;
    exit(1);
}
