<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserHolidayServiceTest extends AbstractDbTestCase
{
    private UserHolidayService $service;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[UserHolidayService::class];
        $this->userRepository = $this->app[UserRepository::class];
    }

    public function testActivateHolidayMode(): void
    {
        $this->assertTrue($this->service->activateHolidayMode(1));
    }

    public function testDeactivateHolidayMode(): void
    {
        $this->createUser(1);
        $user = $this->userRepository->getUser(1);

        $this->assertFalse($this->service->deactivateHolidayMode($user));

        $user->hmodFrom = time() - 3600;
        $user->hmodTo = time() - 1800;

        $this->assertTrue($this->service->deactivateHolidayMode($user));
    }
}
