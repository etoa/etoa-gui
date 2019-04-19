<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Ship\Event\ShipUpgrade;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class UpgradeShip implements EventHandlerFunctionInterface
{
    const NAME = 'upgrade-ship';

    public function handle(TaskInterface $task, ShipUpgrade $event)
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap()
    {
        return [ShipUpgrade::UPGRADE_SUCCESS => 'handle'];
    }
}
