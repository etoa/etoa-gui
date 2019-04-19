<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Defense\Event\DefenseRecycle;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class RecycleDefense implements EventHandlerFunctionInterface
{
    public const NAME = 'recycle-defense';

    public function handle(TaskInterface $task, DefenseRecycle $event): int
    {
        return $task->getProgress() + $event->getCount();
    }

    public function getEventMap(): array
    {
        return [DefenseRecycle::RECYCLE_SUCCESS => 'handle'];
    }
}
