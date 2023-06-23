<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\SymfonyWebTestCase;

class MarketRateRepositoryTest extends SymfonyWebTestCase
{
    private MarketRateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(MarketRateRepository::class);
    }

    public function testGetRates(): void
    {
        $this->getConnection()->executeQuery('INSERT INTO market_rates (id) VALUES (1)');

        $rates = $this->repository->getRates(1);

        $this->assertNotEmpty($rates);
    }
}
