<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AlliancePoints
{
    public int $id;
    public int $allianceId;
    public int $timestamp;
    public int $points;
    public int $avg;
    public int $count;

    public function __construct(array $data)
    {
        $this->id = (int) $data['point_id'];
        $this->allianceId = (int) $data['point_alliance_id'];
        $this->timestamp = (int) $data['point_timestamp'];
        $this->points = (int) $data['point_points'];
        $this->avg = (int) $data['point_avg'];
        $this->count = (int) $data['point_cnt'];
    }
}
