<?php declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Building;
use EtoA\Entity\Planet;

class BuildingDataRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly BuildingListItemRepository $buildingListItemRepository
    )
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
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort)
            ->getQuery()
            ->execute();
    }

    public function getBuilding(int $buildingId): ?Building
    {
        return $this->findOneBy(['id'=>$buildingId,'show'=>true]);
    }

    /**
     * @return Building[]
     */
    public function getBuildingsByType(int $type): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.type = :type')
            ->andWhere('q.show=1')
            ->setParameter('type', $type)
            ->orderBy('q.order')
            ->addOrderBy('q.name')
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, Building>
     */
    public function getBuildingNames(bool $showAll = false, BuildingSort $orderBy = null): array
    {
        $orderBy = $orderBy ?? BuildingSort::name();
        $qb = $this->applySearchSortLimit($this->createQueryBuilder('q'), null, $orderBy);

        if (!$showAll) {
            $qb->where('q.show = 1');
        }

        return $qb
            ->getQuery()
            ->execute();
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
            ->select('q.id, q.name')
            ->where('q.peoplePlace > 0')
            ->orderBy('q.order')
            ->addOrderBy('q.name')
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<string, Building>
     */
    public function getBuildingsWithBuildList(int|Planet $entityId): array
    {
        $buildings = $this->findBy(['show'=>true]);

        foreach ($buildings as $building) {
            $bl = $this->buildingListItemRepository->findOneBy(['building'=>$building, 'entity'=>$entityId]);
            $building->bl = $bl;
        }

        return $buildings;
    }
}
