<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\AbstractDbTestCase;

class ShipRepositoryTest extends AbstractDbTestCase
{
    private ShipRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[ShipRepository::class];
    }

    public function testGetNumberOfShips(): void
    {
        $userId = 1;
        $shipId = 3;

        $this->repository->addShip($shipId, 1, $userId, 1);

        $this->assertSame(1, $this->repository->getNumberOfShips($shipId));
    }

    public function testAddShip(): void
    {
        $userId = 3;
        $shipId = 5;
        $entityId = 10;

        $this->repository->addShip($shipId, 1, $userId, $entityId);
        $this->repository->addShip($shipId, 29, $userId, $entityId);

        $ships = $this->connection->createQueryBuilder()->select('s.*')->from('shiplist', 's')->execute()->fetchAllAssociative();

        $this->assertCount(1, $ships);
        $ship = $ships[0];
        $this->assertEquals($shipId, $ship['shiplist_ship_id']);
        $this->assertEquals($userId, $ship['shiplist_user_id']);
        $this->assertEquals($entityId, $ship['shiplist_entity_id']);
        $this->assertEquals(30, $ship['shiplist_count']);
    }

    public function testBunker(): void
    {
        $userId = 3;
        $shipId = 5;
        $entityId = 10;

        $this->repository->addShip($shipId, 10, $userId, $entityId);

        $amount = $this->repository->bunker($userId, $entityId, $shipId, 100);
        $this->assertSame(10, $amount);

        $amount = $this->repository->leaveBunker($userId, $entityId, $shipId, 100);
        $this->assertSame(10, $amount);
    }

    public function testRemove(): void
    {
        $userId = 3;
        $shipId = 5;
        $entityId = 10;

        $this->repository->addShip($shipId, 10, $userId, $entityId);

        $amount = $this->repository->removeShips($shipId, 50, $userId, $entityId);

        $this->assertSame(10, $amount);
    }
}
