<?PHP
	class Backup
	{
		static public function create()
		{
			$cfg = Config::getInstance();
			$rtn = false;
						
			if (UNIX)
			{
				$log = "Starte Backup...\n";
				$tmr = timerStart();
				$log .= " Warte auf Mutex...";
				$mtx = new Mutex();
				$mtx->acquire();
				$log .= " Mutex erhalten in ".timerStop($tmr)."s, beginne Backup...\n\n";
				$tmr = timerStart();				
				
			 	// Alte Backups löschen
			 	$cmd = "find ".BACKUP_DIR." -name *.sql.gz -mtime +".$cfg->p1('backup')." -exec rm {} \;";
				passthru($cmd);
			 	$cmd = "find ".BACKUP_DIR." -name *.sql -mtime +".$cfg->p1('backup')." -exec rm {} \;";
				passthru($cmd);
		
				$file = BACKUP_DIR."/".DB_DATABASE."-".date("Y-m-d-H-i");
				$file_wo_path = DB_DATABASE."-".date("Y-m-d-H-i");
				$result = shell_exec("mysqldump -u".DB_USER." -h".DB_SERVER." -p".DB_PASSWORD." ".DB_DATABASE." > ".$file.".sql");
				if ($result=="")
				{
					if ($cfg->p2('backup')==1)
					{
						$result = shell_exec("gzip -9 --best ".$file.".sql");
						if ($result!="")
						{
							echo "Error while zipping Backup-Dump $file: $result\n";
						}
						else
						{
							$log.= "Backup erstellt! Grösse: ".byte_format(filesize($file.".sql"));						
							$rtn = true;
						}
					}
					else
					{
						$rtn = true;
					}
				}
				else
				{
					echo "Error while creating Backup-Dump $file: $result\n";		
					$log.= "FEHLER beim erstellen der Datei $file: $result";					
				}
				add_log (15,"[b]Backup[/b]\nGesamtdauer: ".timerStop($tmr)."\n\n".$log);			
				$mtx->release();					
			}
			else
			{
				echo "Die Backup-Funktion ist nur auf UNIX-Systemen verfügbar!";
			}
			return $rtn;
		}
		
		static public function restore($arg)
		{
			$rtn = false;
			if (UNIX)
			{
			 	$mtx = new Mutex();
				$mtx->acquire();
				$file = BACKUP_DIR."/".DB_DATABASE."-".$arg;
				if (file_exists($file.".sql.gz"))
				{
					$result = shell_exec("gunzip ".$file.".sql.gz");
					if ($result=="")
					{
						$result = shell_exec("mysql -u".DB_USER." -p".DB_PASSWORD." -h".DB_SERVER." ".DB_DATABASE." < ".$file.".sql");
						if ($result!="")
						{
							echo "Error while restoring backup: $result\n";
						}
						else
							$rtn = true;
						shell_exec("gzip ".$file.".sql");
					}
					else
						echo "Error while unzipping Backup-Dump $file: $result\n";
				}
				elseif (file_exists($file.".sql"))
				{
					$result = shell_exec("mysql -u".DB_USER." -p".DB_PASSWORD." -h".DB_SERVER." ".DB_DATABASE." < ".$file.".sql");
					if ($result!="")
						echo "Error while restoring backup: $result\n";
					else
						$rtn = true;
				}
				else
				{
					echo "Error: File $file not found!\n";	
				}
				
				add_log (15,"[b]Datenbank-Restore[/b]\n\nDie Datenbank wurde von der Quelle [b]".$file."[/b] wiederhergestellt!\n");			
				$mtx->release();			
			}
			else
			{
				echo "Die Backup-Funktion ist nur auf UNIX-Systemen verfügbar!";
			}
			return $rtn;
		}		
	}

?>