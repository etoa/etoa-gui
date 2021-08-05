<?php declare(strict_types=1);

namespace EtoA\Requirement;

class ObjectRequirement
{
    public int $id;
    public int $objectId;
    public int $requiredBuildingId;
    public int $requiredTechnologyId;
    public int $requiredLevel;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->objectId = (int) $data['obj_id'];
        $this->requiredBuildingId = (int) $data['req_building_id'];
        $this->requiredTechnologyId = (int) $data['req_tech_id'];
        $this->requiredLevel = (int) $data['req_level'];
    }
}
