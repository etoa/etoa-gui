<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\AbstractDbTestCase;

class BuildingRepositoryTest extends AbstractDbTestCase
{
    private BuildingRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.building.repository'];
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

        $this->connection->createQueryBuilder()
            ->insert('buildlist')
            ->values([
                'buildlist_user_id' => $userId,
                'buildlist_entity_id' => 1,
                'buildlist_current_level' => 10,
                'buildlist_building_id' => $buildingId,
            ])->execute();

        $this->assertSame(1, $this->repository->getNumberOfBuildings($buildingId));
    }
}
