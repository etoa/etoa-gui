<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Defense\Event\DefenseRecycle;

class RecycleDefenseTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new RecycleDefense();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $count, $expectedProgress)
    {
        $this->simulateHandle(new DefenseRecycle(1, $count), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, 1, 1],
            [1, 9, 10],
        ];
    }
}
