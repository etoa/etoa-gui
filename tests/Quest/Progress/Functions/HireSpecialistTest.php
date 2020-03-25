<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistHire;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class HireSpecialistTest extends AbstractProgressFunctionTestCase
{
    /** @var HireSpecialist */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new HireSpecialist();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task): int {
            return $this->progressFunction->handle($task, new SpecialistHire(1));
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
