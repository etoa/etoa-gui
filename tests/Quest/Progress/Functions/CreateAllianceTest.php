<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Alliance\Event\AllianceCreate;

class CreateAllianceTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new CreateAlliance();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $expectedProgress)
    {
        $this->simulateHandle(new AllianceCreate(), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
