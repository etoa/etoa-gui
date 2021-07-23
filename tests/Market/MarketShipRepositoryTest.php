<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\AbstractDbTestCase;
use EtoA\Universe\Resources\BaseResources;

class MarketShipRepositoryTest extends AbstractDbTestCase
{
    private MarketShipRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[MarketShipRepository::class];
    }

    public function testGetAll(): void
    {
        $this->repository->add(1, 2, 3, 4, 'Text', 1, 10, new BaseResources());

        $this->assertNotEmpty($this->repository->getAll());
    }

    public function testGetBuyableOffers(): void
    {
        $this->repository->add(1, 2, 3, 4, 'Text', 1, 10, new BaseResources());

        $this->assertNotEmpty($this->repository->getBuyableOffers(3, 4));

        $this->assertEmpty($this->repository->getBuyableOffers(99, 4));
        $this->assertEmpty($this->repository->getBuyableOffers(3, 99));
    }

    public function testGetBuyableOffer(): void
    {
        $offerId = $this->repository->add(1, 2, 3, 4, 'Text', 1, 10, new BaseResources());

        $this->assertNotNull($this->repository->getBuyableOffer($offerId, 3, 4));
        $this->assertNull($this->repository->getBuyableOffer($offerId, 3, 999));
        $this->assertNull($this->repository->getBuyableOffer($offerId, 999, 4));
        $this->assertNull($this->repository->getBuyableOffer(999, 3, 4));
    }

    public function testGetUserOffers(): void
    {
        $this->repository->add(1, 2, 3, 4, 'Text', 1, 10, new BaseResources());

        $this->assertNotEmpty($this->repository->getUserOffers(1));
    }

    public function testGetUserOffer(): void
    {
        $offerId = $this->repository->add(1, 2, 3, 4, 'Text', 1, 10, new BaseResources());

        $this->assertNotNull($this->repository->getUserOffer($offerId, 1));
    }
}
