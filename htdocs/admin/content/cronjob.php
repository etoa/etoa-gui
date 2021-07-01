<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

// Activate update system
$successMessage = null;
$errorMessage = null;
if (($_GET['activateupdate'] ?? null) == 1) {
    $config->set("update_enabled", 1);
    $successMessage = 'Tasks aktiviert!';
}

// Cron configuration
$cronjob = null;
$crontabUser = null;
if (isUnixOS()) {
    $scriptname = dirname(realpath(__DIR__."/../../"))."/bin/cronjob.php";
    $cronjob = '* * * * * ' . $scriptname;
    $crontabUser = trim(shell_exec('id'));

    // Get current crontab
    $crontab = [];
    exec("crontab -l", $crontab);

    // Enable cronjob
    if (isset($_GET['enablecronjob']) && !in_array($cronjob, $crontab, true)) {
        $out = shell_exec('(crontab -l 2>/dev/null; echo "'.$cronjob.'") | crontab -');
        if ($out) {
            $errorMessage = 'Cronjob konnte nicht aktiviert werden: ' . $out;
        }
        exec("crontab -l", $crontab);
    }

    $crontabCheck = in_array($cronjob, $crontab, true);
    $crontab = implode("\n", $crontab);
} else {
    $crontabCheck = false;
}

// Load periodic tasks from configuration
$periodictasks = [];
$time = time();
foreach (PeriodicTaskRunner::getScheduleFromConfig() as $tc) {
    $klass = $tc['name'];
    $reflect = new ReflectionClass($klass);
    if ($reflect->implementsInterface('IPeriodicTask')) {
        $elements = preg_split('/\s+/', $tc['schedule']);
        $t = new $klass($app);
        $taskConfig = [
            'desc' => $t->getDescription(),
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
        $title = "[b]Task: ".$periodictasks[$_GET['runtask']]['desc']."[/b] (".$_GET['runtask'].")\n";
        ob_start();
        $tr = new PeriodicTaskRunner($app);
        $out = $tr->runTask($_GET['runtask']);
        $_SESSION['update_results'] = $title.$out.ob_get_clean();
        Log::add(Log::F_UPDATES, Log::INFO, "Task [b]".$_GET['runtask']."[/b] manuell ausgeführt:\n".trim($out));
    }
    forward('?page='.$page);
}

// Run current or all tasks if requested
if (isset($_GET['run'])) {
    ob_start();
    $tr = new PeriodicTaskRunner($app);
    $log = '';
    foreach (PeriodicTaskRunner::getScheduleFromConfig() as $tc) {
        if ($_GET['run'] == "all" || PeriodicTaskRunner::shouldRun($tc['schedule'], $time)) {
            $log.= $tc['name'].': '.$tr->runTask($tc['name']);
        }
    }
    $log.= ob_get_clean();
    $log.= "\nTotal: ".$tr->getTotalDuration().' sec';
    $_SESSION['update_results'] = $log;
    Log::add(Log::F_UPDATES, Log::INFO, "Tasks manuell ausgeführt:\n".trim($log));
    forward('?page='.$page);
}

// Handle result message
$updateResults = null;
if (isset($_SESSION['update_results'])) {
    $updateResults = text2html($_SESSION['update_results']);
    unset($_SESSION['update_results']);
}

echo $twig->render('admin/cronjob.html.twig', [
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'periodicTasks' => $periodictasks,
    'crontabCheck' => $crontabCheck,
    'crontabUser' => $crontabUser,
    'crontab' => $crontab ?? null,
    'updateResults' => $updateResults,
]);
exit();
