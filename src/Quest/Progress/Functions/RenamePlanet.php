<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Planet\Event\PlanetRename;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;

class RenamePlanet implements HandlerFunctionInterface
{
    const NAME = 'rename-planet';

    public function handle(TaskInterface $task, PlanetRename $event)
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap()
    {
        return [PlanetRename::RENAME_SUCCESS => 'handle'];
    }
}
