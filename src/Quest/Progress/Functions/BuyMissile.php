<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileBuy;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class BuyMissile implements EventHandlerFunctionInterface
{
    public const NAME = 'buy-missile';

    public function handle(TaskInterface $task, MissileBuy $event): int
    {
        return $task->getProgress() + $event->getCount();
    }

    public function getEventMap(): array
    {
        return [MissileBuy::BUY_SUCCESS => 'handle'];
    }
}
