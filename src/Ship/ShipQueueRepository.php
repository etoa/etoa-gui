<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\ShipQueueItem;
use EtoA\Entity\User;

class ShipQueueRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipQueueItem::class);
    }

    public function countBuildInProgress(int $userId, int $entityId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(queue_id)')
            ->from('ship_queue')
            ->where('queue_entity_id = :entityId')
            ->andWhere('queue_user_id = :userId')
            ->andWhere('queue_starttime > 0')
            ->andWhere('queue_endtime > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->fetchOne();
    }

    /**
     * @return ShipQueueItem[]
     */
    public function findQueueItemsForUser(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('ship_queue')
            ->where('queue_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('queue_starttime', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipQueueItem::createFromData($row), $data);
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
    public function getUserQueuedShipCounts(User $user): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('IDENTITY(q.ship), SUM(q.count)')
            ->where('q.user = :user')
            ->andWhere('q.endTime > :now')
            ->setParameters([
                'user' => $user,
                'now' => time(),
            ])
            ->groupBy('q.ship')
            ->getQuery()
            ->getArrayResult();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return ShipQueueItem[]
     */
    public function searchQueueItems(ShipQueueSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.startTime', 'ASC')
            ->getQuery()
            ->execute();
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
            ->set('q.buildType', 1)
            ->where('q.userId = :userId')
            ->setParameters([
                'userId' => $userId
            ])
            ->getQuery()
            ->execute();
    }

    public function unfreezeConstruction(int $userId, int $duration): void
    {
        $this->createQueryBuilder('q')
            ->set('q.buildType', 0)
            ->set('q.startTime', 'q.startTime +'.$duration)
            ->set('q.endTime', 'q.endTime +'. $duration)
            ->where('q.userId = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->getQuery()
            ->execute();
    }

    public function removeForEntity(Entity $entity): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.entity = :entity')
            ->setParameter('entity', $entity)
            ->getQuery()
            ->execute();
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

    public function countBySearch(ShipQueueSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
