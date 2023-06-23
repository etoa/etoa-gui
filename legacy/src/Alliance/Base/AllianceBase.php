<?php declare(strict_types=1);

namespace EtoA\Alliance\Base;

use EtoA\Alliance\AllianceBuilding;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceBuildListItem;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnology;
use EtoA\Alliance\AllianceTechnologyListItem;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Alliance\AllianceWithMemberCount;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\User;

class AllianceBase
{
    private ConfigurationService $config;
    private AllianceRepository $allianceRepository;
    private AllianceHistoryRepository $allianceHistoryRepository;
    private AllianceTechnologyRepository $allianceTechnologyRepository;
    private AllianceBuildingRepository $allianceBuildingRepository;

    public function __construct(ConfigurationService $config, AllianceRepository $allianceRepository, AllianceHistoryRepository $allianceHistoryRepository, AllianceTechnologyRepository $allianceTechnologyRepository, AllianceBuildingRepository $allianceBuildingRepository)
    {
        $this->config = $config;
        $this->allianceRepository = $allianceRepository;
        $this->allianceHistoryRepository = $allianceHistoryRepository;
        $this->allianceTechnologyRepository = $allianceTechnologyRepository;
        $this->allianceBuildingRepository = $allianceBuildingRepository;
    }

    public function getTechnologyBuildStatus(AllianceWithMemberCount $alliance, AllianceTechnology $technology, ?AllianceTechnologyListItem $item, AllianceItemRequirementStatus $requirementStatus): AllianceItemBuildStatus
    {
        if (!$requirementStatus->requirementsMet($technology->id)) {
            return AllianceItemBuildStatus::missingRequirements();
        }

        $level = $item !== null ? $item->level + 1 : 1;
        if ($technology->lastLevel <= $level) {
            return AllianceItemBuildStatus::maxLevel();
        }

        if ($item !== null && $item->buildEndTime > 0) {
            return AllianceItemBuildStatus::itemUnderConstruction();
        }

        if ($requirementStatus->isUnderConstruction()) {
            return AllianceItemBuildStatus::underConstruction();
        }

        $allianceResources = $alliance->getResources();
        $costs = $technology->calculateCosts($level, $alliance->memberCount, $this->config->getFloat('alliance_membercosts_factor'));

        $missingResources = $costs->missing($allianceResources);
        if ($missingResources->getSum() > 0) {
            return AllianceItemBuildStatus::missingResources($missingResources);
        }

        return AllianceItemBuildStatus::ok();
    }

    public function buildTechnology(User $user, AllianceWithMemberCount $alliance, AllianceTechnology $technology, ?AllianceTechnologyListItem $item, AllianceItemRequirementStatus $requirementStatus): BaseResources
    {
        $status = $this->getTechnologyBuildStatus($alliance, $technology, $item, $requirementStatus);
        if ($status->status !== AllianceItemBuildStatus::STATUS_OK) {
            throw new \RuntimeException(AllianceItemBuildStatus::STATUS_MESSAGES[$status->status]);
        }

        $level = $item !== null ? $item->level + 1 : 1;
        $costs = $technology->calculateCosts($level, $alliance->memberCount, $this->config->getFloat('alliance_membercosts_factor'));

        $startTime = time();
        $endTime = $startTime + $technology->buildTime * $level;
        $this->allianceRepository->addResources($alliance->id, -$costs->metal, -$costs->crystal, -$costs->plastic, -$costs->fuel, -$costs->food);
        if ($level === 1) {
            $this->allianceTechnologyRepository->addToAlliance($alliance->id, $technology->id, 0, $alliance->memberCount, $startTime, $endTime);
        } else {
            $this->allianceTechnologyRepository->updateForAlliance($alliance->id, $technology->id, $level - 1, $alliance->memberCount, $startTime, $endTime);
        }

        $this->allianceHistoryRepository->addEntry($alliance->id, "[b]" . $user->nick . "[/b] hat die Forschung [b]" . $technology->name . " (" . $level . ")[/b] in Auftrag gegeben.");

        return $costs;
    }

    public function getBuildingBuildStatus(AllianceWithMemberCount $alliance, AllianceBuilding $building, ?AllianceBuildListItem $item, AllianceItemRequirementStatus $requirementStatus): AllianceItemBuildStatus
    {
        if (!$requirementStatus->requirementsMet($building->id)) {
            return AllianceItemBuildStatus::missingRequirements();
        }

        $level = $item !== null ? $item->level + 1 : 1;
        if ($building->lastLevel <= $level) {
            return AllianceItemBuildStatus::maxLevel();
        }

        if ($item !== null && $item->buildEndTime > 0) {
            return AllianceItemBuildStatus::itemUnderConstruction();
        }

        if ($requirementStatus->isUnderConstruction()) {
            return AllianceItemBuildStatus::underConstruction();
        }

        $allianceResources = $alliance->getResources();
        $costs = $building->calculateCosts($level, $alliance->memberCount, $this->config->getFloat('alliance_membercosts_factor'));

        $missingResources = $costs->missing($allianceResources);
        if ($missingResources->getSum() > 0) {
            return AllianceItemBuildStatus::missingResources($missingResources);
        }

        return AllianceItemBuildStatus::ok();
    }

    public function buildBuilding(User $user, AllianceWithMemberCount $alliance, AllianceBuilding $building, ?AllianceBuildListItem $item, AllianceItemRequirementStatus $requirementStatus): BaseResources
    {
        $status = $this->getBuildingBuildStatus($alliance, $building, $item, $requirementStatus);
        if ($status->status !== AllianceItemBuildStatus::STATUS_OK) {
            throw new \RuntimeException(AllianceItemBuildStatus::STATUS_MESSAGES[$status->status]);
        }

        $level = $item !== null ? $item->level + 1 : 1;
        $costs = $building->calculateCosts($level, $alliance->memberCount, $this->config->getFloat('alliance_membercosts_factor'));

        $startTime = time();
        $endTime = $startTime + $building->buildTime * $level;
        $this->allianceRepository->addResources($alliance->id, -$costs->metal, -$costs->crystal, -$costs->plastic, -$costs->fuel, -$costs->food);
        if ($level === 1) {
            $this->allianceBuildingRepository->addToAlliance($alliance->id, $building->id, 0, $alliance->memberCount, $startTime, $endTime);
        } else {
            $this->allianceBuildingRepository->updateForAlliance($alliance->id, $building->id, $level - 1, $alliance->memberCount, $startTime, $endTime);
        }

        $this->allianceHistoryRepository->addEntry($alliance->id, "[b]" . $user->nick . "[/b] hat die Forschung [b]" . $building->name . " (" . $level . ")[/b] in Auftrag gegeben.");

        return $costs;
    }
}
