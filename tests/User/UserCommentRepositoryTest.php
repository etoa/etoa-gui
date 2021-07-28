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

    public function testGetCommentInformation(): void
    {
        $this->repository->addComment(1, 2, 'Test');

        $info = $this->repository->getCommentInformation(1);

        $this->assertSame(1, $info['count']);
    }

    public function testGetComments(): void
    {
        $this->repository->addComment(1, 2, 'Test');

        $this->assertNotEmpty($this->repository->getComments(1));
    }

    public function testGetOrphanedCount(): void
    {
        $this->assertSame(0, $this->repository->getOrphanedCount([1,2]));
    }
}
