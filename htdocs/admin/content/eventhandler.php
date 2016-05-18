<?PHP

	$tpl->setView('eventhandler');
	$tpl->assign('title', 'Eventhandler');

	if (UNIX)
	{
		if (isset($_GET['action']))
		{
			$executable = $cfg->daemon_exe->v;
			if (empty($executable))
			{
				$executable = realpath(RELATIVE_ROOT.'../eventhandler/target/etoad');
			}
			$instance = $cfg->daemon_instance->v;
			$configfile = realpath(RELATIVE_ROOT.'config/'.EVENTHANDLER_CONFIG_FILE_NAME);
			$pidfile = $cfg->daemon_pidfile->v;

			if (file_exists($executable))
			{
				if (file_exists($configfile))
				{
					if ($_GET['action'] == "start")
					{
						$out = EventHandlerManager::start($executable, $instance, $configfile, $pidfile);
						$tpl->assign('action_output', implode("\n", $out));
						$tpl->assign('msg', "Dienst gestartet!");
					}
					else if ($_GET['action'] == "stop")
					{
						$out = EventHandlerManager::stop($executable, $instance, $configfile, $pidfile);
						$tpl->assign('action_output', implode("\n", $out));
						$tpl->assign('msg', "Dienst gestoppt!");
					}
					
					$tpl->assign("eventhandler_pid", EventHandlerManager::checkDaemonRunning($pidfile));
				}
				else
				{
					$tpl->assign('errmsg', "Eventhandler Konfigurationsdatei $configfile nicht vorhanden!");
				}
			}
			else
			{
				$tpl->assign('errmsg', "Eventhandler Executable $executable nicht vorhanden!");
			}
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
