<?PHP
		echo "<h2>Backups</h2>";
	
		// Backup erstellen
		if (isset($_POST['create']))
		{
			if (DBManager::getInstance()->backupDB())
			{
				ok_msg("Das Backup wurde erstellt!");
			}
			else
			{
				error_msg("Beim Ausf&uuml;hren des Backup-Befehls trat ein Fehler auf!");
			}
		}

		// Backup wiederherstellen
		elseif (isset($_GET['action']) && $_GET['action']=="backuprestore" && $_GET['date']!="")
		{
			// Sicherungskopie anlegen
			if (DBManager::getInstance()->backupDB())
			{
				if (DBManager::getInstance()->restoreDB($_GET['date']))
					echo "Das Backup ".$_GET['date']." wurde wiederhergestellt und es wurde eine Sicherungskopie der vorherigen Daten angelegt!<br/><br/>";
				else
					cms_err_msg("Beim Ausf&uuml;hren des Restore-Befehls trat ein Fehler auf! $result");
			}
			else
			{
				cms_err_msg("Beim Ausf&uuml;hren des Backup-Befehls trat ein Fehler auf! $result");
			}
		}
		
		$frm = new Form("bustn","?page=$page&amp;sub=$sub");
		if (isset($_POST['submit_changes'])) //$frm->checkSubmit("submit_changes")
		{
			$cfg->set("backup_dir", $_POST['backup_dir']);
			$cfg->set("backup_retention_time", $_POST['backup_retention_time']);
			$cfg->set("backup_use_gzip", $_POST['backup_use_gzip']);
			$cfg->set("backup_time_interval", $_POST['backup_time_interval']);
			$cfg->set("backup_time_hour", $_POST['backup_time_hour']);
			$cfg->set("backup_time_minute", $_POST['backup_time_minute']);
			ok_msg("Einstellungen gespeichert");
		}

		echo $frm->begin();
		iBoxStart("Backup-Einstellungen");
		echo "Speicherpfad: <input type=\"text\" value=\"".$cfg->backup_dir."\" name=\"backup_dir\" size=\"50\" /><br/>
		Aufbewahrungsdauer: <input type=\"text\" value=\"".$cfg->get('backup_retention_time')."\" name=\"backup_retention_time\" size=\"2\" /> Tage &nbsp; &nbsp;
		GZIP benutzen: <input type=\"radio\" name=\"backup_use_gzip\" value=\"1\" ".($cfg->get('backup_use_gzip')=="1" ? ' checked="checked"' : '')."/> Ja  
		<input type=\"radio\" name=\"backup_use_gzip\" value=\"0\" ".($cfg->get('backup_use_gzip')=="0" ? ' checked="checked"' : '')."/> Nein<br/>
		Intervall: <select name=\"backup_time_interval\">";
		for ($i=1;$i<=24;$i++)
		{
			echo "<option value=\"".$i."\" ".($cfg->get('backup_time_interval')==$i ? ' selected="selected"':'').">".$i."</option>";
		}
		echo "</select/> Stunden &nbsp;&nbsp; Startzeit: <select name=\"backup_time_hour\">";
		for ($i=0;$i<24;$i++)
		{
			echo "<option value=\"".$i."\" ".($cfg->get('backup_time_hour')==$i ? ' selected="selected"':'').">".$i."</option>";
		}
		echo "</select/>:<select name=\"backup_time_minute\">";
		for ($i=0;$i<60;$i++)
		{
			echo "<option value=\"".$i."\" ".($cfg->get('backup_time_minute')==$i ? ' selected="selected"':'').">".$i."</option>";
		}
		echo "</select/>";
		
		echo "&nbsp;&nbsp;  <input type=\"submit\" value=\"Speichern\" name=\"submit_changes\"  />";
		iBoxEnd();
		echo $frm->end();

		echo "<br/>Im Folgenden sind alle verfügbaren Backups aufgelistet. Backups werden durch ein Skript erstellt dass per Cronjob aufgerufen wird.<br/><br/>";

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "<input type=\"submit\" value=\"Neues Backup erstellen\" name=\"create\" /> &nbsp;
		 </form><br/>";
		if ($d = opendir($cfg->backup_dir))
		{
			$cnt=0;
			echo "<table class=\"tb\" style=\"width:auto;\"><tr><th>Name</th><th>Grösse</th><th>Optionen</th></tr>";
			$bfiles = DBManager::getInstance()->getBackupImages(0);

			foreach ($bfiles as $f)
			{
				$sr = round(filesize($cfg->backup_dir."/".$f)/1024/1024,2);
				$date=substr($f,strpos($f,"-")+1,16);
				echo "<tr><td>".$f."</td>";
				echo "<td>".$sr." MB</td>";
				echo "<td>
					<a href=\"?page=$page&amp;sub=backup&amp;action=backuprestore&amp;date=$date\" onclick=\"return confirm('Soll die Datenbank mit den im Backup $date gespeicherten Daten &uuml;berschrieben werden?');\">Wiederherstellen</a> &nbsp; 
					<a href=\"".createDownloadLink($cfg->backup_dir."/".$f)."\">Download</a>
				</td></tr>";
				$cnt++;
			}
			if ($cnt==0)
			{
				echo "<tr><td colspan=\"3\"><i>Es sind noch keine Dateien vorhanden!</i></td></tr>";
			}

			echo "</table>";
			closedir($d);
		}
		else {
			cms_err_msg("Das Verzeichnis ".$cfg->backup_dir." wurde nicht gefunden!");
		}
?>
