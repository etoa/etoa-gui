<?PHP
	/**
	* Remove old messages
	*/
	class RemoveOldMessagesTask implements IPeriodicTask
	{
		function run()
		{
			Message::removeOld();
			return "Alte Nachrichten gelöscht";
		}

		function getDescription() {
			return "Alte Nachrichten löschen";
		}
	}
?>