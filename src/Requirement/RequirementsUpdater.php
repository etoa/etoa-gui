<?php declare(strict_types=1);

namespace EtoA\Requirement;

class RequirementsUpdater
{
    public function __construct(
        private AbstractRequirementRepository $repository,
    ) {
    }

    /**
     * @param ObjectRequirement[][] $existingRequirements
     * @param ObjectRequirement[][] $newRequirements
     */
    public function update(array $existingRequirements, array $newRequirements): void
    {
        $existingMap = [];
        foreach ($existingRequirements as $objectId => $objectRequirements) {
            foreach ($objectRequirements as $requirement) {
                $existingMap[$objectId][$requirement->requiredId()][$requirement->requiredLevel] = $requirement->id;
            }
        }

        $toBeAdded = [];
        foreach ($newRequirements as $objectId => $requirements) {
            foreach ($requirements as $requirement) {
                if (isset($existingMap[$objectId][$requirement->requiredId()][$requirement->requiredLevel])) {
                    unset($existingMap[$objectId][$requirement->requiredId()][$requirement->requiredLevel]);
                } else {
                    $toBeAdded[] = $requirement;
                }
            }
        }

        foreach ($existingMap as $requiredIdRequirements) {
            foreach ($requiredIdRequirements as $levelRequirements) {
                foreach ($levelRequirements as $id) {
                    $this->repository->remove($id);
                }
            }
        }

        foreach ($toBeAdded as $requirement) {
            $this->repository->add($requirement->objectId, $requirement->requiredLevel, $requirement->requiredTechnologyId, $requirement->requiredBuildingId);
        }
    }
}
