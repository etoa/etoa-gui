<?php

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\User\UserRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class HaveGalaxyDiscoveredTest extends TestCase
{
    /**
     * @dataProvider initProgressProvider
     */
    public function testInitProgress($discoverMask, $expectedProgress)
    {
        $userId = 1;
        $repository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->once())
            ->method('getDiscoverMask')
            ->with($this->equalTo($userId))
            ->willReturn($discoverMask);

        $quest = $this->getMockBuilder(QuestInterface::class)->getMock();
        $quest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $task = $this->getMockBuilder(TaskInterface::class)->getMock();

        $function = new HaveGalaxyDiscovered($repository);

        $this->assertSame($expectedProgress, $function->initProgress($quest, $task));
    }

    public function initProgressProvider()
    {
        return [
            ['0000000000', 0],
            ['0111111110', 0],
            ['1111111111', 1],
            ['', 0],
        ];
    }
}
