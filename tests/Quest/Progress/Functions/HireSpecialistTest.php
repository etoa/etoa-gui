<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistHire;

class HireSpecialistTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new HireSpecialist();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $expectedProgress)
    {
        $this->simulateHandle(new SpecialistHire(1), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
