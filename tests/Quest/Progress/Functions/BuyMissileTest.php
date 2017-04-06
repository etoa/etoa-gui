<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Missile\Event\MissileBuy;

class BuyMissileTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new BuyMissile();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $count, $expectedProgress)
    {
        $this->simulateHandle(new MissileBuy(1, $count), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, 1, 1],
            [1, 9, 10],
        ];
    }
}
