<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\AbstractDbTestCase;
use EtoA\Universe\Resources\BaseResources;

class MarketAuctionRepositoryTest extends AbstractDbTestCase
{
    private MarketAuctionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[MarketAuctionRepository::class];
    }

    public function testGetAll(): void
    {
        $this->repository->add(1, 2, time() + 10, 'Text', new BaseResources(), new BaseResources());

        $this->assertNotEmpty($this->repository->getAll());
    }

    public function testGetBuyableAuctions(): void
    {
        $this->repository->add(1, 2, time() + 10, 'Text', new BaseResources(), new BaseResources());

        $this->assertNotEmpty($this->repository->getBuyableAuctions(99));
    }

    public function testGetNonUserAuction(): void
    {
        $auctionId = $this->repository->add(1, 2, time() + 10, 'Text', new BaseResources(), new BaseResources());

        $this->assertNotNull($this->repository->getNonUserAuction($auctionId, 99));
    }

    public function testGetUserAuctions(): void
    {
        $this->repository->add(1, 2, time() + 10, 'Text', new BaseResources(), new BaseResources());

        $this->assertNotEmpty($this->repository->getUserAuctions(1));
    }

    public function testGetUserAuction(): void
    {
        $auctionId = $this->repository->add(1, 2, time() + 10, 'Text', new BaseResources(), new BaseResources());

        $this->assertNotNull($this->repository->getUserAuction($auctionId, 1));
    }

    public function testAddBid(): void
    {
        $auctionId = $this->repository->add(1, 2, time() + 10, 'Text', new BaseResources(), new BaseResources());

        $this->repository->addBid($auctionId, 99, 999, new BaseResources());

        $auction = $this->repository->getUserAuction($auctionId, 1);
        $this->assertNotNull($auction);

        $this->assertSame(99, $auction->currentBuyerId);
        $this->assertSame(999, $auction->currentBuyerEntityId);
    }
}
