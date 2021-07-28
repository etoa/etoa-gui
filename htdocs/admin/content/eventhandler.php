<?PHP

use EtoA\Backend\BackendMessageRepository;
use EtoA\Backend\EventHandlerManager;
use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var BackendMessageRepository */
$backendMessageRepository = $app[BackendMessageRepository::class];

/** @var EventHandlerManager */
$eventHandlerManager = $app[EventHandlerManager::class];

$successMessage = null;
$errorMessage = null;
$actionOutput = null;
$eventHandlerPid = null;
$messageQueueSize = null;
$sysId = null;
$log = null;
if (isUnixOS()) {
    $eventHandlerPid = $eventHandlerManager->checkDaemonRunning();

    if (isset($_GET['action'])) {
        try {
            if ($_GET['action'] === "start") {
                $out = $eventHandlerManager->start();
                $actionOutput = implode("\n", $out);
                $successMessage = 'Dienst gestartet!';
            } else if ($_GET['action'] === "stop") {
                $out = $eventHandlerManager->stop();
                $actionOutput = implode("\n", $out);
                $successMessage = 'Dienst gestoppt!';
            }
            $eventHandlerPid = $eventHandlerManager->checkDaemonRunning();
        } catch (Exception $ex) {
            $errorMessage = $ex->getMessage();
        }
    }

    $messageQueueSize = $backendMessageRepository->getMessageQueueSize();
    $eventHandlerPid = $eventHandlerManager->checkDaemonRunning();

    if (function_exists('posix_uname')) {
        $un = posix_uname();
        $sysId = $un['sysname'] . " " . $un['release'] . " " . $un['version'];
    }

    // Warning: Open-Basedir restrictions may appply
    $logfile = $config->get('daemon_logfile');
    if (!preg_match('#^/#', $logfile)) {
        $logfile = '../' . $logfile;
    }
    if (is_file($logfile)) {
        $lf = fopen($logfile, "r");
        $log = [];
        while ($l = fgets($lf)) {
            $log[] = $l;
        }
        fclose($lf);
        $log = array_reverse($log);
    } else {
        $log = "<em>Die Logdatei " . $config->get('daemon_logfile') . " kann nicht geöffnet werden!</em>";
    }
}

echo $twig->render('admin/eventhandler.html.twig', [
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'log' => $log,
    'eventHandlerPid' => $eventHandlerPid,
    'sysId' => $sysId,
    'messageQueueSize' => $messageQueueSize,
    'actionOutput' => $actionOutput,
]);
exit();
