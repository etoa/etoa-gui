<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Planet\Event\PlanetRename;

class RenamePlanetTest extends AbsractProgressFunctionTestCase
{
    protected function setUp(): void
    {
        $this->progressFunction = new RenamePlanet();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(new PlanetRename(), $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
