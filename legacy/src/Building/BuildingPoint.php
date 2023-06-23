<?php declare(strict_types=1);

namespace EtoA\Building;

class BuildingPoint
{
    public int $buildingId;
    public int $level;
    public float $points;

    public function __construct(array $data)
    {
        $this->buildingId = (int) $data['bp_building_id'];
        $this->level = (int) $data['bp_level'];
        $this->points = (float) $data['bp_points'];
    }
}
