<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Ship\Event\ShipRecycle;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;

class RecycleShip implements HandlerFunctionInterface
{
    const NAME = 'recycle-ship';

    public function handle(TaskInterface $task, ShipRecycle $event)
    {
        return $task->getProgress() + $event->getCount();
    }

    public function getEventMap()
    {
        return [ShipRecycle::RECYCLE_SUCCESS => 'handle'];
    }
}
