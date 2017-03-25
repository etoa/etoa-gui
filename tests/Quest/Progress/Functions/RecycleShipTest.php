<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Ship\Event\ShipRecycle;

class RecycleShipTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new RecycleShip();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $count, $expectedProgress)
    {
        $this->simulateHandle(new ShipRecycle(1, $count), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, 1, 1],
            [1, 9, 10],
        ];
    }
}
