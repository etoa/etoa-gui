<?PHP

	$tpl->setView('cronjob');
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
	$time = time();
	foreach (PeriodicTaskRunner::getScheduleFromConfig() as $tc) {
		$klass = $tc['name'];
		$reflect = new ReflectionClass($klass);
		if ($reflect->implementsInterface('IPeriodicTask')) {
			$elements = preg_split('/\s+/', $tc['schedule']);
			$t = new $klass();
			$taskConfig = array(
				'desc' => $t->getDescription(),
				'min' => $elements[0],
				'hour' => $elements[1],
				'dayofmonth' => $elements[2],
				'month' => $elements[3],
				'dayofweek' => $elements[4],
				'current' => PeriodicTaskRunner::shouldRun($tc['schedule'], $time)
			);
			$periodictasks[$tc['name']] = $taskConfig;
		}
	}
	$tpl->assign('periodictasks', $periodictasks);
	
	// Run periodic task if requested
	if (!empty($_GET['runtask']))
	{
		if (isset($periodictasks[$_GET['runtask']])) {
			$title = "[b]Task: ".$periodictasks[$_GET['runtask']]['desc']."[/b] (".$_GET['runtask'].")\n";
			ob_start();
			$tr = new PeriodicTaskRunner();
			$out = $tr->runTask($_GET['runtask']);
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
?>