<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class DefenseQueueRepository extends AbstractRepository
{
    public function add(int $userId, int $defenseId, int $entityId, int $count, int $startTime, int $endTime, int $objectTime): int
    {
        $this->createQueryBuilder()
            ->insert('def_queue')
            ->values([
                'queue_user_id' => ':userId',
                'queue_def_id' => ':defenseId',
                'queue_entity_id' => ':entityId',
                'queue_cnt' => ':count',
                'queue_starttime' => ':startTime',
                'queue_endtime' => ':endTime',
                'queue_objtime' => ':objTime',
                'queue_user_click_time' => ':userClickTime',
            ])
            ->setParameters([
                'userId' => $userId,
                'defenseId' => $defenseId,
                'entityId' => $entityId,
                'count' => $count,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'objTime' => $objectTime,
                'userClickTime' => time(),
            ])->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function getQueueItem(int $id): ?DefenseQueueItem
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('def_queue')
            ->where('queue_id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new DefenseQueueItem($data) : null;
    }

    /**
     * @return DefenseQueueItem[]
     */
    public function searchQueueItems(DefenseQueueSearch $search, int $limit = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit)
            ->select('*')
            ->from('def_queue')
            ->orderBy('queue_starttime', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new DefenseQueueItem($row), $data);
    }

    /**
     * @return AdminDefenseQueueItem[]
     */
    public function adminSearchQueueItems(DefenseQueueSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('def_queue.*')
            ->addSelect('def_name')
            ->addSelect('planet_name, planet_user_id')
            ->addSelect('entities.id, entities.pos, entities.code, cells.sx, cells.sy, cells.cx, cells.cy, cells.id as cid')
            ->addSelect('user_nick, user_points')
            ->from('def_queue')
            ->innerJoin('def_queue', 'planets', 'planets', 'planets.id = queue_entity_id')
            ->innerJoin('planets', 'entities', 'entities', 'planets.id = entities.id')
            ->innerJoin('planets', 'cells', 'cells', 'cells.id = entities.cell_id')
            ->innerJoin('def_queue', 'users', 'users', 'users.user_id = queue_user_id')
            ->innerJoin('def_queue', 'defense', 'defense', 'defense.def_id = queue_def_id')
            ->groupBy('queue_id')
            ->orderBy('queue_entity_id')
            ->addOrderBy('queue_endtime')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new AdminDefenseQueueItem($row), $data);
    }

    public function saveQueueItem(DefenseQueueItem $item): void
    {
        $this->createQueryBuilder()
            ->update('def_queue')
            ->set('queue_user_id', ':userId')
            ->set('queue_def_id', ':defenseId')
            ->set('queue_entity_id', ':entityId')
            ->set('queue_cnt', ':count')
            ->set('queue_starttime', ':startTime')
            ->set('queue_endtime', ':endTime')
            ->set('queue_objtime', ':objectTime')
            ->set('queue_build_type', ':buildType')
            ->set('queue_user_click_time', ':userClickTime')
            ->where('queue_id = :id')
            ->setParameters([
                'id' => $item->id,
                'userId' => $item->userId,
                'defenseId' => $item->defenseId,
                'entityId' => $item->entityId,
                'count' => $item->count,
                'startTime' => $item->startTime,
                'endTime' => $item->endTime,
                'objectTime' => $item->objectTime,
                'buildType' => $item->buildType,
                'userClickTime' => $item->userClickTime,
            ])
            ->execute();
    }

    public function deleteQueueItem(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('def_queue')
            ->where('queue_id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('def_queue')
            ->execute()
            ->fetchOne();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(*)')
            ->from('def_queue')
            ->where($qb->expr()->notIn('queue_user_id', ':userIds'))
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
            ->delete('def_queue')
            ->where($qb->expr()->notIn('queue_user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function freezeConstruction(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('def_queue')
            ->set('queue_build_type', ':type')
            ->where('queue_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'type' => 1,
            ])
            ->execute();
    }

    public function unfreezeConstruction(int $userId, int $duration): void
    {
        $this->createQueryBuilder()
            ->update('def_queue')
            ->set('queue_build_type', ':type')
            ->set('queue_starttime', 'queue_starttime + :duration')
            ->set('queue_endtime', 'queue_endtime + :duration')
            ->where('queue_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'type' => 0,
                'duration' => $duration,
            ])
            ->execute();
    }
}
