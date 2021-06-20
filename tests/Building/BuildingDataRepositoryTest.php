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

    public function testGetUserLevelNoBuilding(): void
    {
        $buildings = $this->repository->getBuildingsByType(1);

        $this->assertNotEmpty($buildings);
    }
}
