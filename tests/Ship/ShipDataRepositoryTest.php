<?php

namespace EtoA\Ship;

use EtoA\AbstractDbTestCase;

class ShipDataRepositoryTest extends AbstractDbTestCase
{
    /** @var ShipDataRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->app['etoa.ship.datarepository'];
    }

    public function testGetShipNames()
    {
        $names = $this->repository->getShipNames();
        $this->assertNotEmpty($names);
    }
}
