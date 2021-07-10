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
}
