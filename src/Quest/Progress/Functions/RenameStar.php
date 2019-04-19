<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Galaxy\Event\StarRename;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;

class RenameStar implements EventHandlerFunctionInterface
{
    public const NAME = 'rename-star';

    public function handle(TaskInterface $task, StarRename $event): int
    {
        return $task->getProgress() + 1;
    }

    public function getEventMap(): array
    {
        return [StarRename::RENAME_SUCCESS => 'handle'];
    }
}
