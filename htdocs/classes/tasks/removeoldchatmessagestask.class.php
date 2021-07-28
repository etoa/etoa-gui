<?PHP

use EtoA\Chat\ChatManager;
use Pimple\Container;

/**
 * Chat-Cleanup
 */
class RemoveOldChatMessagesTask implements IPeriodicTask
{
    private ChatManager $chatManager;

    public function __construct(Container $app)
    {
        $this->chatManager = $app[ChatManager::class];
    }

    function run()
    {
        $nr = $this->chatManager->cleanUpMessages();
        return "$nr alte Chat Nachrichten gelöscht";
    }

    function getDescription()
    {
        return "Alte Chat-Nachrichten löschen";
    }
}
