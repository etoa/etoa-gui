<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Planet\Event\PlanetRename;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class RenamePlanet implements EventHandlerFunctionInterface
{
    public const NAME = 'rename-planet';

    public function handle(TaskInterface $task, PlanetRename $event): int
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap(): array
    {
        return [PlanetRename::RENAME_SUCCESS => 'handle'];
    }
}
