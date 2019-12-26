<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Defense\Event\DefenseRecycle;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class RecycleDefenseTest extends AbstractProgressFunctionTestCase
{
    /** @var RecycleDefense */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new RecycleDefense();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $count, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task) use ($count): int {
            return $this->progressFunction->handle($task, new DefenseRecycle(1, $count));
        }, $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1, 1],
            [1, 9, 10],
        ];
    }
}
