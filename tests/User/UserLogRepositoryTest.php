<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\SymfonyWebTestCase;

class UserLogRepositoryTest extends SymfonyWebTestCase
{
    private UserLogRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(UserLogRepository::class);

        $this->repository->deleteAll();
    }

    public function testGetUserLogs(): void
    {
        $this->repository->add(1, 'zone', 'message', 'localhost', true);

        $this->assertNotEmpty($this->repository->getUserLogs(1, 100));
        $this->assertNotEmpty($this->repository->getUserLogs(1, 100, true));
        $this->assertEmpty($this->repository->getUserLogs(1, 100, false));
    }
}
