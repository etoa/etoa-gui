<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileLaunch;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;

class LaunchMissile implements HandlerFunctionInterface
{
    const NAME = 'launch-missile';

    public function handle(TaskInterface $task, MissileLaunch $event)
    {
        return $task->getProgress() + $event->getMissileCount();
    }

    public function getEventMap()
    {
        return [MissileLaunch::LAUNCH_SUCCESS => 'handle'];
    }
}
