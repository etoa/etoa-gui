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

    public function __construct(array $data)
    {
        $this->id = (int) $data['buildlist_id'];
        $this->userId = (int) $data['buildlist_user_id'];
        $this->buildingId = (int) $data['buildlist_building_id'];
        $this->entityId = (int) $data['buildlist_entity_id'];
        $this->currentLevel = (int) $data['buildlist_current_level'];
        $this->startTime = (int) $data['buildlist_build_start_time'];
        $this->endTime = (int) $data['buildlist_build_end_time'];
        $this->buildType = (int) $data['buildlist_build_type'];
        $this->prodPercent = (int) $data['buildlist_prod_percent'];
        $this->peopleWorking = (int) $data['buildlist_people_working'];
        $this->peopleWorkingStatus = (int) $data['buildlist_people_working_status'];
        $this->deactivated = (int) $data['buildlist_deactivated'];
        $this->cooldown = (int) $data['buildlist_cooldown'];
    }
}
