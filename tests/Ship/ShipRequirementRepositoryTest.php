<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\AbstractDbTestCase;

class ShipRequirementRepositoryTest extends AbstractDbTestCase
{
    private ShipRequirementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[ShipRequirementRepository::class];
    }

    public function testGetRequiredSpeedTechnologies(): void
    {
        $technologies = $this->repository->getRequiredSpeedTechnologies(1);

        $this->assertNotEmpty($technologies);
    }

    public function testGetShipsWithRequiredTechnology(): void
    {
        $requirements = $this->repository->getShipsWithRequiredTechnology(4);

        $this->assertNotEmpty($requirements);
    }
}
