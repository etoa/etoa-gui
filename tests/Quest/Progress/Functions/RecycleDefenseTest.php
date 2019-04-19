<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Defense\Event\DefenseRecycle;

class RecycleDefenseTest extends AbsractProgressFunctionTestCase
{
    protected function setUp(): void
    {
        $this->progressFunction = new RecycleDefense();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $count, int $expectedProgress): void
    {
        $this->simulateHandle(new DefenseRecycle(1, $count), $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1, 1],
            [1, 9, 10],
        ];
    }
}
