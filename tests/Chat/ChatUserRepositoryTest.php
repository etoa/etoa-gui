<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\AbstractDbTestCase;

class ChatUserRepositoryTest extends AbstractDbTestCase
{
    private ChatUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[ChatUserRepository::class];
    }

    public function testGetChatUser(): void
    {
        $this->repository->updateChatUser(1, 'User');

        $chatUser = $this->repository->getChatUser(1);

        $this->assertNotEmpty($chatUser);
    }

    public function testGetChatUsers(): void
    {
        $this->repository->updateChatUser(1, 'User');

        $chatUser = $this->repository->getChatUsers();

        $this->assertNotEmpty($chatUser);
    }

    public function testGetTimedOutChatUsers(): void
    {
        $this->repository->updateChatUser(1, 'User');

        $chatUsers = $this->repository->getTimedOutChatUsers(-10);

        $this->assertNotEmpty($chatUsers);

        $chatUsers = $this->repository->getTimedOutChatUsers(10);

        $this->assertEmpty($chatUsers);
    }

    public function testKickUser(): void
    {
        $this->repository->updateChatUser(1, 'User');

        $kicked = $this->repository->kickUser(1, 'Kick');

        $this->assertSame(1, $kicked);
    }

    public function testDeleteUser(): void
    {
        $this->repository->updateChatUser(1, 'User');

        $deleted = $this->repository->deleteUser(1);

        $this->assertSame(1, $deleted);
    }
}
