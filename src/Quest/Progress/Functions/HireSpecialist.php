<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistHire;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class HireSpecialist implements EventHandlerFunctionInterface
{
    public const NAME = 'hire-specialist';

    public function handle(TaskInterface $task, SpecialistHire $event): int
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap(): array
    {
        return [SpecialistHire::HIRE_SUCCESS => 'handle'];
    }
}
