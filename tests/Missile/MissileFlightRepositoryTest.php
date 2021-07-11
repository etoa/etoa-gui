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

    public function teststartFlight(): void
    {
        $this->repository->startFlight(1, 2, 10, [1 => 1]);
    }
}
