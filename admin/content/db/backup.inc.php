<?PHP
		echo "<h2>Backups</h2>";
	
		// Backup erstellen
		if (isset($_POST['create']))
		{
			echo "Erstelle Backup...<br/>";
			ob_start();
			passthru("../scripts/backup.php");
			$result = ob_get_contents();
			ob_end_clean();
			if ($result=="")
			{
				echo "Das Backup wurde erstellt!<br/><br/>";
			}
			else
			{
				echo "Beim Ausf&uuml;hren des Backup-Befehls trat ein Fehler auf!<br/>
				<div style=\"border:1px solid #fff;background:#335;padding:5px;\">
				$result
				</div><br/>";
			}
		}

		// Backup wiederherstellen
		elseif (isset($_GET['action']) && $_GET['action']=="backuprestore" && $_GET['date']!="")
		{
			$result = shell_exec("../scripts/backup.php"); // Sicherungskopie anlegen
			if ($result=="")
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


		if (Form::validate("bustn") && isset($_POST['submit_changes']))
		{
			$cfg->set("backup",$_POST['backup_v'],$_POST['backup_p1']);
			ok_msg("Gespeichert");
		}

		$frm = new Form("bustn","?page=$page&amp;sub=$sub");
		echo $frm->begin();
		iBoxStart("Backup-Einstellungen");
		echo "Speicherpfad: <input type=\"text\" value=\"".$cfg->get("backup")."\" name=\"backup_v\" size=\"50\" /><br/>
		Aufbewahrungsdauer: <input type=\"text\" value=\"".$cfg->p1("backup")."\" name=\"backup_p1\" size=\"2\" /> Tage 
		&nbsp; <input type=\"submit\" value=\"Speichern\" name=\"submit_changes\"  />";
		iBoxEnd();
		echo $frm->close();

		echo "Im Folgenden sind alle verfügbaren Backups aufgelistet. Backups werden durch ein Skript erstellt dass per Cronjob aufgerufen wird.<br/><br/>";

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "<input type=\"submit\" value=\"Neues Backup erstellen\" name=\"create\" /> &nbsp;
		 </form><br/>";
		if ($d = @opendir(BACKUP_DIR))
		{
			$cnt=0;
			echo "<table class=\"tb\"><tr><th>Name</th><th>Grösse</th><th>Optionen</th></tr>";
			$bfiles=array();
			while ($f = readdir($d))
			{
				if (is_file(BACKUP_DIR."/".$f) && stristr($f,".sql.gz"))
				{
					array_push($bfiles,$f);
				}
			}
			rsort($bfiles);

			foreach ($bfiles as $f)
			{
				$sr = round(filesize(BACKUP_DIR."/".$f)/1024/1024,2);
				$date=substr($f,strpos($f,"-")+1,16);
				echo "<tr><td>".$f."</td>";
				echo "<td>".$sr." MB</td>";
				echo "<td>
					<a href=\"?page=$page&amp;sub=backup&amp;action=backuprestore&amp;date=$date\" onclick=\"return confirm('Soll die Datenbank mit den im Backup $date gespeicherten Daten &uuml;berschrieben werden?');\">Wiederherstellen</a> &nbsp; 
					<a href=\"dl.php?path=".base64_encode(BACKUP_DIR."/".$f)."&amp;hash=".md5(BACKUP_DIR."/".$f)."\">Download</a>
				</td></tr>";
				$cnt++;
			}
			echo "</table>";
			closedir($d);
			if ($cnt==0)
				cms_err_msg("Es sind noch keine Dateien vorhanden!");
		}
		else
			cms_err_msg("Das Verzeichnis ".BACKUP_DIR." wurde nicht gefunden!");

?>