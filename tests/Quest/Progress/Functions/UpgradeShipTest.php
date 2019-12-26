<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Ship\Event\ShipUpgrade;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class UpgradeShipTest extends AbstractProgressFunctionTestCase
{
    /** @var UpgradeShip */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new UpgradeShip();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task): int {
            return $this->progressFunction->handle($task, new ShipUpgrade());
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
