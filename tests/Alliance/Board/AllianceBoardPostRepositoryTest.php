<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use EtoA\AbstractDbTestCase;

class AllianceBoardPostRepositoryTest extends AbstractDbTestCase
{
    private AllianceBoardPostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AllianceBoardPostRepository::class];
    }

    public function testGetPosts(): void
    {
        $this->repository->addPost(1, 'Test', 1, 'User Nick');

        $this->assertNotEmpty($this->repository->getPosts(1));
    }

    public function testGetPost(): void
    {
        $postId = $this->repository->addPost(1, 'Test', 1, 'User Nick');

        $this->assertNotNull($this->repository->getPost($postId));
    }

    public function testDeletePost(): void
    {
        $postId = $this->repository->addPost(1, 'Test', 1, 'User Nick');

        $this->assertNotNull($this->repository->getPost($postId));

        $this->repository->deletePost($postId);

        $this->assertNull($this->repository->getPost($postId));
    }
}
