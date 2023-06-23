<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Message\MessageService;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldMessagesTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldMessagesHandler implements MessageHandlerInterface
{
    private MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function __invoke(RemoveOldMessagesTask $task): SuccessResult
    {
        $nr = $this->messageService->removeOld();

        return SuccessResult::create("$nr alte Nachrichten gelöscht");
    }
}
