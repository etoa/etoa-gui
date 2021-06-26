<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\AbstractDbTestCase;

class MarketRateRepositoryTest extends AbstractDbTestCase
{
    private MarketRateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[MarketRateRepository::class];
    }

    public function testGetRates(): void
    {
        $this->connection->executeQuery('INSERT INTO market_rates (id) VALUES (1)');

        $rates = $this->repository->getRates(1);

        $this->assertNotEmpty($rates);
    }
}
