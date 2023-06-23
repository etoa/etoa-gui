<?php declare(strict_types=1);

namespace EtoA\Missile;

class MissileRequirement
{
    public int $id;
    public int $missileId;
    public int $requiredBuildingId;
    public int $requiredTechnologyId;
    public int $requiredLevel;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->missileId = (int) $data['obj_id'];
        $this->requiredBuildingId = (int) $data['req_building_id'];
        $this->requiredTechnologyId = (int) $data['req_tech_id'];
        $this->requiredLevel = (int) $data['req_level'];
    }
}
