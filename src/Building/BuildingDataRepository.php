<?php declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Building;

class BuildingDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Building::class);
    }

    /**
     * @return array<int, Building>
     */
    public function getBuildings(): array
    {
        return $this->searchBuildings();
    }

    /**
     * @return array<int, Building>
     */
    public function searchBuildings(BuildingSearch $search = null, BuildingSort $sort = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort)
            ->select('*')
            ->from('buildings')
            ->fetchAllAssociative();

        $buildings = [];
        foreach ($data as $row) {
            $building = new Building($row);
            $buildings[$building->getId()] = $building;
        }

        return $buildings;
    }

    public function getBuilding(int $buildingId): ?Building
    {
        $data = $this->createQueryBuilder('q')
            ->select('b.*')
            ->from('buildings', 'b')
            ->andWhere('b.building_id = :building_id')
            ->andWhere('b.building_show=1')
            ->setParameter('building_id', $buildingId)
            ->fetchAssociative();

        return $data !== false ? new Building($data) : null;
    }

    /**
     * @return Building[]
     */
    public function getBuildingsByType(int $type): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('b.*')
            ->from('buildings', 'b')
            ->andWhere('b.building_type_id = :type')
            ->andWhere('b.building_show=1')
            ->setParameter('type', $type)
            ->orderBy('b.building_order')
            ->addOrderBy('b.building_name')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Building($row), $data);
    }

    /**
     * @return array<int, string>
     */
    public function getBuildingNames(bool $showAll = false, BuildingSort $orderBy = null): array
    {
        $orderBy = $orderBy ?? BuildingSort::name();
        $qb = $this->applySearchSortLimit($this->createQueryBuilder('q'), null, $orderBy)
            ->select('building_id', 'building_name')
            ->from('buildings');

        if (!$showAll) {
            $qb->where('building_show = 1');
        }

        return $qb
            ->fetchAllKeyValue();
    }

    public function getBuildingName(int $buildingId): string
    {
        return (string) $this->applySearchSortLimit($this->createQueryBuilder('q'))
            ->select('building_name')
            ->from('buildings')
            ->where('building_id = :buildingId')
            ->setParameter('buildingId', $buildingId)
            ->fetchOne();
    }

    /**
     * @return array<int, string>
     */
    public function getBuildingNamesHavingPlaceForPeople(): array
    {
        return $this->createQueryBuilder('q')
            ->select('building_id, building_name')
            ->from('buildings')
            ->where('building_people_place > 0')
            ->orderBy('building_order')
            ->addOrderBy('building_name')
            ->fetchAllKeyValue();
    }
}
