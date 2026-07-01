<?php

declare(strict_types=1);

namespace EtoA\Technology;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\Technology;
use EtoA\Entity\TechnologyListItem;
use EtoA\Entity\User;

class TechnologyListItemRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechnologyListItem::class);
    }

    /**
     * @return TechnologyListItem[]
     */
    public function findForUser(User $user, int $endTimeAfter = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->setParameter('user', $user);

        if ($endTimeAfter) {
            $qb
                ->andWhere('q.endTime > :time')
                ->setParameter('time', $endTimeAfter);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function getEntry(int $id): ?TechnologyListItem
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('techlist')
            ->where('techlist_id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? TechnologyListItem::createFromData($data) : null;
    }

    public function countSearch(TechnologyListItemSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(techlist_id)')
            ->getFirstResult();
    }

    public function searchEntry(TechnologyListItemSearch $search): ?TechnologyListItem
    {

        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, 1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, int>
     */
    public function getTechnologyLevels(int|User $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('IDENTITY(q.technology), q.currentLevel')
            ->where('q.user = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->getQuery()
            ->execute();

        return array_column($data, 'currentLevel', 'id');
    }

    public function getTechnologyLevel(User $user, Technology|int $technology): ?int
    {
        return $this->findOneBy(['user'=>$user,'technology'=>$technology])?->getCurrentLevel();
    }

    public function addTechnology(Technology $technology, int $level, User $user, Entity $entity): void
    {
        $item = $this->findOneBy(['user'=>$user,'technology'=>$technology]);

        if(!$item) {
            $item = new TechnologyListItem();
            $this->persist($item);
        }

        $item->setUser($user);
        $item->setEntity($entity);
        $item->setTechnology($technology);
        $item->setCurrentLevel(max(0, $level));

        $this->save();
    }

    public function updateBuildStatus(int $userId, int $entityId, int $technologyId, int $status, int $startTime, int $endTime): bool
    {
        return (bool) $this->getConnection()->executeQuery('INSERT INTO techlist (
                techlist_user_id,
                techlist_entity_id,
                techlist_tech_id,
                techlist_build_type,
                techlist_build_start_time,
                techlist_build_end_time
            ) VALUES (
                :userId,
                :entityId,
                :technologyId,
                :status,
                :startTime,
                :endTime
            ) ON DUPLICATE KEY
            UPDATE techlist_entity_id = :entityId, techlist_build_type = :status, techlist_build_start_time = :startTime, techlist_build_end_time = :endTime;
        ', [
            'userId' => $userId,
            'entityId' => $entityId,
            'technologyId' => $technologyId,
            'status' => $status,
            'startTime' => $startTime,
            'endTime' => $endTime,
        ])->rowCount();
    }

    public function countResearchInProgress(int $userId, int $entityId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('COUNT(techlist_id)')
            ->from('techlist')
            ->where('techlist_user_id = :userId')
            ->where('techlist_entity_id = :entityId')
            ->andWhere('techlist_build_type > 2')
            ->andWhere('techlist_tech_id <> :techId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'techId' => TechnologyId::GEN,
            ])
            ->getFirstResult();
    }

    public function isTechInProgress(int $userId, int $technologyId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('1')
            ->where('techlist_user_id = :userId')
            ->andWhere('techlist_build_type > 2')
            ->andWhere('techlist_tech_id = :techId')
            ->setParameters([
                'userId' => $userId,
                'techId' => $technologyId,
            ])
            ->getFirstResult();
    }

    public function countEmpty(): int
    {
        return $this->count(['currentLevel'=>0,'startTime'=>0,'endTime'=>0]);
    }

    public function deleteEmpty(): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.currentLevel=0')
            ->andWhere('q.startTime=0')
            ->andWhere('q.endTime=0')
            ->getQuery()
            ->execute();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('techlist')
            ->where('techlist_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    /**
     * @return array<int, array{name: string, max: int}>
     */
    public function getBestLevels(): array
    {
        $data= $this->createQueryBuilder('q')
            ->select( 'MAX(q.currentLevel) as max')
            ->addSelect('t.name as name')
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->innerJoin('App:Technology', 't', 'WITH', 't.id = q.technology')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->groupBy('t.id')
            ->orderBy('max', 'DESC')
            ->getQuery()
            ->execute();

        return array_map(fn ($arr) => [
            'name' => $arr['name'],
            'max' => (int) $arr['max'],
        ], $data);
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

    public function freezeConstruction(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->set('q.buildType', 'q.buildType - 2')
            ->where('q.userId = :userId')
            ->andWhere('q.startTime > 0')
            ->setParameters([
                'userId' => $userId,
            ])
            ->getQuery()
            ->execute();
    }

    public function unfreezeConstruction(int|User $userId, int $duration): void
    {
        $this->createQueryBuilder('q')
            ->set('q.buildType', 'q.buildType + 2')
            ->set('q.startTime', 'q.startTime +'. $duration)
            ->set('q.endTime', 'q.endTime +'. $duration)
            ->where('q.user = :userId')
            ->andWhere('q.startTime > 0')
            ->setParameters([
                'userId' => $userId
            ])
            ->getQuery()
            ->execute();
    }

    /**
     * @return TechnologyListItem[]
     */
    public function search(TechnologyListItemSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->getQuery()
            ->execute();
    }
}
