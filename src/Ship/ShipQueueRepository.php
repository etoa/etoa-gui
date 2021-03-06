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
                'queue_user_click_time' => ':userClickTime',
            ])
            ->setParameters([
                'userId' => $userId,
                'shipId' => $shipId,
                'entityId' => $entityId,
                'count' => $count,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'objTime' => $objectTime,
                'userClickTime' => time(),
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
     * @return ShipQueueItem[]
     */
    public function findQueueItemsForUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('ship_queue')
            ->where('queue_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('queue_starttime', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new ShipQueueItem($row), $data);
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
            ->set('queue_user_click_time', ':userClickTime')
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
                'userClickTime' => $item->userClickTime,
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
}
