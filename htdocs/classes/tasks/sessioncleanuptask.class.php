<?PHP
	/**
	* Cleanup sessions
	*/
	class SessionCleanupTask implements IPeriodicTask
	{
		function run()
		{
			AdminSessionManager $sessionManager = $app['etoa.admin.session.manager'];

			UserSession::cleanup();
			$sessionManager->cleanup();
			return "Session cleanup";
		}

		function getDescription() {
			return "Session Cleanup";
		}
	}
?>