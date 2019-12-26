<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Galaxy\Event\StarRename;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class RenameStarTest extends AbstractProgressFunctionTestCase
{
    /** @var RenameStar */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new RenameStar();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task): int {
            return $this->progressFunction->handle($task, new StarRename());
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
