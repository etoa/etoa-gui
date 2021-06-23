<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\AbstractDbTestCase;

class ShipDataRepositoryTest extends AbstractDbTestCase
{
    private ShipDataRepository $repository;

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

    public function testGetShipNamesWithAction(): void
    {
        $names = $this->repository->getShipNamesWithAction('attack');

        $this->assertNotEmpty($names);
    }

    public function testGetShipsWithAction(): void
    {
        $names = $this->repository->getShipsWithAction('attack');

        $this->assertNotEmpty($names);
    }

    public function testGetShipWithPowerProduction(): void
    {
        $ships = $this->repository->getShipWithPowerProduction();

        $this->assertNotEmpty($ships);
        foreach ($ships as $ship) {
            $this->assertGreaterThan(0, $ship->powerProduction);
        }
    }

    public function testGetShip(): void
    {
        $ship = $this->repository->getShip(1);

        $this->assertNotNull($ship);
        $this->assertSame(1, $ship->id);
    }

    public function testGetShipsByCategory(): void
    {
        $ships = $this->repository->getShipsByCategory(1);

        $this->assertNotEmpty($ships);
        foreach ($ships as $ship) {
            $this->assertSame(1, $ship->catId);
        }
    }
}
