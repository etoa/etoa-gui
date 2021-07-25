<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserOnlineStatsRepositoryTest extends AbstractDbTestCase
{
    private UserOnlineStatsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[UserOnlineStatsRepository::class];
    }

    public function testGetEntries(): void
    {
        $this->repository->addEntry(1, 1);

        $this->assertNotEmpty($this->repository->getEntries(10));
    }
}
