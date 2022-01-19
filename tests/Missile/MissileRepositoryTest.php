<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\SymfonyWebTestCase;

class MissileRepositoryTest extends SymfonyWebTestCase
{
    private MissileRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(MissileRepository::class);
    }

    public function testAddMissile(): void
    {
        $userId = 3;
        $missileId = 5;
        $entityId = 10;

        $this->repository->addMissile($missileId, 1, $userId, $entityId);
        $this->repository->addMissile($missileId, 29, $userId, $entityId);

        $missiles = $this->getConnection()->fetchAllAssociative('SELECT * FROM missilelist');

        $this->assertCount(1, $missiles);
        $missile = $missiles[0];
        $this->assertEquals($missileId, $missile['missilelist_missile_id']);
        $this->assertEquals($userId, $missile['missilelist_user_id']);
        $this->assertEquals($entityId, $missile['missilelist_entity_id']);
        $this->assertEquals(30, $missile['missilelist_count']);
    }

    public function testCount(): void
    {
        $this->repository->addMissile(1, 1, 1, 1);

        $this->assertSame(1, $this->repository->count());
    }

    public function testGetMissilesCounts(): void
    {
        $this->repository->addMissile(1, 2, 1, 1);

        $this->assertSame([1 => 2], $this->repository->getMissilesCounts(1, 1));
    }
}
