<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\SymfonyWebTestCase;

class AllianceShipPointsServiceTest extends SymfonyWebTestCase
{
    private AllianceShipPointsService $service;
    private AllianceService $allianceService;
    private AllianceBuildingRepository $allianceBuildingRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = self::getContainer()->get(AllianceShipPointsService::class);
        $this->allianceService = self::getContainer()->get(AllianceService::class);
        $this->allianceBuildingRepository = self::getContainer()->get(AllianceBuildingRepository::class);
    }

    public function testUpdate(): void
    {
        $founderId = 23;
        $this->createUser($founderId);

        $alliance = $this->allianceService->create('DEV', 'The Developers', $founderId);

        $this->assertSame(0, $this->service->update());

        $this->allianceBuildingRepository->addToAlliance($alliance->id, AllianceBuildingId::SHIPYARD, 10, 1);

        $this->assertSame(1, $this->service->update());
    }

    public function testUpdateEmpty(): void
    {
        $this->assertSame(0, $this->service->update());
    }
}
