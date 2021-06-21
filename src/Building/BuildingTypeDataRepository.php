<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\AbstractRepository;

class BuildingTypeDataRepository extends AbstractRepository
{
    /**
     * @return array<int, string>
     */
    public function getTypeNames(): array
    {
        return $this->createQueryBuilder()
            ->select('type_id, type_name')
            ->from('building_types')
            ->orderBy('type_order')
            ->addOrderBy('type_name')
            ->execute()
            ->fetchAllKeyValue();
    }
}
