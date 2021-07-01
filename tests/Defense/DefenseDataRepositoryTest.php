<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\AbstractDbTestCase;

class DefenseDataRepositoryTest extends AbstractDbTestCase
{
    private DefenseDataRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[DefenseDataRepository::class];
    }

    public function testGetDefenseNames(): void
    {
        $names = $this->repository->getDefenseNames();
        $this->assertNotEmpty($names);
    }

    public function testGetDefense(): void
    {
        $defense = $this->repository->getDefense(1);

        $this->assertNotNull($defense);
        $this->assertSame(1, $defense->id);
    }
    public function testGetDefenseByRace(): void
    {
        $defenses = $this->repository->getDefenseByRace(10);

        $this->assertNotEmpty($defenses);
        foreach ($defenses as $defense) {
            $this->assertSame(10, $defense->raceId);
        }
    }

    public function testGetDefenseByCategory(): void
    {
        $defenses = $this->repository->getDefenseByCategory(1);

        $this->assertNotEmpty($defenses);
        foreach ($defenses as $defense) {
            $this->assertSame(1, $defense->catId);
        }
    }
}
