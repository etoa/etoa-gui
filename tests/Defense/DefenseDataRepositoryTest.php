<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\AbstractDbTestCase;

class DefenseDataRepositoryTest extends AbstractDbTestCase
{
    /** @var DefenseDataRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.defense.datarepository'];
    }

    public function testGetDefenseNames(): void
    {
        $names = $this->repository->getDefenseNames();
        $this->assertNotEmpty($names);
    }

    public function testGetDefenseByRace(): void
    {
        $defenses = $this->repository->getDefenseByRace(10);

        $this->assertNotEmpty($defenses);
        foreach ($defenses as $defense) {
            $this->assertSame(1, $defense->raceId);
        }
    }
}
