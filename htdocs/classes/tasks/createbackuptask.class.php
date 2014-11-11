<?PHP
	/**
	* Create database backup
	*/
	class CreateBackupTask implements IPeriodicTask 
	{		
		function run()
		{	
			$cfg = Config::getInstance();
			
			$backupDir = DBManager::getBackupDir();
			$gzip = $cfg->backup_use_gzip=="1";
			
			if ($backupDir != null) 
			{
				// Remove old backup files
				$cleaned = DBManager::removeOldBackups($backupDir, $cfg->backup_retention_time);
				
				$log = DBManager::getInstance()->backupDB($backupDir, $gzip);
				return $log.", $cleaned alte Backup-Dateien gelöscht";
			}
			else
			{
				return "Backup konnte nicht erstellt werden, Backup Verzeichnis existiert nicht!";
			}
		}
		
		function getDescription() {
			return "Backup erstellen";
		}
	}
?>