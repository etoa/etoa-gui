<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Planet\PlanetRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class HavePlanetCountTest extends TestCase
{
    public function testInitProgress(): void
    {
        $userId = 1;
        $count = 7;
        $repository = $this->getMockBuilder(PlanetRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->once())
            ->method('getPlanetCount')
            ->with($this->equalTo($userId))
            ->willReturn($count);

        $quest = $this->getMockBuilder(QuestInterface::class)->getMock();
        $quest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $task = $this->getMockBuilder(TaskInterface::class)->getMock();

        $function = new HavePlanetCount($repository);

        $this->assertSame($count, $function->initProgress($quest, $task));
    }
}
