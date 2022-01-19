<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\SymfonyWebTestCase;

class AllianceMemberCostsTest extends SymfonyWebTestCase
{
    private AllianceMemberCosts $allianceMemberCosts;
    private AllianceRepository $allianceRepository;
    private AllianceBuildingRepository $allianceBuildingRepository;
    private AllianceTechnologyRepository $allianceTechnologyRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->allianceMemberCosts = self::getContainer()->get(AllianceMemberCosts::class);
        $this->allianceRepository = self::getContainer()->get(AllianceRepository::class);
        $this->allianceBuildingRepository = self::getContainer()->get(AllianceBuildingRepository::class);
        $this->allianceTechnologyRepository = self::getContainer()->get(AllianceTechnologyRepository::class);
    }

    public function testCalculate(): void
    {
        $allianceId = $this->allianceRepository->create('TAG', 'Alliance', 1);

        $this->allianceBuildingRepository->addToAlliance($allianceId, AllianceBuildingId::MAIN, 1, 1);
        $this->allianceTechnologyRepository->addToAlliance($allianceId, AllianceTechnologyId::TARN, 1, 1);

        $costs = $this->allianceMemberCosts->calculate($allianceId, 1, 10);

        $this->assertGreaterThan(0, $costs->getSum());
    }

    public function testIncrease(): void
    {
        $allianceId = $this->allianceRepository->create('TAG', 'Alliance', 1);

        $this->allianceBuildingRepository->addToAlliance($allianceId, AllianceBuildingId::MAIN, 1, 1);
        $this->allianceTechnologyRepository->addToAlliance($allianceId, AllianceTechnologyId::TARN, 1, 1);

        $costs = $this->allianceMemberCosts->increase($allianceId, 1, 10);

        $alliance = $this->allianceRepository->getAlliance($allianceId);

        $this->assertNotNull($alliance);
        $this->assertSame($costs->metal, -$alliance->resMetal);
        $this->assertSame($costs->crystal, -$alliance->resCrystal);
        $this->assertSame($costs->plastic, -$alliance->resPlastic);
        $this->assertSame($costs->fuel, -$alliance->resFuel);
        $this->assertSame($costs->food, -$alliance->resFood);
    }
}
