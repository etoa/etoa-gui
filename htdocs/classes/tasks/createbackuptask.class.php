<?PHP
	/**
	* Create database backup
	*/
	class CreateBackupTask implements IPeriodicTask 
	{		
		function run()
		{	
			DBManager::getInstance()->backupDB();
			return "Backup erstellt";
		}
		
		function getDescription() {
			return "Backup erstellen";
		}
	}
?>