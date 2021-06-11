<?PHP
	/**
	* Cleanup sessions
	*/
	class SessionCleanupTask implements IPeriodicTask
	{
		function run()
		{
			UserSession::cleanup();
			AdminSession::cleanup();
			return "Session cleanup";
		}

		function getDescription() {
			return "Session Cleanup";
		}
	}
?>