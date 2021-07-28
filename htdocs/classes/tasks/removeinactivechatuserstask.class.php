<?PHP

use EtoA\Chat\ChatManager;
use Pimple\Container;

/**
 * Remove inactive chat users
 */
class RemoveInactiveChatUsersTask implements IPeriodicTask
{
    private ChatManager $chatManager;

    public function __construct(Container $app)
    {
        $this->chatManager = $app[ChatManager::class];
    }

    function run()
    {
        $nr = $this->chatManager->cleanUpUsers();
        return "$nr inaktive Chat-User gelöscht";
    }

    function getDescription()
    {
        return "Inaktive Chat-User entfernen";
    }
}
