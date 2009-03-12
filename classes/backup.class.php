<?PHP
	class Backup
	{
		static public function create()
		{
			$cfg = Config::getInstance();
						
			if (UNIX)
			{
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
							return false;
						}
						return true;
					}
					return true;
				}
				else
					echo "Error while creating Backup-Dump $file: $result\n";		
			}
			else
				echo "Die Backup-Funktion ist nur auf UNIX-Systemen verfügbar!";
			return false;
		}		
	}

?>