<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceTechnologyListItem
{
    public int $id;
    public int $allianceId;
    public int $technologyId;
    public int $level;
    public int $buildStartTime;
    public int $buildEndTime;
    public int $memberFor;

    public static function createFromAlliance(AllianceWithMemberCount $alliance): AllianceTechnologyListItem
    {
        $item = new AllianceTechnologyListItem();
        $item->id = 0;
        $item->allianceId = $alliance->id;
        $item->technologyId = 0;
        $item->level = 0;
        $item->buildStartTime = 0;
        $item->buildEndTime = 0;
        $item->memberFor = $alliance->memberCount;

        return $item;
    }

    public static function createFromData(array $data): AllianceTechnologyListItem
    {
        $item = new AllianceTechnologyListItem();
        $item->id = (int) $data['alliance_techlist_id'];
        $item->allianceId = (int) $data['alliance_techlist_alliance_id'];
        $item->technologyId = (int) $data['alliance_techlist_tech_id'];
        $item->level = (int) $data['alliance_techlist_current_level'];
        $item->buildStartTime = (int) $data['alliance_techlist_build_start_time'];
        $item->buildEndTime = (int) $data['alliance_techlist_build_end_time'];
        $item->memberFor = (int) $data['alliance_techlist_member_for'];

        return $item;
    }

    public function isUnderConstruction(): bool
    {
        return $this->buildEndTime > time();
    }
}
