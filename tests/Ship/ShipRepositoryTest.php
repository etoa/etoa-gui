<?php

namespace EtoA\Ship;

use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;

class ShipRepositoryTest extends TestCase
{
    /** @var ShipRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $app = require dirname(dirname(__DIR__)).'/src/app.php';

        $this->repository = $app['etoa.ship.repository'];
        $this->connection = $app['db'];
    }

    public function testAddShips()
    {
        $userId = 3;
        $shipId = 5;
        $entityId = 10;

        $this->repository->addShips($shipId, 1, $userId, $entityId);
        $this->repository->addShips($shipId, 29, $userId, $entityId);

        $ships = (new QueryBuilder($this->connection))->select('s.*')->from('shiplist', 's')->execute()->fetchAll();

        $this->assertCount(1, $ships);
        $ship = $ships[0];
        $this->assertEquals($shipId, $ship['shiplist_ship_id']);
        $this->assertEquals($userId, $ship['shiplist_user_id']);
        $this->assertEquals($entityId, $ship['shiplist_entity_id']);
        $this->assertEquals(30, $ship['shiplist_count']);
    }
}
