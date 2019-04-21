<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\AbstractDbTestCase;

class MissileRepositoryTest extends AbstractDbTestCase
{
    /** @var MissileRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.missile.repository'];
    }

    public function testAddMissile(): void
    {
        $userId = 3;
        $missileId = 5;
        $entityId = 10;

        $this->repository->addMissile($missileId, 1, $userId, $entityId);
        $this->repository->addMissile($missileId, 29, $userId, $entityId);

        $missiles = $this->connection->createQueryBuilder()->select('d.*')->from('missilelist', 'd')->execute()->fetchAll();

        $this->assertCount(1, $missiles);
        $missile = $missiles[0];
        $this->assertEquals($missileId, $missile['missilelist_missile_id']);
        $this->assertEquals($userId, $missile['missilelist_user_id']);
        $this->assertEquals($entityId, $missile['missilelist_entity_id']);
        $this->assertEquals(30, $missile['missilelist_count']);
    }
}
