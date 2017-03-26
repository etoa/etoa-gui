<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Planet\Event\PlanetRename;

class RenamePlanetTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new RenamePlanet();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $expectedProgress)
    {
        $this->simulateHandle(new PlanetRename(), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
