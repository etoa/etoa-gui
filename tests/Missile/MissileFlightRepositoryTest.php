<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\AbstractDbTestCase;

class MissileFlightRepositoryTest extends AbstractDbTestCase
{
    private MissileFlightRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[MissileFlightRepository::class];
    }

    public function testStartFlight(): void
    {
        $flightId = $this->repository->startFlight(1, 2, 10, [1 => 1]);

        $this->assertGreaterThan(0, $flightId);
    }

    public function testGetFlights(): void
    {
        $this->repository->startFlight(3, 2, 10, [1 => 1]);
        $this->connection->executeQuery('INSERT INTO planets (id) VALUES (2)');

        $flights = $this->repository->getFlights(3);

        $this->assertNotEmpty($flights);
        $this->assertSame([1 => 1], $flights[0]->missiles);
    }

    public function testDeleteFlight(): void
    {
        $flightId = $this->repository->startFlight(1, 2, 10, [1 => 1]);

        $deleted = $this->repository->deleteFlight($flightId, 1);

        $this->assertTrue($deleted);
    }
}
