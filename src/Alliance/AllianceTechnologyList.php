<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceTechnologyList
{
    public int $id;
    public int $allianceId;
    public int $technologyId;
    public int $level;
    public int $buildStartTime;
    public int $buildEndTime;
    public int $memberFor;

    public function __construct(array $data)
    {
        $this->id = (int) $data['alliance_techlist_id'];
        $this->allianceId = (int) $data['alliance_techlist_alliance_id'];
        $this->technologyId = (int) $data['alliance_techlist_tech_id'];
        $this->level = (int) $data['alliance_techlist_current_level'];
        $this->buildStartTime = (int) $data['alliance_techlist_build_start_time'];
        $this->buildEndTime = (int) $data['alliance_techlist_build_end_time'];
        $this->memberFor = (int) $data['alliance_techlist_member_for'];
    }

    public function isUnderConstruction(): bool
    {
        return $this->buildEndTime > time();
    }
}
