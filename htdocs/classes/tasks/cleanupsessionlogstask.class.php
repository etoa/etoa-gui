<?PHP
	/**
	* Remove old session logs of users and admins
	*/
	class CleanupSessionLogsTask implements IPeriodicTask 
	{		
		function run()
		{
			$unr = UserSession::cleanupLogs();
			$anr = AdminSession::cleanupLogs();
			return "$unr alte Spieler Session-Logs gelöscht, $anr alte Admin Session-Logs gelöscht";
		}
		
		function getDescription() {
			return "Alte Session-Logs löschen";
		}
	}
?>