<?PHP

use EtoA\Message\MessageService;
use Pimple\Container;

/**
 * Remove old messages
 */
class RemoveOldMessagesTask implements IPeriodicTask
{
    private MessageService $messageService;

    function __construct(Container $app)
    {
        $this->messageService = $app[MessageService::class];
    }

    function run()
    {
        $this->messageService->removeOld();
        return "Alte Nachrichten gelöscht";
    }

    function getDescription()
    {
        return "Alte Nachrichten löschen";
    }
}
