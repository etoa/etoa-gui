<?PHP

use EtoA\Admin\AdminSessionManager;
use EtoA\User\UserSessionManager;
use Pimple\Container;

/**
 * Remove old session logs of users and admins
 */
class CleanupSessionLogsTask implements IPeriodicTask
{
    private UserSessionManager $userSessionManager;

    private AdminSessionManager $sessionManager;

    function __construct(Container $app)
    {
        $this->userSessionManager = $app[UserSessionManager::class];
        $this->sessionManager = $app[AdminSessionManager::class];
    }

    function run()
    {
        $userSessions = $this->userSessionManager->cleanupLogs();
        $adminSessions = $this->sessionManager->cleanupLogs();
        return "$userSessions alte Spieler Session-Logs gelöscht, $adminSessions alte Admin Session-Logs gelöscht";
    }

    function getDescription()
    {
        return "Alte Session-Logs löschen";
    }
}
