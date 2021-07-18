<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserPropertiesRepositoryTest extends AbstractDbTestCase
{
    private UserPropertiesRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[UserPropertiesRepository::class];
    }

    public function testGetOrphanedCount(): void
    {
        $this->assertSame(0, $this->repository->getOrphanedCount([1,2]));
    }
}
