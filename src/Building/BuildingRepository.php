<?php

declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class BuildingRepository extends AbstractRepository
{
    /**
     * @return array<int, int>
     */
    public function getBuildingLevels(int $entityId): array
    {
        $data = $this->createQueryBuilder()
            ->select('buildlist_building_id, buildlist_current_level')
            ->from('buildlist')
            ->andWhere('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_current_level > 0')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function getBuildingLevel(int $userId, int $buildingId, int $entityId): int
    {
        return (int) $this->createQueryBuilder()
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
            ->execute()
            ->fetchOne();
    }

    public function getHighestBuildingLevel(int $userId, int $buildingId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('MAX(buildlist_current_level)')
            ->from('buildlist')
            ->where('buildlist_building_id = :buildingId')
            ->andWhere('buildlist_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'buildingId' => $buildingId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function getNumberOfBuildings(int $buildingId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(buildlist_id)')
            ->from('buildlist')
            ->where('buildlist_building_id = :buildingId')
            ->setParameter('buildingId', $buildingId)
            ->execute()
            ->fetchOne();
    }

    public function numBuildingListEntries(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(buildlist_id)')
            ->from('buildlist')
            ->execute()
            ->fetchOne();
    }

    public function countEmpty(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(buildlist_id)')
            ->from('buildlist')
            ->where('buildlist_current_level=0')
            ->andWhere('buildlist_build_start_time=0')
            ->andWhere('buildlist_build_end_time=0')
            ->execute()
            ->fetchOne();
    }

    public function deleteEmpty(): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('buildlist')
            ->where('buildlist_current_level=0')
            ->andWhere('buildlist_build_start_time=0')
            ->andWhere('buildlist_build_end_time=0')
            ->execute();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(buildlist_id)')
            ->from('buildlist')
            ->where($qb->expr()->notIn('buildlist_user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchOne();
    }


    /**
     * @param int[] $availableUserIds
     */
    public function deleteOrphaned(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('buildlist')
            ->where($qb->expr()->notIn('buildlist_user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    /**
     * @return array<int, string>
     */
    public function buildingNames(): array
    {
        return $this->createQueryBuilder()
            ->select('building_id', 'building_name')
            ->from('buildings')
            ->orderBy('building_type_id')
            ->addOrderBy('building_order')
            ->addOrderBy('building_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    public function fetchBuildingListEntry(int $id): ?array
    {
        $data = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAssociative();

        return $data !== false ? $data : null;
    }

    public function updateBuildingListEntry(int $id, int $level, int $type, int $start, int $end): bool
    {
        $affected = $this->createQueryBuilder()
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
            ->execute();

        return (int) $affected > 0;
    }

    public function updateUserForEntity(int $newUserId, int $entityId): void
    {
        $this->createQueryBuilder()
            ->update('buildlist')
            ->set('buildlist_user_id', ':newUserId')
            ->where('buildlist_entity_id = :entityId')
            ->setParameters([
                'newUserId' => $newUserId,
                'entityId' => $entityId,
            ])
            ->execute();
    }

    public function removeForEntity(int $entityId): void
    {
        $this->createQueryBuilder()
            ->delete('building_queue')
            ->where('entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('buildlist')
            ->where('buildlist_entity_id = :entityId')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->execute();
    }

    public function deleteBuildingListEntry(int $id): bool
    {
        $affected = $this->createQueryBuilder()
            ->delete('buildlist')
            ->where('buildlist_id = :id')
            ->setParameter('id', $id)
            ->execute();

        return (int) $affected > 0;
    }

    /**
     * @param array<string, mixed> $formData
     */
    public function findByFormData(array $formData): array
    {
        $qry = $this->createQueryBuilder()
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
            $qry = fieldComparisonQuery($qry, $formData, 'planet_name', 'planet_name');
        }
        if ($formData['user_id'] != "") {
            $qry->andWhere('user_id = :userid')
                ->setParameter('userid', $formData['user_id']);
        }
        if ($formData['user_nick'] != "") {
            $qry = fieldComparisonQuery($qry, $formData, 'user_nick', 'user_nick');
        }
        if ($formData['building_id'] != "") {
            $qry->andWhere('building_id = :building')
                ->setParameter('building', $formData['building_id']);
        }

        return $qry->execute()
            ->fetchAllAssociative();
    }

    public function addBuilding(int $buildingId, int $level, int $userId, int $entityId): void
    {
        $this->getConnection()->executeQuery('INSERT INTO buildlist (
                buildlist_user_id,
                buildlist_entity_id,
                buildlist_building_id,
                buildlist_current_level
            ) VALUES (
                :userId,
                :entityId,
                :buildingId,
                :level
            ) ON DUPLICATE KEY
            UPDATE buildlist_current_level = :level;
        ', [
            'userId' => $userId,
            'level' => max(0, $level),
            'entityId' => $entityId,
            'buildingId' => $buildingId,
        ]);
    }

    /**
     * @return BuildingListItem[]
     */
    public function findForUser(int $userId, int $entityId = null, int $endTimeAfter = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('buildlist')
            ->where('buildlist_user_id = :userId')
            ->setParameter('userId', $userId);

        if ($entityId !== null) {
            $qb
                ->andWhere('buildlist_entity_id = :entityId')
                ->setParameter('entityId', $entityId);
        }

        if ($endTimeAfter !== null) {
            $qb
                ->andWhere('buildlist_build_end_time > :time')
                ->setParameter('time', $endTimeAfter);
        }

        $data = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new BuildingListItem($row), $data);
    }

    public function getEntityBuilding(int $userId, int $entityId, int $buildingId): ?BuildingListItem
    {
        $data = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new BuildingListItem($data) : null;
    }

    public function save(BuildingListItem $item): void
    {
        $this->createQueryBuilder()
            ->update('buildlist')
            ->set('buildlist_user_id', 'userId')
            ->set('buildlist_building_id', 'buildingId')
            ->set('buildlist_entity_id', 'entityId')
            ->set('buildlist_current_level', 'currentLevel')
            ->set('buildlist_build_type', 'buildType')
            ->set('buildlist_build_start_time', 'startTime')
            ->set('buildlist_build_end_time', 'endTime')
            ->set('buildlist_prod_percent', 'prodPercent')
            ->set('buildlist_people_working', 'peopleWorking')
            ->set('buildlist_people_working_status', 'peopleWorkingStatus')
            ->set('buildlist_deactivated', 'deactivated')
            ->set('buildlist_cooldown', 'cooldown')
            ->where('buildlist_id = :id')
            ->setParameters([
                'id' => $item->id,
                'userId' => $item->userId,
                'buildingId' => $item->buildingId,
                'entityId' => $item->entityId,
                'currentLevel' => $item->currentLevel,
                'buildType' => $item->buildType,
                'startTime' => $item->startTime,
                'endTime' => $item->endTime,
                'prodPercent' => $item->prodPercent,
                'peopleWorking' => $item->peopleWorking,
                'peopleWorkingStatus' => $item->peopleWorkingStatus,
                'deactivated' => $item->deactivated,
                'cooldown' => $item->cooldown,
            ])
            ->execute();
    }

    public function getPeopleWorking(int $entityId): PeopleWorking
    {
        $data = $this->createQueryBuilder()
            ->select('buildlist_building_id, buildlist_people_working')
            ->from('buildlist')
            ->where('buildlist_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute()
            ->fetchAllKeyValue();

        return new PeopleWorking($data);
    }

    public function setPeopleWorking(int $entityId, int $buildingId, int $people): void
    {
        $this->createQueryBuilder()
            ->update('buildlist')
            ->set('buildlist_people_working', ':peopleWorking')
            ->where('buildlist_entity_id = :entityId')
            ->andWhere('buildlist_building_id = :buildingId')
            ->setParameters([
                'entityId' => $entityId,
                'buildingId' => $buildingId,
                'peopleWorking' => $people,
            ])
            ->execute();
    }

    public function markBuildingWorkingStatus(int $userId, int $entityId, int $buildingId, bool $working): bool
    {
        return (bool) $this->createQueryBuilder()
            ->update('buildlist')
            ->set('buildlist_people_working_status', ':status')
            ->where('buildlist_building_id = :buildingId')
            ->andWhere('buildlist_user_id = :userId')
            ->andWhere('buildlist_entity_id = :entityId')
            ->setParameters([
                'buildingId' => $buildingId,
                'entityId' => $entityId,
                'userId' => $userId,
                'status' => (int) $working,
            ])->execute();
    }

    /**
     * @return array<int, array{name: string, cnt: int}>
     */
    public function getOverallCount(): array
    {
        $data = $this->getConnection()
            ->executeQuery(
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
            )
            ->fetchAllAssociative();

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'cnt' => (int) $arr['cnt'],
        ], $data);
    }

    /**
     * @return array<int, array{name: string, max: int}>
     */
    public function getBestLevels(): array
    {
        $data = $this->getConnection()
            ->executeQuery(
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
            )
            ->fetchAllAssociative();

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'max' => (int) $arr['max'],
        ], $data);
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('buildlist')
            ->where('buildlist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('buildlist')
            ->where('buildlist_id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
