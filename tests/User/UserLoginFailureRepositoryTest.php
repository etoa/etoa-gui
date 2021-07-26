<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserLoginFailureRepositoryTest extends AbstractDbTestCase
{
    private UserLoginFailureRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[UserLoginFailureRepository::class];
    }

    public function testGetUserLoginFailures(): void
    {
        $this->repository->add(1, time(), 'localhost', 'client');

        $this->assertNotEmpty($this->repository->getUserLoginFailures(1));
    }

    public function testCountLoginFailuresSince(): void
    {
        $this->repository->add(1, time(), 'localhost', 'client');

        $this->assertSame(1, $this->repository->countLoginFailuresSince(1, 0));
    }

    public function testGetLoginFailureCountsByIp(): void
    {
        $this->repository->add(1, time(), 'localhost', 'client');

        $this->assertSame([['userId' => 1, 'userNick' => null, 'count' => 1]], $this->repository->getLoginFailureCountsByIp('localhost'));
    }

    public function testGetLoginFailureCountsByUser(): void
    {
        $this->repository->add(1, time(), 'localhost', 'client');

        $this->assertSame([['ip' => 'localhost', 'host' => null, 'count' => 1]], $this->repository->getLoginFailureCountsByUser(1));
    }

    public function testFindLoginFailures(): void
    {
        $this->repository->add(1, time(), 'localhost', 'client');

        $this->assertNotEmpty($this->repository->findLoginFailures('failure_time', 'DESC'));
    }
}
