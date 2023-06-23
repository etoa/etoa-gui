<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Fleet\Event\FleetLaunch;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class LaunchFleetTest extends AbstractProgressFunctionTestCase
{
    /** @var LaunchFleet */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new LaunchFleet();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task): int {
            return $this->progressFunction->handle($task, new FleetLaunch());
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
