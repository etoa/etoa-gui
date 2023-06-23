<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\SymfonyWebTestCase;

class ShipRequirementRepositoryTest extends SymfonyWebTestCase
{
    private ShipRequirementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(ShipRequirementRepository::class);
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
