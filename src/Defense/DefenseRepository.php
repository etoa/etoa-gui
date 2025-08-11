<?php

declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\DefenseListItem;
use EtoA\Entity\Entity;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Universe\Entity\EntityRepository;

class DefenseRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry,private readonly EntityRepository $entityRepository)
    {
        parent::__construct($registry, DefenseListItem::class);
    }

    /**
     * @return DefenseListItem[]
     */
    public function findForUser(User $user, ?Planet $entity = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.count > 0')
            ->setParameter('user', $user);

        if ($entity) {
            $qb
                ->andWhere('q.entity = :entity')
                ->setParameter('entity', $entity);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function getItem(int $id): ?DefenseListItem
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('deflist')
            ->where('deflist_id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? DefenseListItem::createFromData($data) : null;
    }

    public function addDefense(int $defenseId, int $amount, int $userId, int $entityId): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot add negative defense count');
        }


        $this->addDefenseCount($defenseId, $amount, $userId, $entityId);
    }

    public function setDefenseCount(int $id, int $count): void
    {
        $this->createQueryBuilder('q')
            ->update('deflist')
            ->set('deflist_count', ':count')
            ->where('deflist_id = :id')
            ->setParameters([
                'count' => $count,
                'id' => $id,
            ])->executeQuery();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('deflist')
            ->where('deflist_id = :id')
            ->setParameters([
                'id' => $id,
            ])->executeQuery();
    }

    public function removeDefense(DefenseListItem $defenseListItem, int $amount): int
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot remove negative defense count');
        }

        $amount = min($defenseListItem->getCount(), $amount);

        $defenseListItem->setCount($defenseListItem->getCount()-$amount);
        $this->save();

        return $amount;
    }

    private function addDefenseCount(int $defenseId, int $amount, int $userId, int $entityId): void
    {
        $item = $this->findOneBy(['userId'=>$userId,'defenseId'=>$defenseId,'entityId'=>$entityId]);

        if(!$item) {
            $item = new DefenseListItem();
            $item->setUserId($userId);
            $item->setEntityId($entityId);
            $item->setEntity($this->entityRepository->findOneBy(['id'=>$entityId]));
            $item->setDefenseId($defenseId);
        }

        $item->setCount($item->getCount()+max(0, $amount));

        $this->getEntityManager()->persist($item);
        $this->getEntityManager()->flush();
    }

    public function getDefenseCount(int $userId, int $defenseId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('SUM(deflist_count)')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_def_id = :defenseId')
            ->setParameters([
                'userId' => $userId,
                'defenseId' => $defenseId,
            ])
            ->fetchOne();
    }

    public function countBuildInProgress(int $userId, int $entityId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(queue_id)')
            ->from('def_queue')
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

    public function countJammingDevicesOnEntity(Planet $entity): ?int
    {
        $data = $this->createQueryBuilder('q')
            ->where('q.entity = :entity')
            ->andWhere('q.count > 0')
            ->andWhere('d.jam = 1')
            ->innerJoin('App:Defense', 'd', 'WITH', 'q.defense = d.id')
            ->setParameters([
                'entity' => $entity,
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();

        return $data?->getCount();
    }

    /**
     * @return array<int, DefenseListItem>
     */
    public function getRecyclable (User $user, Planet $entity): array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('App:Defense', 'd', 'WITH', 'q.defense = d.id')
            ->where('q.user = :user')
            ->andWhere('q.entity = :entity')
            ->andWhere('q.count > 0')
            ->andWhere('d.buildable = 1')
            ->setParameters([
                'user' => $user,
                'entity' => $entity,
            ])
            ->getQuery()
            ->execute();
    }


    /**
     * @return array<int, DefenseListItem>
     */
    public function getEntityDefenseCounts(User $user, Planet $entity): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.entity = :entity')
            ->andWhere('q.count > 0')
            ->setParameters([
                'user' => $user,
                'entity' => $entity,
            ])
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

    public function cleanupEmpty(): void
    {
        $this->createQueryBuilder('q')
            ->delete('deflist')
            ->where('deflist_count = 0')
            ->executeQuery();
    }

    public function countEmpty(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(deflist_id)')
            ->from('deflist')
            ->where('deflist_count = 0')
            ->fetchOne();
    }

    /**
     * @return array<int, array{name: string, cnt: int, max: int}>
     */
    public function getOverallCount(): array
    {
        $data= $this->createQueryBuilder('q')
            ->select( 'SUM(q.count) as cnt')
            ->addSelect('d.name as name')
            ->addSelect('MAX(q.count) as max')
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->innerJoin('App:Defense', 'd', 'WITH', 'd.id = q.defense')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->groupBy('d.id')
            ->orderBy('cnt', 'DESC')
            ->getQuery()
            ->execute();

        return array_map(fn ($arr) => [
            'name' => $arr['name'],
            'cnt' => (int) $arr['cnt'],
            'max' => (int) $arr['max'],
        ], $data);
    }

    public function cleanUp(): int
    {
        return $this->getConnection()
            ->executeQuery(
                "DELETE FROM
                    `deflist`
                WHERE
                    `deflist_count`='0'
                ;"
            )->rowCount();
    }

    /**
     * @return DefenseListItem[]
     */
    public function search(DefenseListSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->select('deflist.*')
            ->from('deflist')
            ->fetchAllAssociative();

        return array_map(fn ($row) => DefenseListItem::createFromData($row), $data);
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
}
