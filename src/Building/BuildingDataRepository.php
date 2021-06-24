<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\AbstractRepository;

class BuildingDataRepository extends AbstractRepository
{
    public function getBuilding(int $buildingId): ?Building
    {
        $data = $this->createQueryBuilder()
            ->select('b.*')
            ->from('buildings', 'b')
            ->andWhere('b.building_id = :building_id')
            ->andWhere('b.building_show=1')
            ->setParameter('building_id', $buildingId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Building($data) : null;
    }

    /**
     * @return Building[]
     */
    public function getBuildingsByType(int $type): array
    {
        $data = $this->createQueryBuilder()
            ->select('b.*')
            ->from('buildings', 'b')
            ->andWhere('b.building_type_id = :type')
            ->andWhere('b.building_show=1')
            ->setParameter('type', $type)
            ->orderBy('b.building_order')
            ->addOrderBy('b.building_name')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Building($row), $data);
    }

    /**
     * @return array<int, string>
     */
    public function getBuildingNames(): array
    {
        return $this->createQueryBuilder()
            ->select('building_id, building_name')
            ->addSelect()
            ->from('buildings')
            ->where('building_show = 1')
            ->orderBy('building_name')
            ->execute()
            ->fetchAllKeyValue();
    }
}