<?php declare(strict_types=1);

namespace EtoA\Alliance\Base;

use EtoA\AbstractDbTestCase;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnologyId;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\User\UserRepository;

class AllianceBaseTest extends AbstractDbTestCase
{
    private AllianceBase $base;
    private AllianceRepository $allianceRepository;
    private AllianceTechnologyRepository $allianceTechnologyRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = $this->app[AllianceBase::class];
        $this->allianceRepository = $this->app[AllianceRepository::class];
        $this->allianceTechnologyRepository = $this->app[AllianceTechnologyRepository::class];
        $this->userRepository = $this->app[UserRepository::class];
    }

    public function testGetTechnologyBuildStatus(): void
    {
        $allianceId = $this->allianceRepository->create('TAG', 'NAME', 1);
        $this->createUser(1, 0, $allianceId);

        $alliance = $this->allianceRepository->getAlliance($allianceId);
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

        $alliance->resMetal = 100000;
        $alliance->resCrystal = 100000;
        $alliance->resPlastic = 100000;
        $alliance->resFuel = 100000;
        $alliance->resFood = 100000;

        $costs = $this->base->buildTechnology($user, $alliance, $technologies[AllianceTechnologyId::TARN], null, AllianceItemRequirementStatus::createForTechnologies($technologies, []));

        $alliance = $this->allianceRepository->getAlliance($allianceId);
        $this->assertSame($costs->metal, -$alliance->resMetal);
        $this->assertSame($costs->crystal, -$alliance->resCrystal);
        $this->assertSame($costs->plastic, -$alliance->resPlastic);
        $this->assertSame($costs->fuel, -$alliance->resFuel);
        $this->assertSame($costs->food, -$alliance->resFood);
    }
}
