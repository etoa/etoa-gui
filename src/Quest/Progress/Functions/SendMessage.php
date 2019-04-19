<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Message\Event\MessageSend;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class SendMessage implements EventHandlerFunctionInterface
{
    const NAME = 'send-message';

    public function handle(TaskInterface $task, MessageSend $event)
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap()
    {
        return [MessageSend::SEND_SUCCESS => 'handle'];
    }
}
