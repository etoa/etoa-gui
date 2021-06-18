<?PHP

use EtoA\Admin\AdminSessionManager;

/**
 * Cleanup sessions
 */
class SessionCleanupTask implements IPeriodicTask
{
	private AdminSessionManager $sessionManager;

	function __construct($app)
	{
		$this->sessionManager = $app['etoa.admin.session.manager'];
	}

	function run()
	{
		UserSession::cleanup();
		$this->sessionManager->cleanup();
		return "Session cleanup";
	}

	function getDescription()
	{
		return "Session Cleanup";
	}
}
