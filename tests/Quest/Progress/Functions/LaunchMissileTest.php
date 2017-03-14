<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileLaunch;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class LaunchMissileTest extends TestCase
{
    /** @var LaunchMissile */
    protected $progressFunction;

    protected function setUp()
    {
        $this->progressFunction = new LaunchMissile();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, array $missiles, $expectedProgress)
    {
        $task = $this->getMockBuilder(TaskInterface::class)->getMock();
        $task
            ->expects($this->once())
            ->method('getProgress')
            ->willReturn($currentProgress);

        $progress = $this->progressFunction->handle($task, new MissileLaunch($missiles));

        $this->assertSame($expectedProgress, $progress);
    }

    public function providerHandle()
    {
        return [
            [0, [1 => 1], 1],
            [1, [1 => 9], 10],
        ];
    }
}
