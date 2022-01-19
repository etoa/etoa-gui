<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\SymfonyWebTestCase;

class TechnologyRequirementRepositoryTest extends SymfonyWebTestCase
{
    private TechnologyRequirementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(TechnologyRequirementRepository::class);
    }

    public function testGetAll(): void
    {
        $this->assertNotEmpty($this->repository->getAll()->getBuildingRequirements(4));
    }
}
