<?PHP

use EtoA\Admin\AdminSessionManager;

/**
 * Remove old session logs of users and admins
 */
class CleanupSessionLogsTask implements IPeriodicTask
{
	private AdminSessionManager $sessionManager;

	function __construct($app)
	{
		$this->sessionManager = $app['etoa.admin.session.manager'];
	}

	function run()
	{
		$unr = UserSession::cleanupLogs();
		$anr = $this->sessionManager->cleanupLogs();
		return "$unr alte Spieler Session-Logs gelöscht, $anr alte Admin Session-Logs gelöscht";
	}

	function getDescription()
	{
		return "Alte Session-Logs löschen";
	}
}
