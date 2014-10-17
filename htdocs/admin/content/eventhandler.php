<?PHP

	//
	// Cronjob
	//
	if ($sub=="cronjob")
	{
		$tpl->setView('admin/cronjob');
		$tpl->assign('title', 'Periodische Tasks (Cronjob)');
	
		// Activate update system
		if (isset($_GET['activateupdate']) && $_GET['activateupdate']==1)
		{
			Config::getInstance()->set("update_enabled",1);
			$tpl->assign('msg', "Tasks aktiviert!");
		}
	
		// Cron configuration
		if (UNIX)
		{
			$scriptname = dirname(realpath(__DIR__."/../"))."/scripts/update.php";
			$cronjob = "* * * * * ".$scriptname;
			$tpl->assign('cronjob', $cronjob);

			$tpl->assign('crontab_user', trim(shell_exec('id')));

			$crontab = array();
			exec("crontab -l", $crontab);
			$tpl->assign('crontab', implode("\n", $crontab));
			
			$tpl->assign('crontab_check', in_array($cronjob, $crontab));
		}
		else
		{
			$tpl->assign('warnmsg', "Cronjobs sind nur auf UNIX-Systemen verfügbar!");
		}
		
		// Load periodic tasks from configuration
		$periodictasks = array();
		foreach (fetchJsonConfig("periodictasks.conf") as $tc) {
			$klass = $tc['name'];
			$reflect = new ReflectionClass($klass);
			if ($reflect->implementsInterface('IPeriodicTask')) {
				$t = new $klass();
				$taskConfig['desc'] = $t->getDescription();
				$elements = preg_split('/\s+/', $tc['schedule']);
				$taskConfig['min'] = $elements[0];
				$taskConfig['hour'] = $elements[1];
				$taskConfig['dayofmonth'] = $elements[2];
				$taskConfig['month'] = $elements[3];
				$taskConfig['dayofweek'] = $elements[4];
			}
			$periodictasks[$tc['name']] = $taskConfig;
		}
		$tpl->assign('periodictasks', $periodictasks);
		
		// Run periodic task if requested
		if (!empty($_GET['runtask']))
		{
			if (isset($periodictasks[$_GET['runtask']])) {
				$title = "[b]Task: ".$periodictasks[$_GET['runtask']]['desc']."[/b] (".$_GET['runtask'].")\n";
				ob_start();
				$tr = new PeriodicTaskRunner();
				$out = $tr->runTask($_GET['runtask'], 1);
				$_SESSION['update_results'] = $title.$out.ob_get_clean();
				Log::add(Log::F_UPDATES, Log::INFO, "Task [b]".$_GET['runtask']."[/b] manuell ausgeführt:\n".trim($out));
			}
			forward('?page='.$page.'&sub='.$sub);
		}
		// Handle result message
		if (!empty($_SESSION['update_results'])) {
			$tpl->assign('update_results', text2html($_SESSION['update_results']));
			unset($_SESSION['update_results']);
		}
	}
 
	else {

		$tpl->setView('admin/eventhandler');
		$tpl->assign('title', 'Eventhandler');

		if (UNIX)
		{
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
				$tpl->assign('log', $log);
			}
			else
			{
				$tpl->assign('errmsg', "Die Logdatei ".$cfg->daemon_logfile." kann nicht geöffnet werden!");
			}
		}
	}
?>