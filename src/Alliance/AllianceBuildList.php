<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceBuildList
{
    public int $id;
    public int $allianceId;
    public int $buildingId;
    public int $level;
    public int $buildStartTime;
    public int $buildEndTime;
    public int $cooldown;
    public int $memberFor;

    public function __construct(array $data)
    {
        $this->id = (int) $data['alliance_buildlist_id'];
        $this->allianceId = (int) $data['alliance_buildlist_alliance_id'];
        $this->buildingId = (int) $data['alliance_buildlist_building_id'];
        $this->level = (int) $data['alliance_buildlist_current_level'];
        $this->buildStartTime = (int) $data['alliance_buildlist_build_start_time'];
        $this->buildEndTime = (int) $data['alliance_buildlist_build_end_time'];
        $this->cooldown = (int) $data['alliance_buildlist_cooldown'];
        $this->memberFor = (int) $data['alliance_buildlist_member_for'];
    }

    public function isUnderConstruction(): bool
    {
        return $this->buildEndTime > time();
    }
}
