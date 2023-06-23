<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\SymfonyWebTestCase;

class ShipDataRepositoryTest extends SymfonyWebTestCase
{
    private ShipDataRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(ShipDataRepository::class);
    }

    public function testGetShipNames(): void
    {
        $names = $this->repository->getShipNames();

        $this->assertNotEmpty($names);

        $names = $this->repository->getShipNames(true);

        $this->assertNotEmpty($names);
    }

    public function testGetShipPoints(): void
    {
        $points = $this->repository->getShipPoints();

        $this->assertNotEmpty($points);
    }

    public function testGetAllShips(): void
    {
        $ships = $this->repository->getAllShips();

        $this->assertNotEmpty($ships);
    }

    public function testGetSpecialShips(): void
    {
        $ships = $this->repository->getSpecialShips();

        $this->assertNotEmpty($ships);
        foreach ($ships as $ship) {
            $this->assertTrue($ship->special);
        }
    }

    public function testGetFakeableShipNames(): void
    {
        $shipNames = $this->repository->getFakeableShipNames();

        $this->assertNotEmpty($shipNames);
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

    public function testGetShipsByRace(): void
    {
        $ships = $this->repository->getShipsByRace(1);

        $this->assertNotEmpty($ships);
        foreach ($ships as $ship) {
            $this->assertSame(1, $ship->raceId);
        }
    }
}
