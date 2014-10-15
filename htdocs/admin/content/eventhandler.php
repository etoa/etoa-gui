<?PHP

	//
	// Cronjob
	//
	if ($sub=="cronjob")
	{
		// Activate update system
		if (isset($_GET['activateupdate']) && $_GET['activateupdate']==1)
		{
			Config::getInstance()->set("update_enabled",1);
			$tpl->assign('msg', "Updates aktiviert!");
		}
	
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
		
		$tpl->setView('admin/cronjob');
		$tpl->assign('title', 'Periodische Updates (Cronjob)');
	}
	
	//
	// Updates
	//
	else if($sub=='updates')
	{
		// Update points
		if (isset($_GET['action']) && $_GET['action']=="points")
		{
			ob_start();
			echo "[b]Punkte-Update[/b]\n";
			$mtx = new Mutex();
			$mtx->acquire();
			$num = Ranking::calc(true);
	    	Ranking::calcTitles();
			$mtx->release();
	    	$d = $num[1]/$num[0];
	    	echo "Die Punkte von ".$num[0]." Spielern wurden aktualisiert!\nEin Spieler hat durchschnittlich ".nf($d)." Punkte!";
			$_SESSION['update_results'] = ob_get_clean();
			forward('?page='.$page.'&sub='.$sub);
		}
		// Minute update
		if (isset($_GET['action']) && $_GET['action']=="update_minute")
		{
			ob_start();
			echo "[b]Minuten-Update[/b]\n";
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo update_minute();
			$_SESSION['update_results'] = ob_get_clean();
			forward('?page='.$page.'&sub='.$sub);
		}
		// 5 minutes update
		if (isset($_GET['action']) && $_GET['action']=="update_5minute")
		{
			ob_start();
			echo "[b]5-Minuten-Update[/b]\n";
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo update_5minute();
			$_SESSION['update_results'] = ob_get_clean();
			forward('?page='.$page.'&sub='.$sub);
		}
		// 30 minutes update
		if (isset($_GET['action']) && $_GET['action']=="update_30minute")
		{
			ob_start();
			echo "[b]30-Minuten-Update[/b]\n";
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo update_30minute();
			$_SESSION['update_results'] = ob_get_clean();
			forward('?page='.$page.'&sub='.$sub);
		}
		// Hourly update
		if (isset($_GET['action']) && $_GET['action']=="update_hour")
		{
			ob_start();
			echo "[b]Stunden-Update[/b]\n";
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo update_hour();
			$_SESSION['update_results'] = ob_get_clean();
			forward('?page='.$page.'&sub='.$sub);
		}
		// Daily update
		if (isset($_GET['action']) && $_GET['action']=="update_day")
		{
			ob_start();
			echo "[b]Tages-Update[/b]\n";
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo update_day();
			$_SESSION['update_results'] = ob_get_clean();
			forward('?page='.$page.'&sub='.$sub);
		}
		// Monthly update
		if (isset($_GET['action']) && $_GET['action']=="update_month")
		{
			ob_start();
			echo "[b]Monats-Update[/b]\n";
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo update_month();
			$_SESSION['update_results'] = ob_get_clean();
			forward('?page='.$page.'&sub='.$sub);
		}

		$tpl->setView('admin/updates');
		$tpl->assign('title', 'Manuelle Updates');
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