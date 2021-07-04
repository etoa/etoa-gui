<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

use EtoA\AbstractDbTestCase;

class DefaultItemRepositoryTest extends AbstractDbTestCase
{
    private DefaultItemRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[DefaultItemRepository::class];
    }

    public function testGetSets(): void
    {
        $this->repository->createSet('Test');

        $sets = $this->repository->getSets(false);

        $this->assertNotEmpty($sets);
    }

    public function testToggleSetActive(): void
    {
        $this->repository->createSet('Test');

        $sets = $this->repository->getSets(false);

        $this->assertNotEmpty($sets);
        $this->assertFalse($sets[0]->active);

        $this->repository->toggleSetActive($sets[0]->id);

        $sets = $this->repository->getSets();

        $this->assertNotEmpty($sets);
        $this->assertTrue($sets[0]->active);
    }

    public function testDeleteSet(): void
    {
        $this->repository->createSet('Test');

        $sets = $this->repository->getSets(false);

        $this->assertNotEmpty($sets);

        $this->repository->deleteSet($sets[0]->id);

        $sets = $this->repository->getSets();

        $this->assertEmpty($sets);
    }
}
