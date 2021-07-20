<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserSittingRepositoryTest extends AbstractDbTestCase
{
    private UserSittingRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[UserSittingRepository::class];
    }

    public function testGetActiveSittingEntries(): void
    {
        $this->repository->addEntry(1, 2, 'pw', time() - 1, time() + 50);

        $this->assertNotEmpty($this->repository->getActiveSittingEntries());
    }

    public function testGetActiveUserEntry(): void
    {
        $this->repository->addEntry(1, 2, 'pw', time() - 1, time() + 50);

        $this->assertNotNull($this->repository->getActiveUserEntry(1));
        $this->assertNull($this->repository->getActiveUserEntry(2));
    }

    public function testGetWhereUser(): void
    {
        $this->repository->addEntry(1, 2, 'pw', time() - 1, time() + 50);

        $this->assertNotEmpty($this->repository->getWhereUser(1));
        $this->assertEmpty($this->repository->getWhereUser(2));
    }

    public function testGetWhereSitter(): void
    {
        $this->repository->addEntry(1, 2, 'pw', time() - 1, time() + 50);

        $this->assertEmpty($this->repository->getWhereSitter(1));
        $this->assertNotEmpty($this->repository->getWhereSitter(2));
    }

    public function testExistsEntry(): void
    {
        $this->repository->addEntry(1, 2, 'pw', time() - 1, time() + 50);

        $this->assertTrue($this->repository->existsEntry(1, 'pw'));
        $this->assertFalse($this->repository->existsEntry(2, 'pw'));
        $this->assertFalse($this->repository->existsEntry(1, 'wrong'));
    }

    public function testHasSittingEntryForTimeSpan(): void
    {
        $time = time();
        $this->repository->addEntry(1, 2, 'pw', $time - 1, $time + 50);

        $this->assertTrue($this->repository->hasSittingEntryForTimeSpan(1, $time, $time + 1));
        $this->assertFalse($this->repository->hasSittingEntryForTimeSpan(1, $time - 10, $time - 2));
        $this->assertFalse($this->repository->hasSittingEntryForTimeSpan(1, $time + 51, $time + 100));
    }

    public function testGetUsedSittingTime(): void
    {
        $time = time();
        $this->repository->addEntry(1, 2, 'pw', $time - 1, $time + 50);

        $this->assertSame(1, $this->repository->getUsedSittingTime(1));
    }
}
