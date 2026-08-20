<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Defense;
use EtoA\Entity\DefenseQueueItem;
use EtoA\Entity\Entity;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class DefenseQueueRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefenseQueueItem::class);
    }

    public function add(User $user, Defense $defense, Planet $entity, int $count, int $startTime, int $endTime, int $objectTime): void
    {
        $item = new DefenseQueueItem();
        $item->setUser($user);
        $item->setDefense($defense);
        $item->setEntity($entity);
        $item->setCount($count);
        $item->setStartTime($startTime);
        $item->setEndTime($endTime);
        $item->setObjectTime($objectTime);

        $this->persist($item);
        $this->save();
    }

    public function getQueueItem(int $id): ?DefenseQueueItem
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('def_queue')
            ->where('queue_id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? DefenseQueueItem::createFromData($data) : null;
    }

    /**
     * @return DefenseQueueItem[]
     */
    public function searchQueueItems(DefenseQueueSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.startTime', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function deleteQueueItem(DefenseQueueItem $item): void
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
            ->set('q.startTime', 'q.startTime+'.  $duration)
            ->set('q.endTime', 'q.endTime +' .$duration)
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

    public function countBySearch(DefenseQueueSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
