<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

$successMessage = null;
$errorMessage = null;
$actionOutput = null;
$eventHandlerPid = null;
$messageQueueSize = null;
$sysId = null;
$log = null;
if (isUnixOS()) {
    $pidfile = getAbsPath($config->get('daemon_pidfile'));
    $eventHandlerPid = EventHandlerManager::checkDaemonRunning($pidfile);

    if (isset($_GET['action'])) {
        $executable = $config->get('daemon_exe');
        if (!$executable) {
            $executable = realpath(RELATIVE_ROOT.'../eventhandler/target/etoad');
        }
        $instance = $config->get('daemon_instance');
        $configfile = realpath(RELATIVE_ROOT.'config/'.EVENTHANDLER_CONFIG_FILE_NAME);
        $pidfile = getAbsPath($config->get('daemon_pidfile'));

        if (file_exists($executable)) {
            if (file_exists($configfile)) {
                if ($_GET['action'] === "start") {
                    $out = EventHandlerManager::start($executable, $instance, $configfile, $pidfile);
                    $actionOutput = implode("\n", $out);
                    $successMessage = 'Dienst gestartet!';
                } else if ($_GET['action'] === "stop") {
                    $out = EventHandlerManager::stop($executable, $instance, $configfile, $pidfile);
                    $actionOutput = implode("\n", $out);
                    $successMessage = 'Dienst gestoppt!';
                }

                $eventHandlerPid = EventHandlerManager::checkDaemonRunning($pidfile);
            } else {
                $errorMessage = "Eventhandler Konfigurationsdatei $configfile nicht vorhanden!";
            }
        } else {
            $errorMessage = "Eventhandler Executable $executable nicht vorhanden!";
        }
    }

    $messageQueueSize = BackendMessage::getMessageQueueSize();
    $eventHandlerPid = EventHandlerManager::checkDaemonRunning($pidfile);

    if (function_exists('posix_uname')) {
        $un = posix_uname();
        $sysId = $un['sysname']." ".$un['release']." ".$un['version'];
    }

    // Warning: Open-Basedir restrictions may appply
    $logfile = $config->get('daemon_logfile');
    if (!preg_match('#^/#', $logfile)) {
        $logfile = '../'.$logfile;
    }
    if (is_file($logfile)) {
        $lf = fopen($logfile,"r");
        $log = [];
        while($l = fgets($lf)) {
            $log[] = $l;
        }
        fclose($lf);
        $log = array_reverse($log);
    } else {
        $log = "<em>Die Logdatei ".$config->get('daemon_logfile')." kann nicht geöffnet werden!</em>";
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
