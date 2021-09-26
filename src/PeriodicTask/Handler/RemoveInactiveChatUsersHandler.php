<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Chat\ChatManager;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveInactiveChatUsersTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveInactiveChatUsersHandler implements MessageHandlerInterface
{
    private ChatManager $chatManager;

    public function __construct(ChatManager $chatManager)
    {
        $this->chatManager = $chatManager;
    }

    public function __invoke(RemoveInactiveChatUsersTask $task): SuccessResult
    {
        $nr = $this->chatManager->cleanUpUsers();

        return SuccessResult::create("$nr inaktive Chat-User gelöscht");
    }
}
