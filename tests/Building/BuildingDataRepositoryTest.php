<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\SymfonyWebTestCase;

class BuildingDataRepositoryTest extends SymfonyWebTestCase
{
    private BuildingDataRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(BuildingDataRepository::class);
    }

    public function testGetBuildingNames(): void
    {
        $buildings = $this->repository->getBuildingNames();

        $this->assertNotEmpty($buildings);
    }

    public function testGetBuildingNamesHavingPlaceForPeople(): void
    {
        $buildings = $this->repository->getBuildingNamesHavingPlaceForPeople();

        $this->assertNotEmpty($buildings);
    }

    public function testGetBuildingsByType(): void
    {
        $buildings = $this->repository->getBuildingsByType(1);

        $this->assertNotEmpty($buildings);
    }

    public function testGetBuilding(): void
    {
        $building = $this->repository->getBuilding(1);

        $this->assertNotNull($building);
    }
}
