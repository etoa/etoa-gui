<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Alliance\Event\AllianceCreate;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class CreateAlliance implements EventHandlerFunctionInterface
{
    const NAME = 'create-alliance';

    public function handle(TaskInterface $task, AllianceCreate $event)
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap()
    {
        return [AllianceCreate::CREATE_SUCCESS => 'handle'];
    }
}
