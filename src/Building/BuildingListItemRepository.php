<?php

declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\BuildingListItem;

class BuildingListItemRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, BuildingListItem::class);
    }

    /**
     * @return array<int, int>
     */
    public function getBuildingLevels(int $entityId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('buildlist_building_id, buildlist_current_level')
            ->from('buildlist')
            ->andWhere('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_current_level > 0')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->fetchAllKeyValue();

        return array_map(fn($value) => (int)$value, $data);
    }

    /**
     * @return BuildingWorkplace[]
     */
    public function getWorkplaceBuildings(int $entityId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('buildlist_people_working')
            ->addSelect('building_id, building_name, building_people_place')
            ->from('buildlist')
            ->innerJoin('buildlist', 'buildings', 'b', 'buildlist_building_id = b.building_id')
            ->andWhere('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_current_level > 0')
            ->andWhere('building_workplace = 1')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->fetchAllAssociative();

        return array_map(fn(array $row) => new BuildingWorkplace($row), $data);
    }

    /**
     * @return BuildingPeopleStorage[]
     */
    public function getPeopleStorageBuildings(int $entityId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('buildlist_current_level')
            ->addSelect('building_store_factor, building_name, building_people_place')
            ->from('buildlist')
            ->innerJoin('buildlist', 'buildings', 'b', 'buildlist_building_id = b.building_id')
            ->andWhere('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_current_level > 0')
            ->andWhere('building_people_place > 0')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->fetchAllAssociative();

        return array_map(fn(array $row) => new BuildingPeopleStorage($row), $data);
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
        return (int)$this->createQueryBuilder('q')
            ->select('COUNT(buildlist_id)')
            ->from('buildlist')
            ->fetchOne();
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
        return (int)$this->createQueryBuilder('q')
            ->select('COUNT(buildlist_id)')
            ->from('buildlist')
            ->where('buildlist_current_level=0')
            ->andWhere('buildlist_build_start_time=0')
            ->andWhere('buildlist_build_end_time=0')
            ->fetchOne();
    }

    public function deleteEmpty(): int
    {
        return $this->createQueryBuilder('q')
            ->delete('buildlist')
            ->where('buildlist_current_level=0')
            ->andWhere('buildlist_build_start_time=0')
            ->andWhere('buildlist_build_end_time=0')
            ->executeQuery()
            ->rowCount();
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

    public function updateUserForEntity(int $newUserId, int $entityId): void
    {
        $this->createQueryBuilder('q')
            ->update('buildlist')
            ->set('buildlist_user_id', ':newUserId')
            ->where('buildlist_entity_id = :entityId')
            ->setParameters([
                'newUserId' => $newUserId,
                'entityId' => $entityId,
            ])
            ->executeQuery();
    }

    public function removeForEntity(int $entityId): void
    {
        $this->createQueryBuilder('q')
            ->delete('building_queue')
            ->where('entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('buildlist')
            ->where('buildlist_entity_id = :entityId')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->executeQuery();
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

    public function addBuilding(BuildingListItem $buildingListItem): void
    {
        $this->entityManager->persist($buildingListItem);
        $this->entityManager->flush();
    }

    /**
     * @return BuildingListItem[]
     */
    public function search(BuildingListItemSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->select('*')
            ->from('buildlist')
            ->fetchAllAssociative();

        return array_map(fn($row) => BuildingListItem::createFromData($row), $data);
    }

    /**
     * @return BuildingListItem[]
     */
    public function findForUser(int $userId, int $entityId = null, int $endTimeAfter = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.userId = :userId')
            ->setParameter('userId', $userId);

        if ($entityId !== null) {
            $qb
                ->andWhere('q.entityId = :entityId')
                ->setParameter('entityId', $entityId);
        }

        if ($endTimeAfter !== null) {
            $qb
                ->andWhere('q.endTime > :time')
                ->setParameter('time', $endTimeAfter);
        }

        $data = $qb
            ->getQuery()
            ->execute();

        return array_map(fn($row) => BuildingListItem::createFromData($row), $data);
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

    public function getEntityBuilding(int $userId, int $entityId, int $buildingId): ?BuildingListItem
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('buildlist')
            ->where('buildlist_user_id = :userId')
            ->andWhere('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_building_id = :buildingId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'buildingId' => $buildingId,
            ])
            ->fetchAssociative();

        return $data !== false ? BuildingListItem::createFromData($data) : null;
    }

    /**
     * @return ?array{building_name: string, buildlist_id: string}
     */
    public function getDeactivatableBuilding(int $entityId): ?array
    {
        $data = $this->getConnection()->fetchAssociative('
            SELECT
                building_name, buildlist_id
            FROM
                buildlist
            INNER JOIN
                buildings
            ON building_id = buildlist_building_id
            AND buildlist_entity_id = :entityId
            AND buildlist_current_level > 0
            AND buildlist_building_id IN (:buildingIds)
            AND buildlist_deactivated < :now
            ORDER BY RAND()
            LIMIT 1
        ', [
            'entityId' => $entityId,
            'now' => time(),
            'buildingIds' => [BuildingId::DEFENSE, BuildingId::SHIPYARD, BuildingId::FLEET_CONTROL, BuildingId::MARKET, BuildingId::CRYPTO],
        ], [
            'buildingIds' => ArrayParameterType::INTEGER,
        ]);

        return $data !== false ? $data : null;
    }

    public function deactivateBuilding(int $id, int $deactivateTime): void
    {
        $this->createQueryBuilder('q')
            ->update('buildlist')
            ->set('buildlist_deactivated', ':deactivated')
            ->where('buildlist_id = :id')
            ->setParameters([
                'id' => $id,
                'deactivated' => $deactivateTime,
            ])
            ->executeQuery();
    }

    public function getPeopleWorking(int $entityId, bool $onlyWorkingStatus = false): PeopleWorking
    {
        $qb = $this->createQueryBuilder('q')
            ->select('buildlist_building_id, buildlist_people_working')
            ->from('buildlist')
            ->where('buildlist_entity_id = :entityId')
            ->setParameter('entityId', $entityId);

        if ($onlyWorkingStatus) {
            $qb->andWhere('buildlist_people_working_status = 1');
        }

        $data = $qb
            ->fetchAllKeyValue();

        return new PeopleWorking($data);
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
        $data = $this->getConnection()
            ->fetchAllAssociative(
                "SELECT
                    buildings.building_name as name,
                    SUM(buildlist.buildlist_current_level) as cnt
                FROM
                    buildings
                INNER JOIN
                    (
                        buildlist
                    INNER JOIN
                        users
                    ON
                        buildlist_user_id = user_id
                        AND user_ghost = 0
                        AND user_hmode_from = 0
                        AND user_hmode_to = 0
                    )
                ON
                    building_id = buildlist_building_id
                GROUP BY
                    buildings.building_id
                ORDER BY
                    cnt DESC;"
            );

        return array_map(fn($arr) => [
            'name' => $arr['name'],
            'cnt' => (int)$arr['cnt'],
        ], $data);
    }

    /**
     * @return array<int, array{name: string, max: int}>
     */
    public function getBestLevels(): array
    {
        $data = $this->getConnection()
            ->fetchAllAssociative(
                "SELECT
                    buildings.building_name as name,
                    MAX(buildlist.buildlist_current_level) as max
                FROM
                    buildings
                INNER JOIN
                    (
                        buildlist
                    INNER JOIN
                        users
                    ON
                        buildlist_user_id = user_id
                        AND user_ghost = 0
                        AND user_hmode_from = 0
                        AND user_hmode_to = 0
                    )
                ON
                    building_id = buildlist_building_id
                GROUP BY
                    buildings.building_id
                ORDER BY
                    max DESC;"
            );

        return array_map(fn($arr) => [
            'name' => $arr['name'],
            'max' => (int)$arr['max'],
        ], $data);
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->delete('buildlist')
            ->where('buildlist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
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
            ->update('buildlist')
            ->set('buildlist_build_type', 'buildlist_build_type - 2')
            ->where('buildlist_user_id = :userId')
            ->andWhere('buildlist_build_start_time > 0')
            ->setParameters([
                'userId' => $userId,
            ])
            ->executeQuery();
    }

    public function unfreezeConstruction(int $userId, int $duration): void
    {
        $this->createQueryBuilder('q')
            ->update('buildlist')
            ->set('buildlist_build_type', 'buildlist_build_type + 2')
            ->set('buildlist_build_start_time', 'buildlist_build_start_time + :duration')
            ->set('buildlist_build_end_time', 'buildlist_build_end_time + :duration')
            ->where('buildlist_user_id = :userId')
            ->andWhere('buildlist_build_start_time > 0')
            ->setParameters([
                'userId' => $userId,
                'duration' => $duration,
            ])
            ->executeQuery();
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
}
