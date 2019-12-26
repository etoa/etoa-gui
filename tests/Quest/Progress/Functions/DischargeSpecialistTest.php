<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistDischarge;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class DischargeSpecialistTest extends AbstractProgressFunctionTestCase
{
    /** @var DischargeSpecialist */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new DischargeSpecialist();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task): int {
            return $this->progressFunction->handle($task, new SpecialistDischarge(1));
        }, $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
