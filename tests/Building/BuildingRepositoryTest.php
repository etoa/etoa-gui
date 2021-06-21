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
            $this->connection->createQueryBuilder()
                ->insert('buildlist')
                ->values([
                    'buildlist_user_id' => $userId,
                    'buildlist_entity_id' => $i,
                    'buildlist_current_level' => $i,
                    'buildlist_building_id' => $buildingId,
                ])->execute();
        }

        $this->assertSame(6, $this->repository->getHighestBuildingLevel($userId, $buildingId));
    }
}
