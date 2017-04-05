<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileBuy;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class BuyMissile implements EventHandlerFunctionInterface
{
    const NAME = 'buy-missile';

    public function handle(TaskInterface $task, MissileBuy $event)
    {
        return $task->getProgress() + $event->getCount();
    }

    public function getEventMap()
    {
        return [MissileBuy::BUY_SUCCESS => 'handle'];
    }
}
