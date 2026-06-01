<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Missile;
use EtoA\Entity\MissileListItem;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class MissileRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissileListItem::class);
    }

    public function addMissile(Missile $missile, int $amount, User $user, Planet $entity): void
    {

        $item = $this->findOneBy(['user'=>$user,'missile'=>$missile,'entity'=>$entity]);

        if(!$item) {
            $item = new MissileListItem();
            $this->persist($item);
        }

        $item->setUser($user);
        $item->setEntity($entity);
        $item->setMissile($missile);
        $item->setCount($item->getCount()+max(0, $amount));

        $this->save();
    }

    public function countEmpty(): int
    {
        return $this->count(['count'=>0]);
    }

    /**
     * @return array<int, int>
     */
    public function getMissilesCounts(int $userId, int $entityId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("missilelist_missile_id, missilelist_count")
            ->from('missilelist')
            ->where('missilelist_user_id = :userId')
            ->andWhere('missilelist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return MissileListItem[]
     */
    public function search(MissileListSearch $search, int $limit, int $offset): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->getQuery()
            ->execute();
    }

    public function searchOne(MissileListSearch $search): ?MissileListItem
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return MissileListItem[]
     */
    public function findForUser(int|User $userId, int|Planet|null $entityId = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user = :userId')
            ->setParameter('userId', $userId);

        if ($entityId) {
            $qb
                ->andWhere('q.entity = :entityId')
                ->setParameter('entityId', $entityId);
        }

        return $qb->getQuery()->execute();
    }

    public function setMissileCount(int $id, int $count): void
    {
        $this->createQueryBuilder('q')
            ->update('missilelist')
            ->set('missilelist_count', ':count')
            ->where('missilelist_id = :id')
            ->setParameters([
                'count' => $count,
                'id' => $id,
            ])->executeQuery();
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

    public function deleteEmpty(): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.count=0')
            ->getQuery()
            ->execute();
    }

    public function countBySearch(MissileListSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
