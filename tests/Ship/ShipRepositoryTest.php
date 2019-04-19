<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\AbstractDbTestCase;

class ShipRepositoryTest extends AbstractDbTestCase
{
    /** @var ShipRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.ship.repository'];
    }

    public function testAddShip(): void
    {
        $userId = 3;
        $shipId = 5;
        $entityId = 10;

        $this->repository->addShip($shipId, 1, $userId, $entityId);
        $this->repository->addShip($shipId, 29, $userId, $entityId);

        $ships = $this->connection->createQueryBuilder()->select('s.*')->from('shiplist', 's')->execute()->fetchAll();

        $this->assertCount(1, $ships);
        $ship = $ships[0];
        $this->assertEquals($shipId, $ship['shiplist_ship_id']);
        $this->assertEquals($userId, $ship['shiplist_user_id']);
        $this->assertEquals($entityId, $ship['shiplist_entity_id']);
        $this->assertEquals(30, $ship['shiplist_count']);
    }
}
