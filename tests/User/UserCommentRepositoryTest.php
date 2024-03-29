<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\SymfonyWebTestCase;

class UserCommentRepositoryTest extends SymfonyWebTestCase
{
    private UserCommentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(UserCommentRepository::class);
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
}
