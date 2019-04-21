<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\AbstractDbTestCase;

class ShipDataRepositoryTest extends AbstractDbTestCase
{
    /** @var ShipDataRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.ship.datarepository'];
    }

    public function testGetShipNames(): void
    {
        $names = $this->repository->getShipNames();
        $this->assertNotEmpty($names);
    }
}
