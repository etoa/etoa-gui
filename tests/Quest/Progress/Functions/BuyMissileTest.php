<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileBuy;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class BuyMissileTest extends TestCase
{
    /** @var BuyMissile */
    protected $progressFunction;

    protected function setUp()
    {
        $this->progressFunction = new BuyMissile();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $count, $expectedProgress)
    {
        $task = $this->getMockBuilder(TaskInterface::class)->getMock();
        $task
            ->expects($this->once())
            ->method('getProgress')
            ->willReturn($currentProgress);

        $progress = $this->progressFunction->handle($task, new MissileBuy(1, $count));

        $this->assertSame($expectedProgress, $progress);
    }

    public function providerHandle()
    {
        return [
            [0, 1, 1],
            [1, 9, 10],
        ];
    }
}
