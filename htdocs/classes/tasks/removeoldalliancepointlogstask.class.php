<?PHP
	/**
	* Remove old appliance point logs
	*/
	class RemoveOldAlliancePointLogsTask implements IPeriodicTask
	{
		function run()
		{
			$nr = Alliance::cleanUpPoints();
			return "$nr alte Allianzpunkte-Logs gelöscht";
		}

		function getDescription() {
			return "Alte Allianzpunkte-Logs löschen";
		}
	}
?>