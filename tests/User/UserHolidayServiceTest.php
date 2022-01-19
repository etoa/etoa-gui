<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\SymfonyWebTestCase;

class UserHolidayServiceTest extends SymfonyWebTestCase
{
    private UserHolidayService $service;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = self::getContainer()->get(UserHolidayService::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testActivateHolidayMode(): void
    {
        $this->assertTrue($this->service->activateHolidayMode(1));
    }

    public function testDeactivateHolidayMode(): void
    {
        $this->createUser(1);
        $user = $this->userRepository->getUser(1);

        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse($this->service->deactivateHolidayMode($user));

        $user->hmodFrom = time() - 3600;
        $user->hmodTo = time() - 1800;

        $this->assertTrue($this->service->deactivateHolidayMode($user));
    }
}
