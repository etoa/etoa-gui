<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\User\UserService;
use EtoA\WebTestCase;

class RankingServiceTest extends WebTestCase
{
    private RankingService $service;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[RankingService::class];
        $this->userService = $this->app[UserService::class];
    }

    public function testCalc_noUsers(): void
    {
        // given

        // when
        $result = $this->service->calc();

        // then
        $this->assertEquals(0, $result->numberOfUsers);
        $this->assertEquals(0, $result->totalPoints);
    }

    public function testCalc_withNewUsers(): void
    {
        // given
        $this->userService->register('Hans Muster', 'hans@example.com', 'Hans', '12345678');
        $this->userService->register('Peter Lustig', 'peter@example.com', 'Peter', '12345678');

        // when
        $result = $this->service->calc();

        // then
        $this->assertEquals(2, $result->numberOfUsers);
        $this->assertEquals(0, $result->totalPoints);
    }

    public function testCalcTitles_noUsers(): void
    {
        // given

        // when
        $this->service->calcTitles();

        // then
        $this->assertFileExists($this->service->getUserTitlesCacheFilePath());
        $this->assertFileExists($this->service->getUserTitlesAdminCacheFilePath());
    }

    public function testCalcTitles_withNewUsers(): void
    {
        // given
        $this->userService->register('Hans Muster', 'hans@example.com', 'Hans', '12345678');
        $this->userService->register('Peter Lustig', 'peter@example.com', 'Peter', '12345678');

        // when
        $this->service->calcTitles();

        // then
        $this->assertFileExists($this->service->getUserTitlesCacheFilePath());
        $this->assertFileExists($this->service->getUserTitlesAdminCacheFilePath());
    }
}
