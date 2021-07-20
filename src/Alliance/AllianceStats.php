<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceStats
{
    public int $allianceId;
    public string $allianceTag;
    public string $allianceName;
    public int $count;
    public int $points;
    public int $alliancePoints;
    public int $userPoints;
    public int $buildingPoints;
    public int $technologyPoints;
    public int $shipPoints;
    public int $userAverage;
    public int $currentRank;
    public int $lastRank;

    public static function createFromDbRow(array $data): AllianceStats
    {
        $stats = new AllianceStats();
        $stats->allianceId = (int) $data['alliance_id'];
        $stats->allianceTag = $data['alliance_tag'];
        $stats->allianceName = $data['alliance_name'];
        $stats->count = (int) $data['cnt'];
        $stats->points = (int) $data['points'];
        $stats->userPoints = (int) $data['upoints'];
        $stats->alliancePoints = (int) $data['apoints'];
        $stats->buildingPoints = (int) $data['bpoints'];
        $stats->technologyPoints = (int) $data['tpoints'];
        $stats->shipPoints = (int) $data['spoints'];
        $stats->userAverage = (int) $data['uavg'];
        $stats->currentRank = (int) $data['alliance_rank_current'];
        $stats->lastRank = (int) $data['alliance_rank_last'];

        return $stats;
    }

    public static function createFromData(
        int $allianceId,
        string $allianceTag,
        string $allianceName,
        int $count,
        int $points,
        int $alliancePoints,
        int $userPoints,
        int $buildingPoints,
        int $technologyPoints,
        int $shipPoints,
        int $userAverage,
        int $lastRank
    ): AllianceStats {
        $stats = new AllianceStats();
        $stats->allianceId = $allianceId;
        $stats->allianceTag = $allianceTag;
        $stats->allianceName = $allianceName;
        $stats->count = $count;
        $stats->points = $points;
        $stats->userPoints = $userPoints;
        $stats->alliancePoints = $alliancePoints;
        $stats->buildingPoints = $buildingPoints;
        $stats->technologyPoints = $technologyPoints;
        $stats->shipPoints = $shipPoints;
        $stats->userAverage = $userAverage;
        $stats->lastRank = $lastRank;

        return $stats;
    }
}
