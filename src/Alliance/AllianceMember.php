<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceMember
{
    public int $id;
    public string $nick;
    public int $points;
    public int $mainPlanetId;
    public int $rankId;
    public ?int $timeAction;
    public ?int $lastLog;
    public string $raceName;

    public function __construct(array $data)
    {
        $this->id = (int) $data['user_id'];
        $this->nick = $data['user_nick'];
        $this->points = (int) $data['user_points'];
        $this->mainPlanetId = (int) $data['planetId'];
        $this->rankId = (int) $data['user_alliance_rank_id'];
        $this->timeAction = $data['time_action'] !== null ? (int) $data['time_action'] : null;
        $this->lastLog = $data['last_log'] !== null ? (int) $data['last_log'] : null;
        $this->raceName = $data['race_name'];
    }
}
