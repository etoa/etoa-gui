<?PHP

use EtoA\Admin\AdminSessionManager;
use EtoA\User\UserSessionManager;
use Pimple\Container;

/**
 * Cleanup sessions
 */
class SessionCleanupTask implements IPeriodicTask
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
        $this->userSessionManager->cleanup();
        $this->sessionManager->cleanup();
        return "Session cleanup";
    }

    function getDescription()
    {
        return "Session Cleanup";
    }
}
