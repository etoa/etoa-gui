<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;

class TechnologyRequirementRepository extends AbstractRepository
{
    /**
     * @return TechnologyRequirement[]
     */
    public function getAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('tech_requirements')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new TechnologyRequirement($row), $data);
    }

    /**
     * @return TechnologyRequirement[]
     */
    public function getRequirements(int $technologyId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('tech_requirements')
            ->where('obj_id = :technologyId')
            ->setParameter('technologyId', $technologyId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new TechnologyRequirement($row), $data);
    }

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
            ->from('tech_requirements')
            ->where($requirement . ' > 0')
            ->groupBy('obj_id')
            ->addGroupBy($requirement)
            ->having('COUNT(*) > 1')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }
}
