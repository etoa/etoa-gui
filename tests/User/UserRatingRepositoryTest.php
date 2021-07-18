<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserRatingRepositoryTest extends AbstractDbTestCase
{
    private UserRatingRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[UserRatingRepository::class];
    }

    public function testGetOrphanedCount(): void
    {
        $this->assertSame(0, $this->repository->getOrphanedCount([1,2]));
    }
}
