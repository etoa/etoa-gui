<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileLaunch;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class LaunchMissile implements EventHandlerFunctionInterface
{
    public const NAME = 'launch-missile';

    public function handle(TaskInterface $task, MissileLaunch $event): int
    {
        return $task->getProgress() + $event->getMissileCount();
    }

    public function getEventMap(): array
    {
        return [MissileLaunch::LAUNCH_SUCCESS => 'handle'];
    }
}
