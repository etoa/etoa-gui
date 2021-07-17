<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\AbstractDbTestCase;

class TechnologyRepositoryTest extends AbstractDbTestCase
{
    private TechnologyRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[TechnologyRepository::class];
    }

    public function testGetUserLevelNoTechnology(): void
    {
        $this->assertSame(0, $this->repository->getTechnologyLevel(1, 1));
    }

    public function testGetUserLevel(): void
    {
        $userId = 1;
        $technologyId = 3;

        $this->repository->addTechnology($technologyId, 6, $userId, 1);

        $this->assertSame(6, $this->repository->getTechnologyLevel($userId, $technologyId));
    }

    public function testGetUserLevels(): void
    {
        $userId = 1;
        $technologyId = 3;

        $this->repository->addTechnology($technologyId, 6, $userId, 1);

        $this->assertSame([$technologyId => 6], $this->repository->getTechnologyLevels($userId));
    }

    public function testUpdateBuildStatus(): void
    {
        $userId = 1;
        $technologyId = 3;

        $this->assertTrue($this->repository->updateBuildStatus($userId, 1, $technologyId, 0, 0, 0));
    }

    public function testCount(): void
    {
        $this->assertSame(0, $this->repository->count());
    }

    public function testCountEmpty(): void
    {
        $this->assertSame(0, $this->repository->countEmpty());

        $this->repository->addTechnology(1, 0, 1, 1);

        $this->assertSame(1, $this->repository->countEmpty());
    }
}
