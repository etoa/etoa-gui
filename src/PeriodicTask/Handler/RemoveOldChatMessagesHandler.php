<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Chat\ChatManager;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldChatMessagesTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldChatMessagesHandler implements MessageHandlerInterface
{
    private ChatManager $chatManager;

    public function __construct(ChatManager $chatManager)
    {
        $this->chatManager = $chatManager;
    }

    public function __invoke(RemoveOldChatMessagesTask $task): SuccessResult
    {
        $nr = $this->chatManager->cleanUpMessages();

        return SuccessResult::create("$nr alte Chat Nachrichten gelöscht");
    }
}
