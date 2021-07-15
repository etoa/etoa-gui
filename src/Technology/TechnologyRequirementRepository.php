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
}
