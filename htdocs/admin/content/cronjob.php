<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\BBCodeUtils;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

// Load periodic tasks from configuration
$periodictasks = [];
$time = time();
foreach (PeriodicTaskRunner::getScheduleFromConfig() as $tc) {
    $klass = $tc['name'];
    $reflect = new ReflectionClass($klass);
    if ($reflect->implementsInterface('IPeriodicTask')) {
        $elements = preg_split('/\s+/', $tc['schedule']);
        $taskConfig = [
            'desc' => $klass::getDescription(),
            'min' => $elements[0],
            'hour' => $elements[1],
            'dayofmonth' => $elements[2],
            'month' => $elements[3],
            'dayofweek' => $elements[4],
            'current' => PeriodicTaskRunner::shouldRun($tc['schedule'], $time)
        ];
        $periodictasks[$tc['name']] = $taskConfig;
    }
}

// Run periodic task if requested
if (isset($_GET['runtask'])) {
    if (isset($periodictasks[$_GET['runtask']])) {
        $title = "[b]Task: " . $periodictasks[$_GET['runtask']]['desc'] . "[/b] (" . $_GET['runtask'] . ")\n";
        ob_start();
        $tr = new PeriodicTaskRunner($app);
        $out = $tr->runTask($_GET['runtask']);
        $_SESSION['update_results'] = $title . $out . ob_get_clean();
        $logRepository->add(LogFacility::UPDATES, LogSeverity::INFO, "Task [b]" . $_GET['runtask'] . "[/b] manuell ausgeführt:\n" . trim($out));
    }
    forward('/admin/cronjob/');
}

// Run current or all tasks if requested
if (isset($_GET['run'])) {
    ob_start();
    $tr = new PeriodicTaskRunner($app);
    $log = '';
    foreach (PeriodicTaskRunner::getScheduleFromConfig() as $tc) {
        if ($_GET['run'] == "all" || PeriodicTaskRunner::shouldRun($tc['schedule'], $time)) {
            $log .= $tc['name'] . ': ' . $tr->runTask($tc['name']);
        }
    }
    $log .= ob_get_clean();
    $log .= "\nTotal: " . $tr->getTotalDuration() . ' sec';
    $_SESSION['update_results'] = $log;
    $logRepository->add(LogFacility::UPDATES, LogSeverity::INFO, "Tasks manuell ausgeführt:\n" . trim($log));
    forward('/admin/cronjob/');
}

forward('/admin/cronjob/');
