<?PHP

	$tpl->setView('eventhandler');
	$tpl->assign('title', 'Eventhandler');

	if (UNIX)
	{
		if (isset($_GET['action']))
		{
			$executable = $cfg->daemon_exe->v;
			$instance = $cfg->daemon_instance->v;
			$pidfile = $cfg->daemon_pidfile->v;

			if ($_GET['action'] == "start")
			{
				$out = EventHandlerManager::start($executable, $instance, $pidfile);
				$tpl->assign('action_output', implode("\n", $out));
				$tpl->assign('msg', "Dienst gestartet!");
			}
			else if ($_GET['action'] == "stop")
			{
				$out = EventHandlerManager::stop($executable, $instance, $pidfile);
				$tpl->assign('action_output', implode("\n", $out));
				$tpl->assign('msg', "Dienst gestoppt!");
			}
			
			$tpl->assign("eventhandler_pid", EventHandlerManager::checkDaemonRunning($pidfile));
		}
	
		$tpl->assign('message_queue_size', BackendMessage::getMessageQueueSize());

		if (function_exists('posix_uname')) {
			$un=posix_uname();
			$tpl->assign('sys_id', $un['sysname']." ".$un['release']." ".$un['version']);
		}

		// Warning: Open-Basedir restrictions may appply
		if (is_file($cfg->daemon_logfile))
		{
			$lf = fopen($cfg->daemon_logfile,"r");
			$log = array();
			while($l = fgets($lf))
			{
				$log[] = $l;
			}
			fclose($lf);
			$tpl->assign('log', array_reverse($log));
		}
		else
		{
			$tpl->assign('errmsg', "Die Logdatei ".$cfg->daemon_logfile." kann nicht geöffnet werden!");
		}
	}

?>