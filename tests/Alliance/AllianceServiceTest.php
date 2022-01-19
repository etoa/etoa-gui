<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\SymfonyWebTestCase;
use EtoA\User\UserRepository;

class AllianceServiceTest extends SymfonyWebTestCase
{
    private AllianceService $service;
    private AllianceRepository $repository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = self::getContainer()->get(AllianceService::class);
        $this->repository = self::getContainer()->get(AllianceRepository::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testCreate(): void
    {
        // given
        $tag = 'TEST';
        $name = 'The Testers';
        $founderId = $this->userRepository->create('tester', 'John Doe', 'tester@example.com', '12345678');
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        // when
        $alliance = $this->service->create($tag, $name, $founderId);

        // then
        $this->assertEquals($tag, $alliance->tag);
        $this->assertEquals($name, $alliance->name);
        $this->assertEquals($founderId, $alliance->founderId);
        $this->assertNotNull($this->repository->getAlliance($alliance->id));
    }

    public function testAddMember(): void
    {
        $tag = 'TEST';
        $name = 'The Testers';
        $founderId = $this->userRepository->create('tester', 'John Doe', 'tester@example.com', '12345678');
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $otherUserId = $this->userRepository->create('other', 'Other Doe', 'other@example.com', '12345678');
        $otherUser = $this->userRepository->getUser($otherUserId);

        $alliance = $this->service->create($tag, $name, $founderId);

        $this->assertNotNull($otherUser);
        $this->assertTrue($this->service->addMember($alliance, $otherUser));
    }

    public function testKickMember(): void
    {
        $tag = 'TEST';
        $name = 'The Testers';
        $founderId = $this->userRepository->create('tester', 'John Doe', 'tester@example.com', '12345678');
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $otherUserId = $this->userRepository->create('other', 'Other Doe', 'other@example.com', '12345678');
        $otherUser = $this->userRepository->getUser($otherUserId);

        $alliance = $this->service->create($tag, $name, $founderId);

        $this->assertNotNull($otherUser);
        $this->assertTrue($this->service->addMember($alliance, $otherUser));

        $otherUser = $this->userRepository->getUser($otherUserId);

        $this->assertNotNull($otherUser);
        $this->assertTrue($this->service->kickMember($alliance, $otherUser));
    }

    public function testChangeFounder(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $tag = 'TEST';
        $name = 'The Testers';
        $founderId = $this->userRepository->create('tester', 'John Doe', 'tester@example.com', '12345678');

        $alliance = $this->service->create($tag, $name, $founderId);
        $founder = $this->userRepository->getUser($founderId);

        $this->assertNotNull($founder);
        $founder->allianceId = $alliance->id;

        $this->assertTrue($this->service->changeFounder($alliance, $founder));
    }

    public function testDelete(): void
    {
        $tag = 'TEST';
        $name = 'The Testers';
        $founderId = $this->userRepository->create('tester', 'John Doe', 'tester@example.com', '12345678');

        $alliance = $this->service->create($tag, $name, $founderId);

        $this->assertTrue($this->service->delete($alliance));
    }
}
