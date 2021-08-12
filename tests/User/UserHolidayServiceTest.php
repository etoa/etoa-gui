<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserHolidayServiceTest extends AbstractDbTestCase
{
    private UserHolidayService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[UserHolidayService::class];
    }

    public function testActivateHolidayMode(): void
    {
        $this->assertTrue($this->service->activateHolidayMode(1));
    }
}
