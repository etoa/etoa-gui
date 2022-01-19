<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\SymfonyWebTestCase;

class ChatRepositoryTest extends SymfonyWebTestCase
{
    private ChatRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(ChatRepository::class);
    }

    public function testGetMessagesAfter(): void
    {
        $this->repository->addSystemMessage('one');

        $messages = $this->repository->getMessagesAfter(0);

        $this->assertNotEmpty($messages);
    }

    public function testCleanupMessage(): void
    {
        $this->repository->addSystemMessage('one');
        $this->repository->addSystemMessage('two');
        $this->repository->addSystemMessage('three');
        $this->repository->addSystemMessage('four');
        $this->repository->addSystemMessage('five');

        $deleted = $this->repository->cleanupMessage(2);

        $this->assertSame(3, $deleted);
    }

    public function testAddMessage(): void
    {
        $this->repository->addMessage(1, 'Nick', 'one', '', 0);

        $messages = $this->repository->getMessagesAfter(0);

        $this->assertNotEmpty($messages);
    }
}
