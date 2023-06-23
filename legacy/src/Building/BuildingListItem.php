<?php

declare(strict_types=1);

namespace EtoA\Building;

class BuildingListItem
{
    public int $id;
    public int $userId;
    public int $buildingId;
    public int $entityId;
    public int $currentLevel;
    public int $startTime;
    public int $endTime;
    public int $buildType;
    public int $prodPercent;
    public int $peopleWorking;
    public int $peopleWorkingStatus;
    public int $deactivated;
    public int $cooldown;

    public static function createFromData(array $data): BuildingListItem
    {
        $item = new BuildingListItem();
        $item->id = (int) $data['buildlist_id'];
        $item->userId = (int) $data['buildlist_user_id'];
        $item->buildingId = (int) $data['buildlist_building_id'];
        $item->entityId = (int) $data['buildlist_entity_id'];
        $item->currentLevel = (int) $data['buildlist_current_level'];
        $item->startTime = (int) $data['buildlist_build_start_time'];
        $item->endTime = (int) $data['buildlist_build_end_time'];
        $item->buildType = (int) $data['buildlist_build_type'];
        $item->prodPercent = (int) $data['buildlist_prod_percent'];
        $item->peopleWorking = (int) $data['buildlist_people_working'];
        $item->peopleWorkingStatus = (int) $data['buildlist_people_working_status'];
        $item->deactivated = (int) $data['buildlist_deactivated'];
        $item->cooldown = (int) $data['buildlist_cooldown'];

        return $item;
    }

    public static function empty(): BuildingListItem
    {
        $item = new BuildingListItem();
        $item->id = 0;
        $item->userId = 0;
        $item->buildingId = 0;
        $item->entityId = 0;
        $item->currentLevel = 0;
        $item->startTime = 0;
        $item->endTime = 0;
        $item->buildType = 0;
        $item->prodPercent = 0;
        $item->peopleWorking = 0;
        $item->peopleWorkingStatus = 0;
        $item->deactivated = 0;
        $item->cooldown = 0;

        return $item;
    }

    public function isDeactivated(): bool
    {
        return $this->deactivated > time();
    }

    public function isUnderConstruction(): bool
    {
        return in_array($this->buildType, [3, 4], true) && $this->endTime > time();
    }
}
