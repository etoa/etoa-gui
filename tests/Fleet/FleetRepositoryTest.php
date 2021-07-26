<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\AbstractDbTestCase;
use EtoA\Universe\Resources\BaseResources;

class FleetRepositoryTest extends AbstractDbTestCase
{
    private FleetRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[FleetRepository::class];
    }

    public function testCountShipsInFleet(): void
    {
        $fleetId = $this->repository->add(1, 1, 2, 3, 4, 'action', FleetStatus::DEPARTURE, new BaseResources());
        $this->repository->addShipsToFleet($fleetId, 1, 2);
        $this->repository->addShipsToFleet($fleetId, 2, 3);

        $this->assertSame(5, $this->repository->countShipsInFleet($fleetId));
    }
}
