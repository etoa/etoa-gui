<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistDischarge;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class DischargeSpecialist implements EventHandlerFunctionInterface
{
    public const NAME = 'discharge-specialist';

    public function handle(TaskInterface $task, SpecialistDischarge $event): int
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap(): array
    {
        return [SpecialistDischarge::DISCHARGE_SUCCESS => 'handle'];
    }
}
