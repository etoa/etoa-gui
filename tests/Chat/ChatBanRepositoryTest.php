<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\AbstractDbTestCase;

class ChatBanRepositoryTest extends AbstractDbTestCase
{
    private ChatBanRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[ChatBanRepository::class];
    }

    public function testGetUserBan(): void
    {
        $userId = 1;
        $this->createUser($userId);
        $this->repository->banUser($userId, 'Test', true);

        $ban = $this->repository->getUserBan($userId);

        $this->assertNotNull($ban);
        $this->assertSame($userId, $ban->userId);
        $this->assertSame('Test', $ban->reason);
    }

    public function testGetBans(): void
    {
        $userId = 1;
        $this->createUser($userId);
        $this->repository->banUser($userId, 'Test');

        $bans = $this->repository->getBans();

        $this->assertNotEmpty($bans);
    }

    public function testDeleteBan(): void
    {
        $userId = 1;
        $this->createUser($userId);
        $this->repository->banUser($userId, 'Test');

        $ban = $this->repository->getUserBan($userId);

        $this->assertNotNull($ban);

        $number = $this->repository->deleteBan($userId);
        $this->assertSame(1, $number);
    }
}
