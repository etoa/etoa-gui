<?php declare(strict_types=1);

namespace EtoA\BuddyList;

use EtoA\AbstractDbTestCase;

class BuddyListRepositoryTest extends AbstractDbTestCase
{
    private BuddyListRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[BuddyListRepository::class];
    }

    public function testAcceptBuddyRequest(): void
    {
        $this->repository->addBuddyRequest(1, 2);

        $this->assertFalse($this->repository->acceptBuddyRequest(1, 2));
        $this->assertTrue($this->repository->acceptBuddyRequest(2, 1));
        $this->assertFalse($this->repository->acceptBuddyRequest(2, 1));
    }

    public function testRejectBuddyRequest(): void
    {
        $this->repository->addBuddyRequest(1, 2);

        $this->assertFalse($this->repository->rejectBuddyRequest(1, 2));
        $this->assertTrue($this->repository->rejectBuddyRequest(2, 1));
        $this->assertFalse($this->repository->rejectBuddyRequest(2, 1));
    }

    public function testHasPendingFriendRequest(): void
    {
        $this->repository->addBuddyRequest(1, 2);

        $this->assertTrue($this->repository->hasPendingFriendRequest(2));
        $this->assertFalse($this->repository->hasPendingFriendRequest(1));
    }

    public function testBuddyListEntryExist(): void
    {
        $this->repository->addBuddyRequest(1, 2);

        $this->assertTrue($this->repository->buddyListEntryExist(1, 2));
        $this->assertFalse($this->repository->buddyListEntryExist(2, 1));
    }
}
