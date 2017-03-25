<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileLaunch;

class LaunchMissileTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new LaunchMissile();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, array $missiles, $expectedProgress)
    {
        $this->simulateHandle(new MissileLaunch($missiles), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, [1 => 1], 1],
            [1, [1 => 9], 10],
        ];
    }
}
