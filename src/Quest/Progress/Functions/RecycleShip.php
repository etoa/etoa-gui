<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Ship\Event\ShipRecycle;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class RecycleShip implements EventHandlerFunctionInterface
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
