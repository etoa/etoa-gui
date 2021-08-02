<?php declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\AbstractDbTestCase;

class BookmarkRepositoryTest extends AbstractDbTestCase
{
    private BookmarkRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[BookmarkRepository::class];
    }

    public function testHasEntityBookmark(): void
    {
        $this->repository->add(1, 2, 'Test');

        $this->assertTrue($this->repository->hasEntityBookmark(1, 2));
        $this->assertFalse($this->repository->hasEntityBookmark(2, 2));
        $this->assertFalse($this->repository->hasEntityBookmark(2, 1));
    }

    public function testFindForUser(): void
    {
        $this->repository->add(1, 2, 'Test');

        $this->assertNotEmpty($this->repository->findForUser(1));
    }

    public function testRemove(): void
    {
        $id = $this->repository->add(1, 2, 'Test');

        $this->assertFalse($this->repository->remove($id, 3));
        $this->assertTrue($this->repository->remove($id, 1));
    }
}
