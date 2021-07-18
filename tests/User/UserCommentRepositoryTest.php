<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserCommentRepositoryTest extends AbstractDbTestCase
{
    private UserCommentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[UserCommentRepository::class];
    }

    public function testGetOrphanedCount(): void
    {
        $this->assertSame(0, $this->repository->getOrphanedCount([1,2]));
    }
}
