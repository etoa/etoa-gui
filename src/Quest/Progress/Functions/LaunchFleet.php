<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Fleet\Event\FleetLaunch;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class LaunchFleet implements EventHandlerFunctionInterface
{
    public const NAME = 'launch-fleet';

    public function handle(TaskInterface $task, FleetLaunch $event): int
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap(): array
    {
        return [FleetLaunch::LAUNCH_SUCCESS => 'handle'];
    }
}
