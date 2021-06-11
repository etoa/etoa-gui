<?PHP
	/**
	* Remove users marked as deleted
	*/
	class RemoveDeletedUsersTask implements IPeriodicTask
	{
		function run()
		{
			$nr = Users::removeDeleted();
			return "$nr als gelöscht markierte User endgültig gelöscht";
		}

		function getDescription() {
			return "Zum Löschen markierte User löschen";
		}
	}
?>