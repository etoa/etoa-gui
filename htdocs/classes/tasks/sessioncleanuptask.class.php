<?PHP

use EtoA\Admin\AdminSessionManager;
use Pimple\Container;

/**
 * Cleanup sessions
 */
class SessionCleanupTask implements IPeriodicTask
{
	private AdminSessionManager $sessionManager;

	function __construct(Container $app)
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
