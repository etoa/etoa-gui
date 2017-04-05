<?php

namespace EtoA\Quest\Progress\Functions;

use EtoA\Galaxy\Event\StarRename;

class RenameStarTest extends AbsractProgressFunctionTestCase
{
    protected function setUp()
    {
        $this->progressFunction = new RenameStar();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle($currentProgress, $expectedProgress)
    {
        $this->simulateHandle(new StarRename(), $currentProgress, $expectedProgress);
    }

    public function providerHandle()
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
