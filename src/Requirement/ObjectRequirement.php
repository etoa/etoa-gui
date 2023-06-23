<?php declare(strict_types=1);

namespace EtoA\Requirement;

class ObjectRequirement
{
    public int $id = 0;
    public int $objectId = 0;
    public int $requiredBuildingId = 0;
    public int $requiredTechnologyId = 0;
    public int $requiredLevel = 0;

    public static function createFromData(array $data): ObjectRequirement
    {
        $requirement = new ObjectRequirement();
        $requirement->id = (int) $data['id'];
        $requirement->objectId = (int) $data['obj_id'];
        $requirement->requiredBuildingId = (int) $data['req_building_id'];
        $requirement->requiredTechnologyId = (int) $data['req_tech_id'];
        $requirement->requiredLevel = (int) $data['req_level'];

        return $requirement;
    }

    public function requiredId(): string
    {
        if ($this->requiredBuildingId > 0) {
            return 'b:' . $this->requiredBuildingId;
        }

        return 't:' . $this->requiredTechnologyId;
    }

    public function setRequiredId(string $id): void
    {
        [$type, $level] = explode(':', $id);
        if ($type === 'b') {
            $this->requiredBuildingId = (int) $level;
        }

        $this->requiredTechnologyId = (int) $level;
    }

    public function isEqual(ObjectRequirement $requirement): bool
    {
        return $this->objectId === $requirement->objectId &&
            $this->requiredTechnologyId === $requirement->requiredTechnologyId &&
            $this->requiredBuildingId === $requirement->requiredBuildingId &&
            $this->requiredLevel === $requirement->requiredLevel;
    }
}
