<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\AbstractDbTestCase;

class TechnologyRequirementRepositoryTest extends AbstractDbTestCase
{
    private TechnologyRequirementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[TechnologyRequirementRepository::class];
    }

    public function testGetAll(): void
    {
        $this->assertNotEmpty($this->repository->getAll()->getBuildingRequirements(4));
    }
}
