<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use function nf;

class AllianceMemberCosts
{
    private AllianceBuildingRepository $allianceBuildingRepository;
    private AllianceTechnologyRepository $allianceTechnologyRepository;
    private ConfigurationService $configuration;
    private AllianceRepository $allianceRepository;
    private AllianceHistoryRepository $allianceHistoryRepository;

    public function __construct(AllianceBuildingRepository $allianceBuildingRepository, AllianceTechnologyRepository $allianceTechnologyRepository, ConfigurationService $configuration, AllianceRepository $allianceRepository, AllianceHistoryRepository $allianceHistoryRepository)
    {
        $this->allianceBuildingRepository = $allianceBuildingRepository;
        $this->allianceTechnologyRepository = $allianceTechnologyRepository;
        $this->configuration = $configuration;
        $this->allianceRepository = $allianceRepository;
        $this->allianceHistoryRepository = $allianceHistoryRepository;
    }

    public function increase(int $allianceId, int $currentMemberCount, int $newMemberCount): BaseResources
    {
        $costs = $this->calculate($allianceId, $currentMemberCount, $newMemberCount);

        $this->allianceBuildingRepository->updateMembersForAlliance($allianceId, $newMemberCount);
        $this->allianceTechnologyRepository->updateMembersForAlliance($allianceId, $newMemberCount);

        if ($costs->getSum() > 0) {
            $this->allianceRepository->addResources($allianceId, -$costs->metal, -$costs->crystal, -$costs->plastic, -$costs->fuel, -$costs->food, $newMemberCount);

            $this->allianceHistoryRepository->addEntry($allianceId, "Dem Allianzkonto wurden folgende Rohstoffe abgezogen:\n[b]" . ResourceNames::METAL . "[/b]: " . nf($costs->metal) . "\n[b]" . ResourceNames::CRYSTAL . "[/b]: " . nf($costs->crystal) . "\n[b]" . ResourceNames::PLASTIC . "[/b]: " . nf($costs->plastic) . "\n[b]" . ResourceNames::FUEL . "[/b]: " . nf($costs->fuel) . "\n[b]" . ResourceNames::FOOD . "[/b]: " . nf($costs->food) . "\n\nDie Allianzobjekte sind nun für " . $newMemberCount . " Mitglieder verfügbar!");
        }

        return $costs;
    }

    public function calculate(int $allianceId, int $currentMemberCount, int $newMemberCount): BaseResources
    {
        $currentCosts = new BaseResources();
        $newCosts = new BaseResources();

        $memberCostsFactor = $this->configuration->getFloat('alliance_membercosts_factor');

        $buildList = $this->allianceBuildingRepository->getBuildList($allianceId);
        if (count($buildList) > 0) {
            $buildings = $this->allianceBuildingRepository->findAll();
            foreach ($buildList as $item) {
                if (!isset($buildings[$item->buildingId])) {
                    continue;
                }

                $building = $buildings[$item->buildingId];

                $level = $item->level;
                if ($item->isUnderConstruction()) {
                    $level++;
                }

                for ($x = 1; $x <= $level; $x++) {
                    $currentCosts->add($building->calculateCosts($level, $currentMemberCount, $memberCostsFactor));
                    $newCosts->add($building->calculateCosts($level, $newMemberCount, $memberCostsFactor));
                }
            }
        }

        $techList = $this->allianceTechnologyRepository->getTechnologyList($allianceId);
        if (count($buildList) > 0) {
            $technologies = $this->allianceTechnologyRepository->findAll();
            foreach ($techList as $item) {
                if (!isset($technologies[$item->technologyId])) {
                    continue;
                }

                $technology = $technologies[$item->technologyId];

                $level = $item->level;
                if ($item->isUnderConstruction()) {
                    $level++;
                }

                for ($x = 1; $x <= $level; $x++) {
                    $currentCosts->add($technology->calculateCosts($level, $currentMemberCount, $memberCostsFactor));
                    $newCosts->add($technology->calculateCosts($level, $newMemberCount, $memberCostsFactor));
                }
            }
        }

        $upgradeCosts = clone $newCosts;
        $upgradeCosts->remove($currentCosts);

        return $upgradeCosts;
    }
}
