<?php declare(strict_types=1);

namespace EtoA\Requirement;

class RequirementsCollection
{
    /** @var array<int, ObjectRequirement[]> */
    private $requirements = [];

    /**
     * @param ObjectRequirement[] $requirements
     */
    public function __construct(array $requirements)
    {
        foreach ($requirements as $requirement) {
            $this->requirements[$requirement->objectId][] = $requirement;
        }
    }

    /**
     * @return ObjectRequirement[]
     */
    public function getAll(int $objectId): array
    {
        if (!isset($this->requirements[$objectId])) {
            return [];
        }

        return $this->requirements[$objectId];
    }

    /**
     * @return ObjectRequirement[]
     */
    public function getBuildingRequirements(int $objectId): array
    {
        if (!isset($this->requirements[$objectId])) {
            return [];
        }

        return array_filter($this->requirements[$objectId], fn (ObjectRequirement $requirement) => $requirement->requiredBuildingId > 0);
    }

    /**
     * @return ObjectRequirement[]
     */
    public function getTechnologyRequirements(int $objectId): array
    {
        if (!isset($this->requirements[$objectId])) {
            return [];
        }

        return array_filter($this->requirements[$objectId], fn (ObjectRequirement $requirement) => $requirement->requiredTechnologyId > 0);
    }
}
