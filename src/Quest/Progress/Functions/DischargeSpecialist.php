<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistDischarge;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class DischargeSpecialist implements EventHandlerFunctionInterface
{
    const NAME = 'discharge-specialist';

    public function handle(TaskInterface $task, SpecialistDischarge $event)
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap()
    {
        return [SpecialistDischarge::DISCHARGE_SUCCESS => 'handle'];
    }
}
