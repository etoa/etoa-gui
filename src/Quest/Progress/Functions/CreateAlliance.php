<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Alliance\Event\AllianceCreate;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class CreateAlliance implements EventHandlerFunctionInterface
{
    public const NAME = 'create-alliance';

    public function handle(TaskInterface $task, AllianceCreate $event): int
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap(): array
    {
        return [AllianceCreate::CREATE_SUCCESS => 'handle'];
    }
}
