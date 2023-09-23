<?PHP

$successMessage = null;
$errorMessage = null;
$warningMessage = null;
$actionOutput = null;
$eventHandlerPid = null;
$messageQueueSize = null;
$sysId = null;
$log = null;
if (UNIX) {
    $pidfile = getAbsPath($cfg->daemon_pidfile->v);
    $eventHandlerPid = EventHandlerManager::checkDaemonRunning($pidfile);

    if (isset($_GET['action'])) {
        $executable = $cfg->daemon_exe->v;
        if (empty($executable)) {
            $executable = realpath(RELATIVE_ROOT.'../eventhandler/target/etoad');
        }
        $instance = $cfg->daemon_instance->v;
        $configfile = realpath(RELATIVE_ROOT.'config/'.EVENTHANDLER_CONFIG_FILE_NAME);
        $pidfile = getAbsPath($cfg->daemon_pidfile->v);

        if (file_exists($executable)) {
            if (file_exists($configfile)) {
                if ($_GET['action'] === "start") {
                    $out = EventHandlerManager::start($executable, $instance, $configfile, $pidfile);
                    $actionOutput = implode("\n", $out);
                    $successMessage = 'Dienst gestartet!';
                    sleep(1); // wait for service startup
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
    $logfile = $cfg->daemon_logfile;
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
        $log = [
            "Die Logdatei ".$cfg->daemon_logfile." kann nicht geöffnet werden! Es kann sein, dass das Logging via Systemd konfiguriert ist.",
            "Bitte prüfe das Systemlog via Kommandozeile als root user mit dem folgenden Befehl: journalctl --no-pager  _COMM=etoad"
        ];
    }

    $pidDirectory = dirname(getAbsPath($cfg->daemon_pidfile->v));
    try {
        checkDirectoryWritePermissions($pidDirectory);
    } catch (Exception $e) {
        $warningMessage = $e->getMessage();
    }
}

echo $twig->render('admin/eventhandler.html.twig', [
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'warningMessage' => $warningMessage,
    'log' => $log,
    'eventHandlerPid' => $eventHandlerPid,
    'sysId' => $sysId,
    'messageQueueSize' => $messageQueueSize,
    'actionOutput' => $actionOutput,
]);
exit();
