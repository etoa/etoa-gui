<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\Planet;
use EtoA\Entity\Ship;
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

    public function add(User $user, Ship $ship, Planet $entity, int $count, int $startTime, int $endTime, int $objectTime): void
    {
        $item = new ShipQueueItem();
        $item->setUser($user);
        $item->setShip($ship);
        $item->setEntity($entity);
        $item->setCount($count);
        $item->setStartTime($startTime);
        $item->setEndTime($endTime);
        $item->setObjectTime($objectTime);

        $this->persist($item);
        $this->save();
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

    public function deleteQueueItem(ShipQueueItem $item): void
    {
        $this->remove($item);
        $this->save();
    }

    public function freezeConstruction(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->set('q.buildType', 1)
            ->where('q.user = :userId')
            ->setParameters([
                'userId' => $userId
            ])
            ->getQuery()
            ->execute();
    }

    public function unfreezeConstruction(int|User $userId, int $duration): void
    {
        $this->createQueryBuilder('q')
            ->set('q.buildType', 0)
            ->set('q.startTime', 'q.startTime +'.$duration)
            ->set('q.endTime', 'q.endTime +'. $duration)
            ->where('q.user = :userId')
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
