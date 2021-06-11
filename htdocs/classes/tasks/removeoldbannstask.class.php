<?PHP
	/**
	* Remove old, outdated banns
	*/
	class RemoveOldBannsTask implements IPeriodicTask
	{
		function run()
		{
			Users::removeOldBanns();
			return "Abgelaufene Sperren gelöscht";
		}

		function getDescription() {
			return "Abgelaufene Sperren löschen";
		}
	}
?>