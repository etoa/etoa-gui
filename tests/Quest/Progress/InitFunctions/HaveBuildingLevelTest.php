<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Building\BuildingRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class HaveBuildingLevelTest extends TestCase
{
    public function testInitProgress(): void
    {
        $userId = 1;
        $buildingId = 12;
        $progress = 7;
        $repository = $this->getMockBuilder(BuildingRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->once())
            ->method('getBuildingLevel')
            ->with($this->equalTo($userId), $this->equalTo($buildingId))
            ->willReturn($progress);

        $quest = $this->getMockBuilder(QuestInterface::class)->getMock();
        $quest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $task = $this->getMockBuilder(TaskInterface::class)->getMock();

        $function = new HaveBuildingLevel(['building_id' => $buildingId], $repository);

        $this->assertSame($progress, $function->initProgress($quest, $task));
    }
}
