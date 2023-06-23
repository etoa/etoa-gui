<?php declare(strict_types=1);

namespace EtoA\User;

class UserStatistic
{
    public int $userId;
    public string $nick;
    public bool $blocked;
    public bool $hmod;
    public bool $inactive;
    public int $allianceId;
    public ?string $allianceTag;
    public ?string $raceName;
    public int $sx;
    public int $sy;

    public int $points;
    public int $shipPoints;
    public int $techPoints;
    public int $buildingPoints;
    public int $expPoints;
    public int $rank;
    public int $rankShips;
    public int $rankTech;
    public int $rankBuildings;
    public int $rankExp;
    public int $rankShift;
    public int $rankShiftShips;
    public int $rankShiftTech;
    public int $rankShiftBuilding;
    public int $rankShiftExp;

    public static function createFromCalculation(User $user, bool $blocked, bool $hmod, bool $inactive, int $allianceId, ?string $allianceTag, ?string $raceName, int $sx, int $sy, int $points, int $shipPoints, int $techPoints, int $buildingPoints, int $expPoints): UserStatistic
    {
        $stats = new UserStatistic();
        $stats->userId = $user->id;
        $stats->nick = $user->nick;
        $stats->blocked = $blocked;
        $stats->hmod = $hmod;
        $stats->inactive = $inactive;
        $stats->allianceId = $allianceId;
        $stats->allianceTag = $allianceTag;
        $stats->raceName = $raceName;
        $stats->sx = $sx;
        $stats->sy = $sy;
        $stats->points = $points;
        $stats->shipPoints = $shipPoints;
        $stats->techPoints = $techPoints;
        $stats->buildingPoints = $buildingPoints;
        $stats->expPoints = $expPoints;

        return $stats;
    }
}
