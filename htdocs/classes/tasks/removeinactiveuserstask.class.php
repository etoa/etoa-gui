<?PHP
	/**
	* Remove inactive users
	*/
	class RemoveInactiveUsersTask implements IPeriodicTask
	{
		function run()
		{
			$nr = Users::removeInactive();
			return "$nr inaktive User gelöscht";
		}

		function getDescription() {
			return "Inaktive User löschen";
		}
	}
?>