<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Technology\TechnologyRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class HaveTechnologyLevelTest extends TestCase
{
    public function testInitProgress(): void
    {
        $userId = 1;
        $technologyId = 12;
        $progress = 7;
        $repository = $this->getMockBuilder(TechnologyRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->once())
            ->method('getTechnologyLevel')
            ->with($this->equalTo($userId), $this->equalTo($technologyId))
            ->willReturn($progress);

        $quest = $this->getMockBuilder(QuestInterface::class)->getMock();
        $quest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $task = $this->getMockBuilder(TaskInterface::class)->getMock();

        $function = new HaveTechnologyLevel(['technology_id' => $technologyId], $repository);

        $this->assertSame($progress, $function->initProgress($quest, $task));
    }
}
