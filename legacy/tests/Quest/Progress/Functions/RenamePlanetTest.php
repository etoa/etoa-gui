<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\Functions;

use EtoA\Planet\Event\PlanetRename;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class RenamePlanetTest extends AbstractProgressFunctionTestCase
{
    /** @var RenamePlanet */
    private $progressFunction;

    protected function setUp(): void
    {
        $this->progressFunction = new RenamePlanet();
    }

    /**
     * @dataProvider providerHandle
     */
    public function testHandle(int $currentProgress, int $expectedProgress): void
    {
        $this->simulateHandle(function (TaskInterface $task): int {
            return $this->progressFunction->handle($task, new PlanetRename());
        }, $currentProgress, $expectedProgress);
    }

    public function providerHandle(): array
    {
        return [
            [0, 1],
            [1, 2],
        ];
    }
}
