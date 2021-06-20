<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\AbstractRepository;

class BuildingDataRepository extends AbstractRepository
{
    /**
     * @return Building[]
     */
    public function getBuildingsByType(int $type): array
    {
        $data = $this->createQueryBuilder()
            ->select('b.*')
            ->from('buildings', 'b')
            ->andWhere('b.building_type_id = :type')
            ->setParameter('type', $type)
            ->orderBy('b.building_order')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Building($row), $data);
    }
}
