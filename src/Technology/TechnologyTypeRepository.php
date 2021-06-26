<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;

class TechnologyTypeRepository extends AbstractRepository
{
    /**
     * @return TechnologyType[]
     */
    public function getTypes(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('tech_types')
            ->orderBy('type_order')
            ->addOrderBy('type_name')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new TechnologyType($row), $data);
    }
}
