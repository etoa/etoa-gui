<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

abstract class AbsractProgressFunctionTestCase extends TestCase
{
    /** @var HandlerFunctionInterface */
    protected $progressFunction;

    protected function simulateHandle(Event $event, $currentProgress, $expectedProgress): void
    {
        $task = $this->getMockBuilder(TaskInterface::class)->getMock();
        $task
            ->expects($this->once())
            ->method('getProgress')
            ->willReturn($currentProgress);

        $progress = $this->progressFunction->handle($task, $event);

        $this->assertSame($expectedProgress, $progress);
    }
}
