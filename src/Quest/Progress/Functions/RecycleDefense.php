<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Defense\Event\DefenseRecycle;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;

class RecycleDefense implements HandlerFunctionInterface
{
    const NAME = 'recycle-defense';

    public function handle(TaskInterface $task, DefenseRecycle $event)
    {
        return $task->getProgress() + $event->getCount();
    }

    public function getEventMap()
    {
        return [DefenseRecycle::RECYCLE_SUCCESS => 'handle'];
    }
}
