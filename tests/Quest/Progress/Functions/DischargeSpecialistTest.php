<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistDischarge;

class DischargeSpecialistTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new DischargeSpecialist();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $expectedProgress)
    {
        $this->simulateHandle(new SpecialistDischarge(1), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
