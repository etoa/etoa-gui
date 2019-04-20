<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractProgressFunctionTestCase extends TestCase
{
    protected function simulateHandle(callable $handleEvent, int $currentProgress, int $expectedProgress): void
    {
        $task = $this->getMockBuilder(TaskInterface::class)->getMock();
        $task
            ->expects($this->once())
            ->method('getProgress')
            ->willReturn($currentProgress);

        $progress = $handleEvent($task);

        $this->assertSame($expectedProgress, $progress);
    }
}
