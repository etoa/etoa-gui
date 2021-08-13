<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class ShipQueueRepository extends AbstractRepository
{
    public function add(int $userId, int $shipId, int $entityId, int $count, int $startTime, int $endTime, int $objectTime): int
    {
        $this->createQueryBuilder()
            ->insert('ship_queue')
            ->values([
                'queue_user_id' => ':userId',
                'queue_ship_id' => ':shipId',
                'queue_entity_id' => ':entityId',
                'queue_cnt' => ':count',
                'queue_starttime' => ':startTime',
                'queue_endtime' => ':endTime',
                'queue_objtime' => ':objTime',
            ])
            ->setParameters([
                'userId' => $userId,
                'shipId' => $shipId,
                'entityId' => $entityId,
                'count' => $count,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'objTime' => $objectTime,
            ])->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function getQueueItem(int $id): ?ShipQueueItem
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('ship_queue')
            ->where('queue_id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new ShipQueueItem($data) : null;
    }

    /**
     * @return array<int, int>
     */
    public function getUserQueuedShipCounts(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('queue_ship_id, SUM(queue_cnt)')
            ->from('ship_queue')
            ->where('queue_user_id = :userId')
            ->andWhere('queue_endtime > :now')
            ->setParameters([
                'userId' => $userId,
                'now' => time(),
            ])
            ->groupBy('queue_ship_id')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return ShipQueueItem[]
     */
    public function searchQueueItems(ShipQueueSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('*')
            ->from('ship_queue')
            ->orderBy('queue_starttime', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new ShipQueueItem($row), $data);
    }

    /**
     * @return AdminShipQueueItem[]
     */
    public function adminSearchQueueItems(ShipQueueSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('ship_queue.*')
            ->addSelect('ship_name')
            ->addSelect('planet_name, planet_user_id')
            ->addSelect('entities.id, entities.pos, entities.code, cells.sx, cells.sy, cells.cx, cells.cy, cells.id as cid')
            ->addSelect('user_nick, user_points')
            ->from('ship_queue')
            ->innerJoin('ship_queue', 'planets', 'planets', 'planets.id = queue_entity_id')
            ->innerJoin('planets', 'entities', 'entities', 'planets.id = entities.id')
            ->innerJoin('planets', 'cells', 'cells', 'cells.id = entities.cell_id')
            ->innerJoin('ship_queue', 'users', 'users', 'users.user_id = queue_user_id')
            ->innerJoin('ship_queue', 'ships', 'ships', 'ships.ship_id = queue_ship_id')
            ->groupBy('queue_id')
            ->orderBy('queue_entity_id')
            ->orderBy('queue_endtime')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new AdminShipQueueItem($row), $data);
    }

    public function saveQueueItem(ShipQueueItem $item): void
    {
        $this->createQueryBuilder()
            ->update('ship_queue')
            ->set('queue_user_id', ':userId')
            ->set('queue_ship_id', ':shipId')
            ->set('queue_entity_id', ':entityId')
            ->set('queue_cnt', ':count')
            ->set('queue_starttime', ':startTime')
            ->set('queue_endtime', ':endTime')
            ->set('queue_objtime', ':objectTime')
            ->set('queue_build_type', ':buildType')
            ->where('queue_id = :id')
            ->setParameters([
                'id' => $item->id,
                'userId' => $item->userId,
                'shipId' => $item->shipId,
                'entityId' => $item->entityId,
                'count' => $item->count,
                'startTime' => $item->startTime,
                'endTime' => $item->endTime,
                'objectTime' => $item->objectTime,
                'buildType' => $item->buildType,
            ])
            ->execute();
    }

    public function deleteQueueItem(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('ship_queue')
            ->where('queue_id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('ship_queue')
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
            ->from('ship_queue')
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
            ->delete('ship_queue')
            ->where($qb->expr()->notIn('queue_user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function freezeConstruction(int $userId): void
    {
        $this->createQueryBuilder()
            ->update('ship_queue')
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
            ->update('ship_queue')
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
