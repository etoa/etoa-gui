<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\User\UserRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class HaveAllianceTest extends TestCase
{
    /**
     * @dataProvider allianceProvider
     */
    public function testInitProgress(int $allianceId, int $expected): void
    {
        $userId = 1;
        $repository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->once())
            ->method('getAllianceId')
            ->with($this->equalTo($userId))
            ->willReturn($allianceId);

        $quest = $this->getMockBuilder(QuestInterface::class)->getMock();
        $quest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $task = $this->getMockBuilder(TaskInterface::class)->getMock();

        $function = new HaveAlliance($repository);

        $this->assertSame($expected, $function->initProgress($quest, $task));
    }

    public function allianceProvider(): array
    {
        return [
            [100, 1],
            [0, 0],
        ];
    }
}
