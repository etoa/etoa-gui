<?PHP
		echo "<h2>Backups</h2>";
	
		// Backup erstellen
		if (isset($_POST['create']))
		{
			echo "Erstelle Backup...<br/>";

			if (DBManager::getInstance()->backup())
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
			if (DBManager::getInstance()->backup())
			{
				$result = shell_exec("../scripts/restore.php ".$_GET['date']);
				if ($result=="")
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
			$cfg->set("backup",$_POST['backup_v'],$_POST['backup_p1'],$_POST['backup_p2']);
			$cfg->set("backup_time",$_POST['backup_time_v'],$_POST['backup_time_p1'],$_POST['backup_time_p2']);
			ok_msg("Einstellungen gespeichert");
		}
		
		echo $frm->begin();
		iBoxStart("Backup-Einstellungen");
		echo "Speicherpfad: <input type=\"text\" value=\"".$cfg->backup."\" name=\"backup_v\" size=\"50\" /><br/>
		Aufbewahrungsdauer: <input type=\"text\" value=\"".$cfg->backup->p1."\" name=\"backup_p1\" size=\"2\" /> Tage &nbsp; &nbsp;
		GZIP benutzen: <input type=\"radio\" name=\"backup_p2\" value=\"1\" ".($cfg->backup->p2==1 ? ' checked="checked"' : '')."/> Ja  
		<input type=\"radio\" name=\"backup_p2\" value=\"0\" ".($cfg->backup->p2==0 ? ' checked="checked"' : '')."/> Nein<br/>
		Intervall: <select name=\"backup_time_v\">";
		for ($i=1;$i<=24;$i++)
		{
			echo "<option value=\"".$i."\" ".($cfg->backup_time->v==$i ? ' selected="selected"':'').">".$i."</option>";
		}
		echo "</select/> Stunden &nbsp;&nbsp; Startzeit: <select name=\"backup_time_p1\">";
		for ($i=0;$i<24;$i++)
		{
			echo "<option value=\"".$i."\" ".($cfg->backup_time->p1==$i ? ' selected="selected"':'').">".$i."</option>";
		}
		echo "</select/>:<select name=\"backup_time_p2\">";
		for ($i=0;$i<60;$i++)
		{
			echo "<option value=\"".$i."\" ".($cfg->backup_time->p2==$i ? ' selected="selected"':'').">".$i."</option>";
		}
		echo "</select/>";
		
		echo "&nbsp;&nbsp;  <input type=\"submit\" value=\"Speichern\" name=\"submit_changes\"  />";
		iBoxEnd();
		echo $frm->end();

		echo "<br/>Im Folgenden sind alle verfügbaren Backups aufgelistet. Backups werden durch ein Skript erstellt dass per Cronjob aufgerufen wird.<br/><br/>";

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "<input type=\"submit\" value=\"Neues Backup erstellen\" name=\"create\" /> &nbsp;
		 </form><br/>";
		if ($d = @opendir($cfg->backup))
		{
			$cnt=0;
			echo "<table class=\"tb\"><tr><th>Name</th><th>Grösse</th><th>Optionen</th></tr>";
			$bfiles=array();
			while ($f = readdir($d))
			{
				if (is_file($cfg->backup."/".$f) && stristr($f,".sql") && preg_match('/^'.DBManager::getInstance()->getDbName().'/i',$f)==1)
				{
					array_push($bfiles,$f);
				}
			}
			rsort($bfiles);

			foreach ($bfiles as $f)
			{
				$sr = round(filesize($cfg->backup."/".$f)/1024/1024,2);
				$date=substr($f,strpos($f,"-")+1,16);
				echo "<tr><td>".$f."</td>";
				echo "<td>".$sr." MB</td>";
				echo "<td>
					<a href=\"?page=$page&amp;sub=backup&amp;action=backuprestore&amp;date=$date\" onclick=\"return confirm('Soll die Datenbank mit den im Backup $date gespeicherten Daten &uuml;berschrieben werden?');\">Wiederherstellen</a> &nbsp; 
					<a href=\"dl.php?path=".base64_encode($cfg->backup."/".$f)."&amp;hash=".md5($cfg->backup."/".$f)."\">Download</a>
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
		else
			cms_err_msg("Das Verzeichnis ".$cfg->backup." wurde nicht gefunden!");

?>
