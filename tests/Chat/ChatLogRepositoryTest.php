<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\AbstractDbTestCase;

class ChatLogRepositoryTest extends AbstractDbTestCase
{
    private ChatLogRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[ChatLogRepository::class];
    }

    public function testGetChatLogs(): void
    {
        $this->repository->addLog(1, 'User', 'test', '', 0);

        $logs = $this->repository->getLogs();

        $this->assertNotEmpty($logs);
    }
}
