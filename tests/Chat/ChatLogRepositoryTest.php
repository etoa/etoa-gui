<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\SymfonyWebTestCase;

class ChatLogRepositoryTest extends SymfonyWebTestCase
{
    private ChatLogRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(ChatLogRepository::class);
    }

    public function testGetChatLogs(): void
    {
        $this->repository->addLog(1, 'User', 'test', '', 0);

        $logs = $this->repository->getLogs();

        $this->assertNotEmpty($logs);
    }
}
