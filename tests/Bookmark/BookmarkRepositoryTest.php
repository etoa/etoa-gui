<?php declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\AbstractDbTestCase;
use EtoA\Universe\Entity\EntityRepository;

class BookmarkRepositoryTest extends AbstractDbTestCase
{
    private BookmarkRepository $repository;
    private EntityRepository $entityRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[BookmarkRepository::class];
        $this->entityRepository = $this->app[EntityRepository::class];
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
        $entityId = $this->entityRepository->add(1, 'e');
        $this->repository->add(1, $entityId, 'Test');

        $this->assertNotEmpty($this->repository->findForUser(1));
    }

    public function testRemove(): void
    {
        $id = $this->repository->add(1, 2, 'Test');

        $this->assertFalse($this->repository->remove($id, 3));
        $this->assertTrue($this->repository->remove($id, 1));
    }
}
