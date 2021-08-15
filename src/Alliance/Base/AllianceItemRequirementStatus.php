<?php declare(strict_types=1);

namespace EtoA\Alliance\Base;

use EtoA\Alliance\AllianceBuilding;
use EtoA\Alliance\AllianceBuildListItem;
use EtoA\Alliance\AllianceTechnology;
use EtoA\Alliance\AllianceTechnologyListItem;

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
            $levelList[$item->technologyId] = $item->level;
        }

        $requirementList = [];
        foreach ($technologies as $technology) {
            if ($technology->neededLevel > 0) {
                $requirementList[$technology->id][$technology->neededId] = $technology->neededLevel;
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
            $levelList[$item->buildingId] = $item->level;
        }

        $requirementList = [];
        foreach ($buildings as $bui) {
            if ($bui->neededLevel > 0) {
                $requirementList[$bui->id][$bui->neededId] = $bui->neededLevel;
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
