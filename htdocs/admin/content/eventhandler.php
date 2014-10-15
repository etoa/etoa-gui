<?PHP

	//
	// Cronjob
	//
	if ($sub=="cronjob")
	{
		echo "<h1>Periodische Updates (Cronjob)</h1>";
		
		if (UNIX)
		{
			echo "
			<h3>Unix-Cronjob einrichten</h3>
			<ol>
			<li>Auf den Server einloggen (z.B. via SSH) resp. eine Shell/Kommandozeile öffnen</li>
			<li>Folgenden Befehl eingeben: <i>crontab -e</i>
			<li>Diese Zeile einfügen: ";
			$dname = dirname(realpath("../conf.inc.php"));
			echo "<p><span style=\"border:1px solid #fff;background:#000;padding:5px;\">";
			echo "* * * * * php ".$dname."/scripts/update.php";
			echo "</span></p></li>
			<li>Die Datei speichern und den Editor beenden
			<ul><li>Falls der Editor Vim ist: <i>ESC</i> drücken, <i>:wq</i> eingeben</li>
			<li>Falls der Editor Nano ist: <i>CTRL+X</i> drücken und Speichern mit <i>Y</i> bestätigen</li></ul>
			</li>
			<li>Resultat mit <i>crontab -l</i> prüfen</li>
			</ol>";
			echo "<h3>Aktuelle Crontab</h3>
			<p><div style=\"border:1px solid #fff;background:#000;padding:5px;\">";
			ob_start();
			echo "Crontab-User: ";
			passthru("id");
			echo "\n\n";
			passthru("crontab -l");
			echo nl2br(ob_get_clean());
			echo "</div></p>";
		}
		else
		{
			echo "Cronjobs sind nur auf UNIX-Systemen verfügbar!";
		}
		
	}
	
	//
	// Updates
	//
	else if($sub=='updates')
	{
		// Punkte aktualisieren
		if (isset($_GET['action']) && $_GET['action']=="points")
		{
			ob_start();
			$mtx = new Mutex();
			$mtx->acquire();
			$num = Ranking::calc(true);
	    	Ranking::calcTitles();
			$mtx->release();
	    	echo "Die Punkte von ".$num[0]." Spielern wurden aktualisiert!<br/>";
	    	$d = $num[1]/$num[0];
	    	echo "Ein Spieler hat durchschnittlich ".nf($d)." Punkte!";
			$tpl->assign('update_points_results', ob_get_clean());
		}

		if (isset($_GET['action']) && $_GET['action']=="update_minute")
		{
			ob_start();
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo text2html(update_minute());
			$tpl->assign('update_minute_results', ob_get_clean());
		}
		if (isset($_GET['action']) && $_GET['action']=="update_30minute")
		{
			ob_start();
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo text2html(update_30minute());
			$tpl->assign('update_30minute_results', ob_get_clean());
		}
		if (isset($_GET['action']) && $_GET['action']=="update_5minute")
		{
			ob_start();
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo text2html(update_5minute());
			$tpl->assign('update_5minute_results', ob_get_clean());
		}
		if (isset($_GET['action']) && $_GET['action']=="update_hour")
		{
			ob_start();
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo text2html(update_hour());
			$tpl->assign('update_hour_results', ob_get_clean());
		}
		if (isset($_GET['action']) && $_GET['action']=="update_day")
		{
			ob_start();
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo text2html(update_day());
			$tpl->assign('update_day_results', ob_get_clean());
		}
		if (isset($_GET['action']) && $_GET['action']=="update_month")
		{
			ob_start();
			include(RELATIVE_ROOT."inc/update.inc.php");
			echo text2html(update_month());
			$tpl->assign('update_month_results', ob_get_clean());
		}
			
		$tpl->setView('admin/updates');
		$tpl->assign('title', 'Manuelle Updates');

		/*
		echo '<b>Markt updaten:</b> 
		Fertige Auktionen und Angebote abschliessen
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=market\'" /><br/><br/>';
		echo '<b>Felder updaten:</b> 
		Felder der Planeten neu berechnen
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=fields\'" /><br/><br/>';
		echo '<b>Lager updaten:</b> 
		Lagerkapazitäten neu berechnen
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=store\'" /><br/><br/>';
		echo '<b>Ressourcen updaten:</b> 
		Ressourcen auf allen Planeten neu berechnen.
		<input type="button" value="Ausführen" onclick="document.location=\'?page='.$page.'&amp;sub='.$sub.'&amp;action=resources\'" /><br/><br/>';
		*/
		
	}
 
	else {

    $tpl->setView('admin/eventhandler');
    $tpl->assign('title', 'Eventhandler');
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
?>