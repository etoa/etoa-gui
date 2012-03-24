<?PHP
	$tpl->setView('admin/eventhandler');
	$tpl->assign('title', 'Eventhandler');
	
	$un=posix_uname();
	$tpl->assign('sys_id', $un['sysname']." ".$un['release']." ".$un['version']);

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
?>