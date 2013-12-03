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