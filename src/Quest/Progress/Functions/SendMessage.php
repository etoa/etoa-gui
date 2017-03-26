<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Message\Event\MessageSend;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;

class SendMessage implements HandlerFunctionInterface
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
