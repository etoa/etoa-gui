<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use EtoA\WebTestCase;

class FleetScanServiceTest extends WebTestCase
{
    private FleetScanService $service;
    private UserRepository $userRepository;
    private AllianceRepository $allianceRepository;
    private AllianceBuildingRepository $allianceBuildingRepository;
    private PlanetRepository $planetRepository;
    private CellRepository $cellRepository;
    private EntityRepository $entityRepository;
    private FleetRepository $fleetRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[FleetScanService::class];
        $this->userRepository = $this->app[UserRepository::class];
        $this->allianceRepository = $this->app[AllianceRepository::class];
        $this->allianceBuildingRepository = $this->app[AllianceBuildingRepository::class];
        $this->planetRepository = $this->app[PlanetRepository::class];
        $this->cellRepository = $this->app[CellRepository::class];
        $this->entityRepository = $this->app[EntityRepository::class];
        $this->fleetRepository = $this->app[FleetRepository::class];
    }

    public function testScanFleets_withNoFleets(): void
    {
        // given
        $userId1 = $this->userRepository->create('Aggressor', 'Hans Muster', 'hans@example.com', 'password');
        $userId2 = $this->userRepository->create('Victim', 'Peter Meier', 'peter@example.com', 'password');
        $allianceId1 = $this->allianceRepository->create('TST', 'Testers', $userId1);
        $this->userRepository->setAllianceId($userId1, $allianceId1);
        $this->allianceBuildingRepository->setUserCooldown($userId1, ALLIANCE_CRYPTO_ID, 0);
        $this->allianceRepository->addResources($allianceId1, 0, 0, 0, 10000, 0);

        $cellId1 = $this->cellRepository->create(1, 1, 1, 1);
        $cellId2 = $this->cellRepository->create(1, 1, 2, 2);
        $entityId1 = $this->entityRepository->add($cellId1, EntityType::PLANET, 3);
        $entityId2 = $this->entityRepository->add($cellId2, EntityType::PLANET, 4);
        $this->planetRepository->add($entityId1, 1, 100, '1_1', -20, 50);
        $this->planetRepository->add($entityId2, 1, 100, '1_1', -20, 50);
        $this->planetRepository->assignToUser($entityId1, $userId1);
        $this->planetRepository->assignToUser($entityId2, $userId2);
        $this->planetRepository->addResources($entityId1, 0, 0, 0, 10000, 0, 0);

        $user1 = $this->userRepository->getUser($userId1);
        $planet1 = $this->planetRepository->find($entityId1);
        $entity2 = $this->entityRepository->findIncludeCell($entityId2);

        // when
        $out = $this->service->scanFleets($user1, $planet1, 1, $entity2);

        // then
        $this->assertStringContainsString('[b]Flottenscan vom Planeten [/b] (1/1 : 2/2 : 4)', $out);
        $this->assertStringContainsString('Keine eintreffenden Flotten gefunden!', $out);
        $this->assertStringContainsString('Keine abfliegenden Flotten gefunden!', $out);
    }

    public function testScanFleets_withOneIncomingFleet(): void
    {
        // given
        $userId1 = $this->userRepository->create('Aggressor', 'Hans Muster', 'hans@example.com', 'password');
        $userId2 = $this->userRepository->create('Victim', 'Peter Meier', 'peter@example.com', 'password');
        $allianceId1 = $this->allianceRepository->create('TST', 'Testers', $userId1);
        $this->userRepository->setAllianceId($userId1, $allianceId1);
        $this->allianceBuildingRepository->setUserCooldown($userId1, ALLIANCE_CRYPTO_ID, 0);
        $this->allianceRepository->addResources($allianceId1, 0, 0, 0, 10000, 0);

        $cellId1 = $this->cellRepository->create(1, 1, 1, 1);
        $cellId2 = $this->cellRepository->create(1, 1, 2, 2);
        $entityId1 = $this->entityRepository->add($cellId1, EntityType::PLANET, 3);
        $entityId2 = $this->entityRepository->add($cellId2, EntityType::PLANET, 4);
        $this->planetRepository->add($entityId1, 1, 100, '1_1', -20, 50);
        $this->planetRepository->add($entityId2, 1, 100, '1_1', -20, 50);
        $this->planetRepository->assignToUser($entityId1, $userId1);
        $this->planetRepository->assignToUser($entityId2, $userId2);
        $this->planetRepository->addResources($entityId1, 0, 0, 0, 10000, 0, 0);

        $user1 = $this->userRepository->getUser($userId1);
        $planet1 = $this->planetRepository->find($entityId1);
        $entity2 = $this->entityRepository->findIncludeCell($entityId2);

        $this->fleetRepository->add($userId1, time(), time() + 60, $entityId1, $entityId2, FleetAction::SPY, FleetStatus::DEPARTURE);

        // when
        $out = $this->service->scanFleets($user1, $planet1, 31, $entity2);

        // then
        $this->assertStringContainsString('[b]Flottenscan vom Planeten [/b] (1/1 : 2/2 : 4)', $out);
        $this->assertStringContainsString('Es sind 1 Flotten unterwegs', $out);
        $this->assertStringContainsString('[b]Besitzer:[/b] Aggressor', $out);
        $this->assertStringContainsString('[b]Herkunft:[/b] Planet 1/1 : 1/1 : 3', $out);
        $this->assertStringContainsString('[b]Aktion:[/b] Ausspionieren', $out);
        $this->assertStringContainsString('Keine abfliegenden Flotten gefunden!', $out);
    }
}
