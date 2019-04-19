<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Specialist\Event\SpecialistHire;

class HireSpecialistTest extends AbsractProgressFunctionTestCase
{
    protected function setUp(): void
    {
        $this->progressFunction = new HireSpecialist();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(new SpecialistHire(1), $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
