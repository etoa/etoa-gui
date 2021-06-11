<?PHP
	/**
	* Chat-Cleanup
	*/
	class RemoveOldChatMessagesTask implements IPeriodicTask
	{
		function run()
		{
			$nr = ChatManager::cleanUpMessages();
			return "$nr alte Chat Nachrichten gelöscht";
		}

		function getDescription() {
			return "Alte Chat-Nachrichten löschen";
		}
	}
?>