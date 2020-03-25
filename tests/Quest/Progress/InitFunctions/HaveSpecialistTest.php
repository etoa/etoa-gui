<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\User\UserRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use PHPUnit\Framework\TestCase;

class HaveSpecialistTest extends TestCase
{
    /**
     * @dataProvider specialistProvider
     */
    public function testInitProgress(int $specialistId, int $expected): void
    {
        $userId = 1;
        $repository = $this->getMockBuilder(UserRepository::class)->disableOriginalConstructor()->getMock();
        $repository
            ->expects($this->once())
            ->method('getSpecialistId')
            ->with($this->equalTo($userId))
            ->willReturn($specialistId);

        $quest = $this->getMockBuilder(QuestInterface::class)->getMock();
        $quest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId);

        $task = $this->getMockBuilder(TaskInterface::class)->getMock();

        $function = new HaveSpecialist($repository);

        $this->assertSame($expected, $function->initProgress($quest, $task));
    }

    public function specialistProvider(): array
    {
        return [
            [3, 1],
            [0, 0],
        ];
    }
}
