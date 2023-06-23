<?php declare(strict_types=1);

namespace EtoA\Technology;

class TechnologyPoint
{
    public int $technologyId;
    public int $level;
    public float $points;

    public function __construct(array $data)
    {
        $this->technologyId = (int) $data['bp_tech_id'];
        $this->level = (int) $data['bp_level'];
        $this->points = (float) $data['bp_points'];
    }
}
