<?php declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\AbstractDbTestCase;
use EtoA\Universe\Resources\BaseResources;

class FleetBookmarkRepositoryTest extends AbstractDbTestCase
{
    private FleetBookmarkRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[FleetBookmarkRepository::class];
    }

    public function testGet(): void
    {
        $id = $this->repository->add(1, 'Test', 2, '1:12', new BaseResources(), new BaseResources(), 'action', 100);

        $this->assertNotNull($this->repository->get($id, 1));
    }

    public function testGetForUser(): void
    {
        $this->repository->add(1, 'Test', 2, '1:12', new BaseResources(), new BaseResources(), 'action', 100);

        $this->assertNotEmpty($this->repository->getForUser(1));
    }

    public function testUpdate(): void
    {
        $id = $this->repository->add(1, 'Test', 2, '1:12', new BaseResources(), new BaseResources(), 'action', 100);

        $resource = new BaseResources();
        $resource->metal = 1;
        $resource->crystal = 2;
        $resource->plastic = 3;
        $resource->fuel = 4;
        $resource->food = 5;
        $resource->people = 6;
        $this->repository->update($id, 1, 'Test', 3, '2:24', new BaseResources(), $resource, 'other', 100);

        $bookmark = $this->repository->get($id, 1);

        $this->assertNotNull($bookmark);
        $this->assertSame([2 => 24], $bookmark->ships);
        $this->assertEquals($resource, $bookmark->fetch);
    }
}
