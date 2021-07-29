<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\User\UserRepository;
use EtoA\WebTestCase;

class AllianceServiceTest extends WebTestCase
{
    private AllianceService $service;
    private AllianceRepository $repository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[AllianceService::class];
        $this->repository = $this->app[AllianceRepository::class];
        $this->userRepository = $this->app[UserRepository::class];
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
}
