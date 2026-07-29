<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Alliance;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;

class AllianceMemberCosts
{
    public function __construct(
        private readonly ConfigurationService $configuration,
        private readonly AllianceRepository $allianceRepository,
        private readonly AllianceHistoryRepository $allianceHistoryRepository,
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly AllianceTechnologyListRepository $allianceTechnologyListRepository,
        private readonly AllianceService $allianceService
    ){}

    public function increase(Alliance $alliance, int $currentMemberCount, int $newMemberCount): BaseResources
    {
        $costs = $this->calculate($alliance->getId(), $currentMemberCount, $newMemberCount);

        $this->allianceBuildListRepository->updateMembersForAlliance($alliance, $newMemberCount);
        $this->allianceTechnologyListRepository->updateMembersForAlliance($alliance, $newMemberCount);

        if ($costs->getSum() > 0) {
            $this->allianceRepository->addResources($alliance, -$costs->metal, -$costs->crystal, -$costs->plastic, -$costs->fuel, -$costs->food, $newMemberCount);

            $this->allianceHistoryRepository->addEntry($alliance, "Dem Allianzkonto wurden folgende Rohstoffe abgezogen:\n[b]" . ResourceNames::METAL . "[/b]: " . StringUtils::formatNumber($costs->metal) . "\n[b]" . ResourceNames::CRYSTAL . "[/b]: " . StringUtils::formatNumber($costs->crystal) . "\n[b]" . ResourceNames::PLASTIC . "[/b]: " . StringUtils::formatNumber($costs->plastic) . "\n[b]" . ResourceNames::FUEL . "[/b]: " . StringUtils::formatNumber($costs->fuel) . "\n[b]" . ResourceNames::FOOD . "[/b]: " . StringUtils::formatNumber($costs->food) . "\n\nDie Allianzobjekte sind nun für " . $newMemberCount . " Mitglieder verfügbar!");
        }

        return $costs;
    }

    public function calculate(int $allianceId, int $currentMemberCount, int $newMemberCount): BaseResources
    {
        $currentCosts = new BaseResources();
        $newCosts = new BaseResources();

        $memberCostsFactor = $this->configuration->getFloat('alliance_membercosts_factor');

        $buildList = $this->allianceBuildListRepository->findBy(['alliance'=>$allianceId]);
        if (count($buildList) > 0) {
            foreach ($buildList as $item) {
                $building = $item->getAllianceBuilding();

                $level = $item->getLevel();
                if ($item->isUnderConstruction()) {
                    $level++;
                }

                for ($x = 1; $x <= $level; $x++) {
                    $currentCosts->add($this->allianceService->calculateCosts($building,$level, $currentMemberCount, $memberCostsFactor));
                    $newCosts->add($this->allianceService->calculateCosts($building ,$level, $newMemberCount, $memberCostsFactor));
                }
            }
        }

        $techList = $this->allianceTechnologyListRepository->findBy(['alliance'=>$allianceId]);
        if (count($buildList) > 0) {
            foreach ($techList as $item) {
                $technology = $item->getTechnology();

                $level = $item->getLevel();
                if ($item->isUnderConstruction()) {
                    $level++;
                }

                for ($x = 1; $x <= $level; $x++) {
                    $currentCosts->add($this->allianceService->calculateCosts($technology,$level, $currentMemberCount, $memberCostsFactor));
                    $newCosts->add($this->allianceService->calculateCosts($technology,$level, $newMemberCount, $memberCostsFactor));
                }
            }
        }

        $upgradeCosts = clone $newCosts;
        $upgradeCosts->remove($currentCosts);

        return $upgradeCosts;
    }
}
