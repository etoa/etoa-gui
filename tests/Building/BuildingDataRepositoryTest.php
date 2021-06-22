<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\AbstractDbTestCase;

class BuildingDataRepositoryTest extends AbstractDbTestCase
{
    private BuildingDataRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.building.datarepository'];
    }

    public function testGetBuildingNames(): void
    {
        $buildings = $this->repository->getBuildingNames();

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
