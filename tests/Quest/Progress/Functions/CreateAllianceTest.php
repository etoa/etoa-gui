<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Alliance\Event\AllianceCreate;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class CreateAllianceTest extends AbstractProgressFunctionTestCase
{
    /** @var CreateAlliance */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new CreateAlliance();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task): int {
            return $this->progressFunction->handle($task, new AllianceCreate());
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
