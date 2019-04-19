<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Ship\Event\ShipRecycle;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class RecycleShip implements EventHandlerFunctionInterface
{
    public const NAME = 'recycle-ship';

    public function handle(TaskInterface $task, ShipRecycle $event): int
    {
        return $task->getProgress() + $event->getCount();
    }

    public function getEventMap(): array
    {
        return [ShipRecycle::RECYCLE_SUCCESS => 'handle'];
    }
}
