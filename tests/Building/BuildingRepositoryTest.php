<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\AbstractDbTestCase;

class BuildingRepositoryTest extends AbstractDbTestCase
{
    private BuildingRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[BuildingRepository::class];
    }

    public function testGetUserHighestLevelNoBuilding(): void
    {
        $this->assertSame(0, $this->repository->getHighestBuildingLevel(1, 1));
    }

    public function testGetHighestUserLevel(): void
    {
        $userId = 1;
        $buildingId = 3;
        for ($i = 4; $i <= 6; $i++) {
            $this->repository->addBuilding($buildingId, $i, $userId, $i);
        }

        $this->assertSame(6, $this->repository->getHighestBuildingLevel($userId, $buildingId));
    }

    public function testGetNumberOfBuildings(): void
    {
        $userId = 1;
        $buildingId = 10;

        $this->repository->addBuilding($buildingId, 10, $userId, 1);

        $this->assertSame(1, $this->repository->getNumberOfBuildings($buildingId));
    }

    public function testGetBuildingLevels(): void
    {
        $userId = 1;
        $buildingId = 3;
        $level = 2;
        $entityId = 4;
        $this->repository->addBuilding($buildingId, $level, $userId, $entityId);

        $this->assertSame([$buildingId => $level], $this->repository->getBuildingLevels($entityId));
    }

    public function testGetEntityBuilding(): void
    {
        $userId = 1;
        $buildingId = 3;
        $level = 2;
        $entityId = 4;
        $this->repository->addBuilding($buildingId, $level, $userId, $entityId);

        $this->assertNotNull($this->repository->getEntityBuilding(1, 4, 3));
    }

    public function testGetPeopleWorking(): void
    {
        $userId = 1;
        $buildingId = 3;
        $level = 2;
        $entityId = 4;
        $this->repository->addBuilding($buildingId, $level, $userId, $entityId);

        $this->assertSame(0, $this->repository->getPeopleWorking($entityId)->total);

        $this->repository->setPeopleWorking($entityId, $buildingId, 100);

        $this->assertSame(100, $this->repository->getPeopleWorking($entityId)->total);
    }

    public function testMarkBuildingWorkingStatus(): void
    {
        $userId = 1;
        $buildingId = BuildingId::TECHNOLOGY;
        $level = 2;
        $entityId = 4;
        $this->repository->addBuilding($buildingId, $level, $userId, $entityId);

        $this->assertTrue($this->repository->markBuildingWorkingStatus($userId, $entityId, $buildingId, true));
    }

    public function testCountEmpty(): void
    {
        $this->assertSame(0, $this->repository->countEmpty());

        $this->repository->addBuilding(1, 0, 1, 1);

        $this->assertSame(1, $this->repository->countEmpty());
    }
}
