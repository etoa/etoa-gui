<?PHP
	$tpl->setView('backups');
	$tpl->assign('subtitle', 'Backups');

	// Backup erstellen
	if (isset($_POST['create']))
	{
		try
		{
			$dir = DBManager::getBackupDir();
			$gzip = Config::getInstance()->backup_use_gzip=="1";
		
			// Acquire mutex
			$mtx = new Mutex();
			$mtx->acquire();
		
			// Do the backup
			$log = DBManager::getInstance()->backupDB($dir, $gzip);
			
			// Release mutex
			$mtx->release();
			
			// Write log
			Log::add(Log::F_SYSTEM, Log::INFO, "[b]Datenbank-Backup[/b]\n".$log);
			
			// Show message
			cms_success_msg($log);
		}
		catch (Exception $e)
		{
			// Release mutex
			$mtx->release();
		
			// Write log
			Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Backup[/b]\nFehler: ".$e->getMessage());
		
			// Show message
			cms_err_msg("Beim Ausf&uuml;hren des Backup-Befehls trat ein Fehler auf: ".$e->getMessage());
		}
	}

	// Backup wiederherstellen
	elseif (isset($_GET['action']) && $_GET['action']=="backuprestore" && $_GET['date']!="")
	{
		// Sicherungskopie anlegen
		try 
		{
			$dir = DBManager::getBackupDir();
			$restorePoint = $_GET['date'];
			$gzip = Config::getInstance()->backup_use_gzip=="1";
			
			try 
			{
				// Acquire mutex
				$mtx = new Mutex();
				$mtx->acquire();
				
				// Backup current database
				$log = "Anlegen einer Sicherungskopie: ";
				$log.= DBManager::getInstance()->backupDB($dir, $gzip);
			
				// Restore database
				$log.= "\nWiederherstellen der Datenbank: ";
				$log.= DBManager::getInstance()->restoreDB($dir, $restorePoint);

				// Release mutex
				$mtx->release();
			
				// Write log
				Log::add(Log::F_SYSTEM, Log::INFO, "[b]Datenbank-Restore[/b]\n".$log);

				// Show message
				cms_success_msg("Das Backup ".$restorePoint." wurde wiederhergestellt und es wurde eine Sicherungskopie der vorherigen Daten angelegt!");
			}
			catch (Exception $e) 
			{
				// Release mutex
				$mtx->release();

				// Write log
				Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Restore[/b]\nDie Datenbank konnte nicht vom Backup [b]".$restorePoint."[/b] aus dem Verzeichnis [b]".$dir."[/b] wiederhergestellt werden: ".$e->getMessage());
				
				// Show message
				cms_err_msg("Beim Ausf&uuml;hren des Restore-Befehls trat ein Fehler auf! ".$e->getMessage());
			}
		}
		catch (Exception $e)
		{
			cms_err_msg("Beim Ausf&uuml;hren des Backup-Befehls trat ein Fehler auf! ".$e->getMessage());
		}
	}
	
	$frm = new Form("bustn","?page=$page&amp;sub=$sub");
	if (isset($_POST['submit_changes']))
	{
		$cfg->set("backup_dir", $_POST['backup_dir']);
		$cfg->set("backup_retention_time", $_POST['backup_retention_time']);
		$cfg->set("backup_use_gzip", $_POST['backup_use_gzip']);
		cms_success_msg("Einstellungen gespeichert");
	}

	echo $frm->begin();
	echo "<fieldset><legend>Backup-Einstellungen</legend>";
	echo "Speicherpfad: <input type=\"text\" value=\"".$cfg->backup_dir."\" name=\"backup_dir\" size=\"50\" /> (leerlassen für Standardpfad)<br/>
	Aufbewahrungsdauer: <input type=\"text\" value=\"".$cfg->get('backup_retention_time')."\" name=\"backup_retention_time\" size=\"2\" /> Tage &nbsp; &nbsp;
	GZIP benutzen: <input type=\"radio\" name=\"backup_use_gzip\" value=\"1\" ".($cfg->get('backup_use_gzip')=="1" ? ' checked="checked"' : '')."/> Ja  
	<input type=\"radio\" name=\"backup_use_gzip\" value=\"0\" ".($cfg->get('backup_use_gzip')=="0" ? ' checked="checked"' : '')."/> Nein<br/>";
	echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_changes\"  />";
	echo "</fieldset>";
	echo $frm->end();

	echo "<p>Im Folgenden sind alle verfügbaren Backups aufgelistet. Backups werden automatisch durch einen periodischen Task erstellt.</p>";

	echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	echo "<p><input type=\"submit\" value=\"Neues Backup erstellen\" name=\"create\" /></p>
	 </form>";
	 
	$dir = DBManager::getBackupDir();
	if ($dir != null)
	{
		echo "<h3>Vorhandene Backups in ".realpath($dir)."</h3>";
		$cnt=0;
		echo "<table class=\"tb\" style=\"width:auto;\"><tr><th>Name</th><th>Erstellt</th><th>Grösse</th><th>Optionen</th></tr>";
		$bfiles = DBManager::getInstance()->getBackupImages($dir, 0);

		foreach ($bfiles as $f)
		{
			$date = substr($f,strpos($f,"-")+1,16);
			echo "<tr><td>".$f."</td>";
			echo "<td>".df(filectime($dir."/".$f))."</td>";
			echo "<td>".byte_format(filesize($dir."/".$f))."</td>";
			echo "<td>
				<a href=\"?page=$page&amp;sub=backup&amp;action=backuprestore&amp;date=$date\" onclick=\"return confirm('Soll die Datenbank mit den im Backup $date gespeicherten Daten &uuml;berschrieben werden?');\">Wiederherstellen</a> &nbsp; 
				<a href=\"".createDownloadLink($dir."/".$f)."\">Download</a>
			</td></tr>";
			$cnt++;
		}
		if ($cnt==0)
		{
			echo "<tr><td colspan=\"4\"><i>Es sind noch keine Dateien vorhanden!</i></td></tr>";
		}
		echo "</table>";
	}
	else {
		echo "<h3>Vorhandene Backups</h3>";
		cms_err_msg("Das Backupverzeichnis wurde nicht gefunden!");
	}
?>
