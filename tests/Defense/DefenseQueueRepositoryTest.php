<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\AbstractDbTestCase;

class DefenseQueueRepositoryTest extends AbstractDbTestCase
{
    private DefenseQueueRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[DefenseQueueRepository::class];
    }

    public function testAdd(): void
    {
        $id = $this->repository->add(1, 2, 3, 4, time(), time() + 20, 5);

        $this->assertNotNull($this->repository->getQueueItem($id));
    }

    public function testSaveQueueItem(): void
    {
        $id = $this->repository->add(1, 2, 3, 4, time(), time() + 20, 5);

        $item = $this->repository->getQueueItem($id);

        $item->count = 99;
        $this->repository->saveQueueItem($item);

        $this->assertSame(99, $this->repository->getQueueItem($id)->count);
    }

    public function testFindQueueItemsForUser(): void
    {
        $this->repository->add(1, 2, 3, 4, time(), time() + 20, 5);

        $this->assertNotEmpty($this->repository->findQueueItemsForUser(1));
    }

    public function testDeleteQueueItem(): void
    {
        $id = $this->repository->add(1, 2, 3, 4, time(), time() + 20, 5);

        $this->assertSame(1, $this->repository->count());

        $this->repository->deleteQueueItem($id);

        $this->assertSame(0, $this->repository->count());
    }
}
