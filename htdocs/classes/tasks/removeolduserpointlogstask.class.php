<?PHP
	/**
	* Remove old user point log entries
	*/
	class RemoveOldUserPointLogsTask implements IPeriodicTask
	{
		function run()
		{
			$nr = Users::cleanUpPoints();
			return "$nr alte Userpunkte-Logs gelöscht";
		}

		function getDescription() {
			return "Alte Benuterpunkte-Logs löschen";
		}
	}
?>