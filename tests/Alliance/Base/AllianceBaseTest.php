<?php declare(strict_types=1);

namespace EtoA\Alliance\Base;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnologyId;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\SymfonyWebTestCase;
use EtoA\User\UserRepository;

class AllianceBaseTest extends SymfonyWebTestCase
{
    private AllianceBase $base;
    private AllianceRepository $allianceRepository;
    private AllianceTechnologyRepository $allianceTechnologyRepository;
    private AllianceBuildingRepository $allianceBuildingRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = self::getContainer()->get(AllianceBase::class);
        $this->allianceRepository = self::getContainer()->get(AllianceRepository::class);
        $this->allianceTechnologyRepository = self::getContainer()->get(AllianceTechnologyRepository::class);
        $this->allianceBuildingRepository = self::getContainer()->get(AllianceBuildingRepository::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testGetTechnologyBuildStatus(): void
    {
        $allianceId = $this->allianceRepository->create('TAG', 'NAME', 1);
        $this->createUser(1, 0, $allianceId);

        $alliance = $this->allianceRepository->getAlliance($allianceId);
        $this->assertNotNull($alliance);

        $technologies = $this->allianceTechnologyRepository->findAll();

        $status = $this->base->getTechnologyBuildStatus($alliance, $technologies[AllianceTechnologyId::SPY], null, AllianceItemRequirementStatus::createForTechnologies($technologies, []));
        $this->assertSame(AllianceItemBuildStatus::STATUS_MISSING_REQUIREMENTS, $status->status);

        $status = $this->base->getTechnologyBuildStatus($alliance, $technologies[AllianceTechnologyId::TARN], null, AllianceItemRequirementStatus::createForTechnologies($technologies, []));
        $this->assertSame(AllianceItemBuildStatus::STATUS_MISSING_RESOURCE, $status->status);

        $alliance->resMetal = 100000;
        $alliance->resCrystal = 100000;
        $alliance->resPlastic = 100000;
        $alliance->resFuel = 100000;
        $alliance->resFood = 100000;

        $status = $this->base->getTechnologyBuildStatus($alliance, $technologies[AllianceTechnologyId::TARN], null, AllianceItemRequirementStatus::createForTechnologies($technologies, []));
        $this->assertSame(AllianceItemBuildStatus::STATUS_OK, $status->status);
    }

    public function testBuildTechnology(): void
    {
        $allianceId = $this->allianceRepository->create('TAG', 'NAME', 1);
        $this->createUser(1, 0, $allianceId);

        $user = $this->userRepository->getUser(1);
        $alliance = $this->allianceRepository->getAlliance($allianceId);
        $technologies = $this->allianceTechnologyRepository->findAll();

        $this->assertNotNull($user);
        $this->assertNotNull($alliance);

        $alliance->resMetal = 100000;
        $alliance->resCrystal = 100000;
        $alliance->resPlastic = 100000;
        $alliance->resFuel = 100000;
        $alliance->resFood = 100000;

        $costs = $this->base->buildTechnology($user, $alliance, $technologies[AllianceTechnologyId::TARN], null, AllianceItemRequirementStatus::createForTechnologies($technologies, []));

        $alliance = $this->allianceRepository->getAlliance($allianceId);

        $this->assertNotNull($alliance);
        $this->assertSame($costs->metal, -$alliance->resMetal);
        $this->assertSame($costs->crystal, -$alliance->resCrystal);
        $this->assertSame($costs->plastic, -$alliance->resPlastic);
        $this->assertSame($costs->fuel, -$alliance->resFuel);
        $this->assertSame($costs->food, -$alliance->resFood);

        $this->assertNotNull($this->allianceTechnologyRepository->getInProgress($allianceId));
    }

    public function testGetBuildingBuildStatus(): void
    {
        $allianceId = $this->allianceRepository->create('TAG', 'NAME', 1);
        $this->createUser(1, 0, $allianceId);

        $alliance = $this->allianceRepository->getAlliance($allianceId);
        $buildings = $this->allianceBuildingRepository->findAll();

        $this->assertNotNull($alliance);

        $status = $this->base->getBuildingBuildStatus($alliance, $buildings[AllianceBuildingId::FLEET_CONTROL], null, AllianceItemRequirementStatus::createForBuildings($buildings, []));
        $this->assertSame(AllianceItemBuildStatus::STATUS_MISSING_REQUIREMENTS, $status->status);

        $status = $this->base->getBuildingBuildStatus($alliance, $buildings[AllianceBuildingId::MAIN], null, AllianceItemRequirementStatus::createForBuildings($buildings, []));
        $this->assertSame(AllianceItemBuildStatus::STATUS_MISSING_RESOURCE, $status->status);

        $alliance->resMetal = 100000;
        $alliance->resCrystal = 100000;
        $alliance->resPlastic = 100000;
        $alliance->resFuel = 100000;
        $alliance->resFood = 100000;

        $status = $this->base->getBuildingBuildStatus($alliance, $buildings[AllianceBuildingId::MAIN], null, AllianceItemRequirementStatus::createForBuildings($buildings, []));
        $this->assertSame(AllianceItemBuildStatus::STATUS_OK, $status->status);
    }

    public function testBuildBuilding(): void
    {
        $allianceId = $this->allianceRepository->create('TAG', 'NAME', 1);
        $this->createUser(1, 0, $allianceId);

        $user = $this->userRepository->getUser(1);
        $alliance = $this->allianceRepository->getAlliance($allianceId);

        $this->assertNotNull($user);
        $this->assertNotNull($alliance);

        $buildings = $this->allianceBuildingRepository->findAll();

        $alliance->resMetal = 100000;
        $alliance->resCrystal = 100000;
        $alliance->resPlastic = 100000;
        $alliance->resFuel = 100000;
        $alliance->resFood = 100000;

        $costs = $this->base->buildBuilding($user, $alliance, $buildings[AllianceBuildingId::MAIN], null, AllianceItemRequirementStatus::createForBuildings($buildings, []));

        $alliance = $this->allianceRepository->getAlliance($allianceId);
        $this->assertNotNull($alliance);

        $this->assertSame($costs->metal, -$alliance->resMetal);
        $this->assertSame($costs->crystal, -$alliance->resCrystal);
        $this->assertSame($costs->plastic, -$alliance->resPlastic);
        $this->assertSame($costs->fuel, -$alliance->resFuel);
        $this->assertSame($costs->food, -$alliance->resFood);

        $this->assertNotNull($this->allianceBuildingRepository->getInProgress($allianceId));
    }
}
