<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceBuildListItem
{
    public int $id;
    public int $allianceId;
    public int $buildingId;
    public int $level;
    public int $buildStartTime;
    public int $buildEndTime;
    public int $cooldown;
    public int $memberFor;

    public static function createFromAlliance(AllianceWithMemberCount $alliance): AllianceBuildListItem
    {
        $item = new AllianceBuildListItem();
        $item->id = 0;
        $item->allianceId = $alliance->id;
        $item->buildingId = 0;
        $item->level = 0;
        $item->buildStartTime = 0;
        $item->buildEndTime = 0;
        $item->cooldown = 0;
        $item->memberFor = $alliance->memberCount;

        return $item;
    }

    public static function createFromData(array $data): AllianceBuildListItem
    {
        $item = new AllianceBuildListItem();
        $item->id = (int) $data['alliance_buildlist_id'];
        $item->allianceId = (int) $data['alliance_buildlist_alliance_id'];
        $item->buildingId = (int) $data['alliance_buildlist_building_id'];
        $item->level = (int) $data['alliance_buildlist_current_level'];
        $item->buildStartTime = (int) $data['alliance_buildlist_build_start_time'];
        $item->buildEndTime = (int) $data['alliance_buildlist_build_end_time'];
        $item->cooldown = (int) $data['alliance_buildlist_cooldown'];
        $item->memberFor = (int) $data['alliance_buildlist_member_for'];

        return $item;
    }

    public function isUnderConstruction(): bool
    {
        return $this->buildEndTime > time();
    }
}
