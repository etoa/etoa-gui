<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\AbstractDbTestCase;

class DefenseRepositoryTest extends AbstractDbTestCase
{
    /** @var DefenseRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.defense.repository'];
    }

    public function testAddDefense(): void
    {
        $userId = 3;
        $defenseId = 5;
        $entityId = 10;

        $this->repository->addDefense($defenseId, 1, $userId, $entityId);
        $this->repository->addDefense($defenseId, 29, $userId, $entityId);

        $defenses = $this->connection->createQueryBuilder()->select('d.*')->from('deflist', 'd')->execute()->fetchAll();

        $this->assertCount(1, $defenses);
        $defense = $defenses[0];
        $this->assertEquals($defenseId, $defense['deflist_def_id']);
        $this->assertEquals($userId, $defense['deflist_user_id']);
        $this->assertEquals($entityId, $defense['deflist_entity_id']);
        $this->assertEquals(30, $defense['deflist_count']);
    }

    public function testGetDefenseCount(): void
    {
        $userId = 3;
        $defenseId = 5;
        $entityId = 10;

        $this->repository->addDefense($defenseId, 1, $userId, $entityId);
        $this->repository->addDefense($defenseId, 29, $userId, $entityId + 1);
        $this->repository->addDefense($defenseId + 1, 29, $userId, $entityId);

        $this->assertSame(30, $this->repository->getDefenseCount($userId, $defenseId));
    }

    public function testGetDefenseCountNoDef(): void
    {
        $userId = 3;
        $defenseId = 5;

        $this->assertSame(0, $this->repository->getDefenseCount($userId, $defenseId));
    }
}
