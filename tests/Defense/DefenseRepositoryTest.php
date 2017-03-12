<?php

namespace EtoA\Defense;

use EtoA\AbstractDbTestCase;

class DefenseRepositoryTest extends AbstractDbTestCase
{
    /** @var DefenseRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->app['etoa.defense.repository'];
    }

    public function testAddShips()
    {
        $userId = 3;
        $shipId = 5;
        $entityId = 10;

        $this->repository->addDefense($shipId, 1, $userId, $entityId);
        $this->repository->addDefense($shipId, 29, $userId, $entityId);

        $ships = $this->connection->createQueryBuilder()->select('d.*')->from('deflist', 'd')->execute()->fetchAll();

        $this->assertCount(1, $ships);
        $ship = $ships[0];
        $this->assertEquals($shipId, $ship['deflist_def_id']);
        $this->assertEquals($userId, $ship['deflist_user_id']);
        $this->assertEquals($entityId, $ship['deflist_entity_id']);
        $this->assertEquals(30, $ship['deflist_count']);
    }
}
