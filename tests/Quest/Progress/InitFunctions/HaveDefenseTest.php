<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Defense\DefenseRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class HaveDefenseTest extends TestCase
{
    public function testInitProgress(): void
    {
        $userId = 1;
        $defenseId = 12;
        $progress = 7;
        $repository = $this->getMockBuilder(DefenseRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->once())
            ->method('getDefenseCount')
            ->with($this->equalTo($userId), $this->equalTo($defenseId))
            ->willReturn($progress);

        $quest = $this->getMockBuilder(QuestInterface::class)->getMock();
        $quest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $task = $this->getMockBuilder(TaskInterface::class)->getMock();

        $function = new HaveDefense(['defense_id' => $defenseId], $repository);

        $this->assertSame($progress, $function->initProgress($quest, $task));
    }
}
