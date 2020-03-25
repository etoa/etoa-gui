<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\AbstractDbTestCase;

class BuildingRepositoryTest extends AbstractDbTestCase
{
    /** @var BuildingRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.building.repository'];
    }

    public function testGetUserLevelNoBuilding(): void
    {
        $this->assertSame(0, $this->repository->getBuildingLevel(1, 1));
    }

    public function testGetUserLevel(): void
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

        $this->assertSame(6, $this->repository->getBuildingLevel($userId, $buildingId));
    }
}
