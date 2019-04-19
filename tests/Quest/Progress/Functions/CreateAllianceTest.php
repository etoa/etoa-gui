<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Alliance\Event\AllianceCreate;

class CreateAllianceTest extends AbsractProgressFunctionTestCase
{
    protected function setUp(): void
    {
        $this->progressFunction = new CreateAlliance();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(new AllianceCreate(), $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
