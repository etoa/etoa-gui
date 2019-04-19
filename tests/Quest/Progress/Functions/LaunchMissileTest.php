<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileLaunch;

class LaunchMissileTest extends AbsractProgressFunctionTestCase
{
    protected function setUp(): void
    {
        $this->progressFunction = new LaunchMissile();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, array $missiles, int $expectedProgress): void
    {
        $this->simulateHandle(new MissileLaunch($missiles), $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, [1 => 1], 1],
            [1, [1 => 9], 10],
        ];
    }
}
