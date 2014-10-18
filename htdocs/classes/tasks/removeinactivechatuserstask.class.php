<?PHP
	/**
	* Remove inactive chat users
	*/
	class RemoveInactiveChatUsersTask implements IPeriodicTask 
	{		
		function run()
		{
			$nr = ChatManager::cleanUpUsers();
			return "$nr inaktive Chat-User gelöscht";
		}
		
		function getDescription() {
			return "Inaktive Chat-User entfernen";
		}
	}
?>