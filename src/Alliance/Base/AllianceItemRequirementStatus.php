<?php declare(strict_types=1);

namespace EtoA\Alliance\Base;

use EtoA\Entity\AllianceBuilding;
use EtoA\Entity\AllianceBuildListItem;
use EtoA\Entity\AllianceTechnology;
use EtoA\Entity\AllianceTechnologyListItem;

class AllianceItemRequirementStatus
{
    /** @var array<int, int> */
    public array $levelList;
    /** @var array<int, array<int, int>> */
    public array $requirementList;
    private bool $underConstruction;

    /**
     * @param array<int, int> $levelList
     * @param array<int, array<int, int>> $requirementList
     */
    private function __construct(array $levelList, array $requirementList, bool $underConstruction)
    {
        $this->levelList = $levelList;
        $this->requirementList = $requirementList;
        $this->underConstruction = $underConstruction;
    }

    /**
     * @param AllianceTechnology[] $technologies
     * @param AllianceTechnologyListItem[] $technologyList
     */
    public static function createForTechnologies(array $technologies, array $technologyList): AllianceItemRequirementStatus
    {
        $levelList = [];
        foreach ($technologyList as $item) {
            $levelList[$item->getTechnology()->getId()] = $item->getLevel();
        }

        $requirementList = [];
        foreach ($technologies as $technology) {
            if ($technology->getNeededLevel() > 0) {
                $requirementList[$technology->getId()][$technology->getNeededId()] = $technology->getNeededLevel();
            }
        }

        return new AllianceItemRequirementStatus($levelList, $requirementList, (bool) array_filter($technologyList, fn (AllianceTechnologyListItem $item) => $item->isUnderConstruction()));
    }

    /**
     * @param AllianceBuilding[] $buildings
     * @param AllianceBuildListItem[] $buildingList
     */
    public static function createForBuildings(array $buildings, array $buildingList): AllianceItemRequirementStatus
    {
        $levelList = [];
        foreach ($buildingList as $item) {
            $levelList[$item->getId()] = $item->getLevel();
        }

        $requirementList = [];
        foreach ($buildings as $bui) {
            if ($bui->getNeededLevel() > 0) {
                $requirementList[$bui->getId()][$bui->getNeededId()] = $bui->getNeededLevel();
            }
        }

        return new AllianceItemRequirementStatus($levelList, $requirementList, (bool) array_filter($buildingList, fn (AllianceBuildListItem $item) => $item->isUnderConstruction()));
    }

    public function requirementsMet(int $itemId): bool
    {
        if (!isset($this->requirementList[$itemId])) {
            return true;
        }

        foreach ($this->requirementList[$itemId] as $requiredItem => $requiredLevel) {
            if (!isset($this->levelList[$requiredItem])) {
                return false;
            }

            if ($this->levelList[$requiredItem] < $requiredLevel) {
                return false;
            }
        }

        return true;
    }

    public function isUnderConstruction(): bool
    {
        return $this->underConstruction;
    }
}
