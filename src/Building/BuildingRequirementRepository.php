<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\AbstractRepository;

class BuildingRequirementRepository extends AbstractRepository
{
    public function getDuplicateTechRequirements(): array
    {
        return $this->getDuplicateRequirements('req_building_id');
    }

    public function getDuplicateBuildingRequirements(): array
    {
        return $this->getDuplicateRequirements('req_tech_id');
    }

    private function getDuplicateRequirements(string $requirement): array
    {
        $data = $this->createQueryBuilder()
            ->select('obj_id', $requirement)
            ->from('building_requirements')
            ->where($requirement . ' > 0')
            ->groupBy('obj_id')
            ->addGroupBy($requirement)
            ->having('COUNT(*) > 1')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }
}
