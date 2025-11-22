<?php

declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Building;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Entity;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class BuildingListItemRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildingListItem::class);
    }

    /**
     * @return array<int, int>
     */
    public function getBuildingLevels(int|Planet $entityId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('IDENTITY(q.building), q.currentLevel')
            ->andWhere('q.entity = :entityId')
            ->andWhere('q.currentLevel > 0')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->getQuery()
            ->execute();

        return array_column($data, 'currentLevel', 'id');
    }

    /**
     * @return BuildingListItem[]
     */
    public function getWorkplaceBuildings(Planet $planet): array
    {
        return $this->createQueryBuilder('q')
            ->select('q')
            ->innerJoin('App:Building', 'b', 'WITH', 'q.building = b.id')
            ->andWhere('q.entity = :planet')
            ->andWhere('q.currentLevel > 0')
            ->andWhere('b.workplace = 1')
            ->setParameters([
                'planet' => $planet,
            ])
            ->getQuery()
            ->execute();
    }

    /**
     * @return BuildingListItem[]
     */
    public function getPeopleStorageBuildings(Planet $planet): array
    {
        return $this->createQueryBuilder('q')
            ->select('q')
            ->innerJoin('App:Building', 'b', 'WITH', 'q.building = b.id')
            ->where('q.entity = :planet')
            ->andWhere('q.currentLevel > 0')
            ->andWhere('b.peoplePlace > 0')
            ->setParameters([
                'planet' => $planet,
            ])
            ->getQuery()
            ->execute();
    }

    public function getBuildingLevel(int $userId, int $buildingId, int $entityId): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select('buildlist_current_level')
            ->from('buildlist')
            ->where('buildlist_building_id = :buildingId')
            ->andWhere('buildlist_user_id = :userId')
            ->andWhere('buildlist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'buildingId' => $buildingId,
                'entityId' => $entityId,
            ])
            ->fetchOne();
    }

    public function getHighestBuildingLevel(int $userId, int $buildingId): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select('MAX(buildlist_current_level)')
            ->from('buildlist')
            ->where('buildlist_building_id = :buildingId')
            ->andWhere('buildlist_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'buildingId' => $buildingId,
            ])
            ->fetchOne();
    }

    public function getNumberOfBuildings(int $buildingId): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select('COUNT(buildlist_id)')
            ->from('buildlist')
            ->where('buildlist_building_id = :buildingId')
            ->setParameter('buildingId', $buildingId)
            ->fetchOne();
    }

    public function countSearch(BuildingListItemSearch $search = null): int
    {
        return (int)$this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(buildlist_id)')
            ->from('buildlist')
            ->fetchOne();
    }

    public function numBuildingListEntries(): int
    {
        return $this->count([]);
    }

    public function countBuildInProgress(int $userId, int $entityId): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select('COUNT(buildlist_id)')
            ->from('buildlist')
            ->where('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_user_id = :userId')
            ->andWhere('buildlist_build_start_time > 0')
            ->andWhere('buildlist_build_end_time > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->fetchOne();
    }

    public function countEmpty(): int
    {
        return $this->count(['currentLevel'=>0,'startTime'=>0,'endTime'=>0]);
    }

    public function deleteEmpty(): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.currentLevel=0')
            ->andWhere('q.startTime=0')
            ->andWhere('q.endTime=0')
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, string>
     */
    public function buildingNames(): array
    {
        return $this->createQueryBuilder('q')
            ->select('building_id', 'building_name')
            ->from('buildings')
            ->orderBy('building_type_id')
            ->addOrderBy('building_order')
            ->addOrderBy('building_name')
            ->fetchAllKeyValue();
    }

    public function fetchBuildingListEntry(int $id): ?array
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'bl.buildlist_id',
                'bl.buildlist_current_level',
                'bl.buildlist_build_start_time',
                'bl.buildlist_build_end_time',
                'bl.buildlist_build_type',
                'p.planet_name',
                'u.user_nick',
                'b.building_name'
            )
            ->from('buildlist', 'bl')
            ->innerJoin('bl', 'planets', 'p', 'bl.buildlist_entity_id = p.id')
            ->innerJoin('bl', 'users', 'u', 'bl.buildlist_user_id = u.user_id')
            ->innerJoin('bl', 'buildings', 'b', 'bl.buildlist_building_id = b.building_id AND bl.buildlist_id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? $data : null;
    }

    public function updateBuildingListEntry(int $id, int $level, int $type, int $start, int $end): bool
    {
        $affected = $this->createQueryBuilder('q')
            ->update('buildlist')
            ->set('buildlist_current_level', ':level')
            ->set('buildlist_build_type', ':type')
            ->set('buildlist_build_start_time', ':start')
            ->set('buildlist_build_end_time', ':end')
            ->where('buildlist_id = :id')
            ->setParameters([
                'level' => $level,
                'type' => $type,
                'start' => $start,
                'end' => $end,
                'id' => $id,
            ])
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function updateUserForEntity(User $newUser, Planet $entity): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.user', ':newUser')
            ->where('q.entity = :entity')
            ->setParameters([
                'newUser' => $newUser,
                'entity' => $entity,
            ])
            ->getQuery()
            ->execute();
    }

    public function removeForEntity(Planet $entity): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.entity = :entity')
            ->setParameters([
                'entity' => $entity,
            ])
            ->getQuery()
            ->execute();
    }

    public function deleteBuildingListEntry(int $id): bool
    {
        $affected = $this->createQueryBuilder('q')
            ->delete('buildlist')
            ->where('buildlist_id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    /**
     * @param array<string, mixed> $formData
     */
    public function findByFormData(array $formData): array
    {
        $qry = $this->createQueryBuilder('q')
            ->select('*')
            ->from('buildlist', 'l')
            ->innerJoin('l', 'planets', 'p', 'p.id = l.buildlist_entity_id')
            ->innerJoin('l', 'users', 'u', 'u.user_id = l.buildlist_user_id')
            ->innerJoin('l', 'buildings', 'b', 'b.building_id = l.buildlist_building_id')
            ->groupBy('buildlist_id')
            ->orderBy('buildlist_user_id')
            ->addOrderBy('buildlist_entity_id')
            ->addOrderBy('building_type_id')
            ->addOrderBy('building_order')
            ->addOrderBy('building_name');

        if ($formData['entity_id'] != "") {
            $qry->andWhere('id = :id')
                ->setParameter('id', $formData['entity_id']);
        }
        if ($formData['planet_name'] != "") {
            $qry = $this->fieldComparisonQuery($qry, $formData, 'planet_name', 'planet_name');
        }
        if ($formData['user_id'] != "") {
            $qry->andWhere('user_id = :userid')
                ->setParameter('userid', $formData['user_id']);
        }
        if ($formData['user_nick'] != "") {
            $qry = $this->fieldComparisonQuery($qry, $formData, 'user_nick', 'user_nick');
        }
        if ($formData['building_id'] != "") {
            $qry->andWhere('building_id = :building')
                ->setParameter('building', $formData['building_id']);
        }

        return $qry
            ->fetchAllAssociative();
    }

    private function fieldComparisonQuery(QueryBuilder $qry, array $formData, string $column, string $formKey): QueryBuilder
    {
        $value = $formData[$formKey];
        switch ($formData['comparisonMode'][$formKey]) {
            case 'like_wildcard':
                $comparator = 'LIKE';
                $value = "%$value%";
                break;
            case 'like':
                $comparator = 'LIKE';
                break;
            case 'not_like_wildcard':
                $comparator = 'NOT LIKE';
                $value = "%$value%";
                break;
            case 'not_like':
                $comparator = 'NOT LIKE';
                break;
            case 'lt':
                $comparator = '<';
                break;
            case 'gt':
                $comparator = '>';
                break;
            default:
                $comparator = '=';
        }
        $qry->andWhere("$column $comparator :$column")
            ->setParameter($column, $value);
        return $qry;
    }

    public function addBuilding(Building $building, int $level, User $user, Planet $entity, int $buildType = 0, int $startTime = 0, int $endTime = 0): void
    {
        $item = $this->findOneBy(['user'=>$user,'building'=>$building,'entity'=>$entity]);

        if(!$item) {
            $item = new BuildingListItem();
            $item->setBuilding($building);
            $item->setUser($user);
            $item->setEntity($entity);
        }

        $item->setCurrentLevel(max(0, $level));
        $item->setBuildType($buildType);
        $item->setStartTime($startTime);
        $item->setEndTime($endTime);

        $this->persist($item);
        $this->save();
    }

    /**
     * @return BuildingListItem[]
     */
    public function search(BuildingListItemSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->getQuery()
            ->execute();
    }

    /**
     * @return BuildingListItem[]
     */
    public function findForUser(User $user, Planet $entity = null, int $endTimeAfter = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->setParameter('user', $user);

        if ($entity !== null) {
            $qb
                ->andWhere('q.entity = :entity')
                ->setParameter('entity', $entity);
        }

        if ($endTimeAfter !== null) {
            $qb
                ->andWhere('q.endTime > :time')
                ->setParameter('time', $endTimeAfter);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function getEntry(int $id): ?BuildingListItem
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('buildlist')
            ->where('buildlist_id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchAssociative();

        return $data !== false ? BuildingListItem::createFromData($data) : null;
    }

    public function getEntityBuilding(User|int $user, Planet|int $entity, Building|int $building): ?BuildingListItem
    {
        return $this->findOneBy(['user'=>$user,'entity'=>$entity,'building'=>$building]);
    }

    /**
     * @return ?array{building_name: string, buildlist_id: string}
     */
    public function getDeactivatableBuilding(int|Planet $entityId): ?BuildingListItem
    {
        return $this->createQueryBuilder('q')
            ->where('q.entity = :entityId')
            ->andWhere('q.currentLevel>0')
            ->andWhere('q.building IN (:buildingIds)')
            ->andWhere('q.deactivated>:now')
            ->setParameters([
                'entityId' => $entityId,
                'now' => time(),
                'buildingIds' => [BuildingId::DEFENSE, BuildingId::SHIPYARD, BuildingId::FLEET_CONTROL, BuildingId::MARKET, BuildingId::CRYPTO],
            ])
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deactivateBuilding(BuildingListItem $buildingListItem, int $deactivateTime): void
    {
        $buildingListItem->setDeactivated($deactivateTime);
        $this->save();
    }

    public function getPeopleWorking(Planet $entity, bool $onlyWorkingStatus = false): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.entity = :entity')
            ->setParameter('entity', $entity);

        if ($onlyWorkingStatus) {
            $qb->andWhere('q.peopleWorkingStatus = 1');
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function getTotalPeopleWorking(Planet $entity, bool $onlyWorkingStatus = false): int
    {
        $qb = $this->createQueryBuilder('q')
            ->select('SUM(q.peopleWorking)')
            ->where('q.entity = :entity')
            ->setParameter('entity', $entity);

        if ($onlyWorkingStatus) {
            $qb->andWhere('q.peopleWorkingStatus = 1');
        }

        return (int)$qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function updateProductionPercent(int $userId, int $entityId, int $buildingId, float $percent): void
    {
        $this->createQueryBuilder('q')
            ->update('buildlist')
            ->set('buildlist_prod_percent', ':percent')
            ->where('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_building_id = :buildingId')
            ->andWhere('buildlist_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'buildingId' => $buildingId,
                'percent' => $percent,
            ])
            ->executeQuery();
    }

    public function setPeopleWorking(int $entityId, int $buildingId, int $people): void
    {
        $this->createQueryBuilder('q')
            ->update('buildlist')
            ->set('buildlist_people_working', ':peopleWorking')
            ->where('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_building_id = :buildingId')
            ->setParameters([
                'entityId' => $entityId,
                'buildingId' => $buildingId,
                'peopleWorking' => $people,
            ])
            ->executeQuery();
    }

    public function markBuildingWorkingStatus(int $userId, int $entityId, int $buildingId, bool $working): bool
    {
        return (bool)$this->createQueryBuilder('q')
            ->update('buildlist')
            ->set('buildlist_people_working_status', ':status')
            ->where('buildlist_building_id = :buildingId')
            ->andWhere('buildlist_user_id = :userId')
            ->andWhere('buildlist_entity_id = :entityId')
            ->setParameters([
                'buildingId' => $buildingId,
                'entityId' => $entityId,
                'userId' => $userId,
                'status' => (int)$working,
            ])
            ->executeQuery()
            ->rowCount();
    }

    /**
     * @return array<int, array{name: string, cnt: int}>
     */
    public function getOverallCount(): array
    {
        $data= $this->createQueryBuilder('q')
            ->select( 'SUM(q.currentLevel) as cnt')
            ->addSelect('b.name as name')
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->innerJoin('App:Building', 'b', 'WITH', 'b.id = q.building')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->groupBy('b.id')
            ->orderBy('cnt', 'DESC')
            ->getQuery()
            ->execute();

        return array_map(fn ($arr) => [
            'name' => $arr['name'],
            'cnt' => (int) $arr['cnt'],
        ], $data);
    }

    /**
     * @return array<int, array{name: string, max: int}>
     */
    public function getBestLevels(): array
    {
        $data= $this->createQueryBuilder('q')
            ->select( 'MAX(q.currentLevel) as max')
            ->addSelect('b.name as name')
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->innerJoin('App:Building', 'b', 'WITH', 'b.id = q.building')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->groupBy('b.id')
            ->orderBy('max', 'DESC')
            ->getQuery()
            ->execute();

        return array_map(fn ($arr) => [
            'name' => $arr['name'],
            'max' => (int) $arr['max'],
        ], $data);
    }

    public function removeForUser(User $user): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('buildlist')
            ->where('buildlist_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    public function freezeConstruction(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->set('q.buildType', 'q.buildType - 2')
            ->where('q.userId = :userId')
            ->andWhere('q.startTime > 0')
            ->setParameters([
                'userId' => $userId
            ])
            ->getQuery()
            ->execute();
    }

    public function unfreezeConstruction(int|User $userId, int $duration): void
    {
        $this->createQueryBuilder('q')
            ->set('q.buildType', 'q.build_type + 2')
            ->set('q.startTime', 'q.startTime +'. $duration)
            ->set('q.endTime', 'q.endTime +'. $duration)
            ->where('q.user = :userId')
            ->andWhere('q.startTime > 0')
            ->setParameters([
                'userId' => $userId,
            ])
            ->getQuery()
            ->execute();
    }

    public function findUnderConstruction(int $userId, int $entityId) : ?array {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('buildlist')
            ->where('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_build_type = 3')
            ->orWhere('buildlist_build_type = 4')
            ->andWhere('buildlist_build_end_time > :time')
            ->setParameters([
                'entityId' => $entityId,
                'userId' => $userId,
                'time' => time()
            ])
            ->fetchAllAssociative();

        return array_map(fn($arr) => [
            BuildingListItem::createFromData($arr)
        ], $data);
    }

    /**
     * @return array<string, mixed>[]
     */
    public function getLegacyBuildList(int $entityId): array
    {
        return $this->getConnection()
            ->fetchAllAssociative("
                SELECT
                    l.*,
                    i.*
                FROM
                    buildings i
                LEFT JOIN
                    buildlist l
                ON
                    l.buildlist_building_id = i.building_id
                    AND l.buildlist_entity_id= :entityId
                WHERE i.building_show='1'
                ORDER BY
                    i.building_order,
                    i.building_name;
            ", [
                'entityId' => $entityId,
            ]);
    }

    public function findWithProductionOrPowerUse(Entity $entity): array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('App:Building', 'b', 'WITH', 'q.building = b.id')
            ->where('q.entity = :entity')
            ->andWhere('b.prodMetal > 0 OR b.prodCrystal > 0 OR b.prodPlastic > 0 OR b.prodFuel > 0 OR b.prodFood > 0 OR b.powerUse > 0')
            ->setParameters([
                'entity' => $entity
            ])
            ->getQuery()
            ->execute();
    }

    public function countBySearch(BuildingListItemSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
