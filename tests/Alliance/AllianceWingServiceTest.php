<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AllianceWingServiceTest extends AbstractDbTestCase
{
    private AllianceWingService $wingService;
    private AllianceService $allianceService;
    private AllianceRepository $allianceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wingService = $this->app[AllianceWingService::class];
        $this->allianceService = $this->app[AllianceService::class];
        $this->allianceRepository = $this->app[AllianceRepository::class];
    }

    public function testAcceptWingRequest(): void
    {
        $founderId = 1;
        $this->createUser($founderId);

        $wingFounderId = 2;
        $this->createUser($wingFounderId);

        $alliance = $this->allianceService->create('Tag', 'Alliance', $founderId);
        $wing = $this->allianceService->create('Wing', 'Wing', $wingFounderId);

        $this->assertTrue($this->wingService->addWingRequest($alliance, $wing));

        $wing = $this->allianceRepository->getAlliance($wing->id);

        $this->assertTrue($this->wingService->acceptWingRequest($alliance, $wing));

        $wing = $this->allianceRepository->getAlliance($wing->id);

        $this->assertTrue($this->wingService->removeWing($alliance, $wing));
    }

    public function testCancelWingRequest(): void
    {
        $founderId = 1;
        $this->createUser($founderId);

        $wingFounderId = 2;
        $this->createUser($wingFounderId);

        $alliance = $this->allianceService->create('Tag', 'Alliance', $founderId);
        $wing = $this->allianceService->create('Wing', 'Wing', $wingFounderId);

        $this->assertTrue($this->wingService->addWingRequest($alliance, $wing));

        $wing = $this->allianceRepository->getAlliance($wing->id);

        $this->assertTrue($this->wingService->cancelWingRequest($alliance, $wing));
    }

    public function testDeclineWingRequest(): void
    {
        $founderId = 1;
        $this->createUser($founderId);

        $wingFounderId = 2;
        $this->createUser($wingFounderId);

        $alliance = $this->allianceService->create('Tag', 'Alliance', $founderId);
        $wing = $this->allianceService->create('Wing', 'Wing', $wingFounderId);

        $this->assertTrue($this->wingService->addWingRequest($alliance, $wing));

        $wing = $this->allianceRepository->getAlliance($wing->id);

        $this->assertTrue($this->wingService->declineWingRequest($alliance, $wing));
    }
}
