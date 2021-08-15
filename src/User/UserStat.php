<?php declare(strict_types=1);

namespace EtoA\User;

class UserStat
{
    public int $id;
    public string $nick;
    public bool $blocked;
    public bool $hmod;
    public bool $inactive;

    /** Rank for the current selection */
    public int $rank;

    /** Points for the current selection */
    public int $points;

    /** Shift for the current selection */
    public int $shift;

    public string $raceName;
    public ?string $allianceTag;
    public int $sx;
    public int $sy;
    public int $shipPoints;
    public int $techPoints;
    public int $buildingPoints;
    public int $expPoints;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->nick = $data['nick'];
        $this->blocked = (bool) $data['blocked'];
        $this->hmod = (bool) $data['hmod'];
        $this->inactive = (bool) $data['inactive'];
        $this->rank = (int) $data['rank'];
        $this->points = (int) $data['points'];
        $this->shift = (int) $data['shift'];
        $this->raceName = $data['race_name'];
        $this->allianceTag = $data['alliance_tag'];
        $this->sx = (int) $data['sx'];
        $this->sy = (int) $data['sy'];
        $this->shipPoints = (int) $data['points_ships'];
        $this->techPoints = (int) $data['points_tech'];
        $this->buildingPoints = (int) $data['points_buildings'];
        $this->expPoints = (int) $data['points_exp'];
    }
}
