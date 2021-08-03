<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\AbstractRepository;

class DefenseRequirementRepository extends AbstractRepository
{
    /**
     * @return array<int, int>
     */
    public function getDuplicateTechRequirements(): array
    {
        return $this->getDuplicateRequirements('req_building_id');
    }

    /**
     * @return array<int, int>
     */
    public function getDuplicateBuildingRequirements(): array
    {
        return $this->getDuplicateRequirements('req_tech_id');
    }

    /**
     * @return array<int, int>
     */
    private function getDuplicateRequirements(string $requirement): array
    {
        $data = $this->createQueryBuilder()
            ->select('obj_id', $requirement)
            ->from('def_requirements')
            ->where($requirement . ' > 0')
            ->groupBy('obj_id')
            ->addGroupBy($requirement)
            ->having('COUNT(*) > 1')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }
}
