<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;
use EtoA\Universe\Resources\BaseResources;

class AllianceSpendRepositoryTest extends AbstractDbTestCase
{
    private AllianceSpendRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[AllianceSpendRepository::class];
    }

    public function testGetTotalSpent(): void
    {
        $resources = new BaseResources();
        $resources->metal = 10;
        $resources->crystal = 20;
        $resources->plastic = 30;
        $resources->fuel = 40;
        $resources->food = 50;

        $this->repository->addEntry(1, 2, $resources);
        $this->repository->addEntry(1, 3, $resources);

        $total = $this->repository->getTotalSpent(1);

        $this->assertSame(20, $total->metal);
        $this->assertSame(40, $total->crystal);
        $this->assertSame(60, $total->plastic);
        $this->assertSame(80, $total->fuel);
        $this->assertSame(100, $total->food);
    }

    public function testGetSpent(): void
    {
        $this->repository->addEntry(1, 2, new BaseResources());

        $this->assertNotEmpty($this->repository->getSpent(1, 2, 1));
    }

    public function testDeleteAllianceEntries(): void
    {
        $this->repository->addEntry(1, 2, new BaseResources());

        $this->assertNotEmpty($this->repository->getSpent(1, 2, 1));

        $this->repository->deleteAllianceEntries(1);

        $this->assertEmpty($this->repository->getSpent(1, 2, 1));
    }
}
