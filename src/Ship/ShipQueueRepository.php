<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\ShipQueueItem;
use EtoA\Entity\User;

class ShipQueueRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipQueueItem::class);
    }

    public function add(int $userId, int $shipId, int $entityId, int $count, int $startTime, int $endTime, int $objectTime): int
    {
        $this->createQueryBuilder('q')
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
            ])->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function getQueueItem(int $id): ?ShipQueueItem
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ship_queue')
            ->where('queue_id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? ShipQueueItem::createFromData($data) : null;
    }

    /**
     * @return array<int, int>
     */
    public function getUserQueuedShipCounts(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('queue_ship_id, SUM(queue_cnt)')
            ->from('ship_queue')
            ->where('queue_user_id = :userId')
            ->andWhere('queue_endtime > :now')
            ->setParameters([
                'userId' => $userId,
                'now' => time(),
            ])
            ->groupBy('queue_ship_id')
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return ShipQueueItem[]
     */
    public function searchQueueItems(ShipQueueSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.startTime', 'ASC')
            ->getQuery()
            ->execute();

        return array_map(fn ($row) => ShipQueueItem::createFromData($row), $data);
    }

    public function saveQueueItem(ShipQueueItem $item): void
    {
        $this->createQueryBuilder('q')
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
            ->executeQuery();
    }

    public function deleteQueueItem(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('ship_queue')
            ->where('queue_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    public function freezeConstruction(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->update('ship_queue')
            ->set('queue_build_type', ':type')
            ->where('queue_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'type' => 1,
            ])
            ->executeQuery();
    }

    public function unfreezeConstruction(int $userId, int $duration): void
    {
        $this->createQueryBuilder('q')
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
            ->executeQuery();
    }
}
