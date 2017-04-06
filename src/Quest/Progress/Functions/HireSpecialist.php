<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistHire;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class HireSpecialist implements EventHandlerFunctionInterface
{
    const NAME = 'hire-specialist';

    public function handle(TaskInterface $task, SpecialistHire $event)
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap()
    {
        return [SpecialistHire::HIRE_SUCCESS => 'handle'];
    }
}
