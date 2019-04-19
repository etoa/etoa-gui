<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Fleet\Event\FleetLaunch;

class LaunchFleetTest extends AbsractProgressFunctionTestCase
{
    protected function setUp(): void
    {
        $this->progressFunction = new LaunchFleet();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(new FleetLaunch(), $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
