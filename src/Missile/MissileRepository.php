<?php declare(strict_types=1);

namespace EtoA\Missile;

class MissileRepository extends \EtoA\Core\AbstractRepository
{
    public function addMissile(int $missileId, int $amount, int $userId, int $entityId): void
    {
        $hasMissiles = (bool) $this->createQueryBuilder('q')
            ->select('missilelist_id')
            ->from('missilelist')
            ->where('missilelist_user_id = :userId')
            ->andWhere('missilelist_entity_id = :entityId')
            ->andWhere('missilelist_missile_id = :missileId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'missileId' => $missileId,
            ])->fetchOne();

        if ($hasMissiles) {
            $this->createQueryBuilder('q')
                ->update('missilelist')
                ->set('missilelist_count', 'missilelist_count + :amount')
                ->where('missilelist_missile_id = :missileId')
                ->andWhere('missilelist_entity_id = :entityId')
                ->andWhere('missilelist_user_id = :userId')
                ->setParameters([
                    'amount' => $amount,
                    'missileId' => $missileId,
                    'userId' => $userId,
                    'entityId' => $entityId,
                ])->executeQuery();
        } else {
            $this->createQueryBuilder('q')
                ->insert('missilelist')
                ->values([
                    'missilelist_count' => ':amount',
                    'missilelist_missile_id' => ':missileId',
                    'missilelist_entity_id' => ':entityId',
                    'missilelist_user_id' => ':userId',
                ])
                ->setParameters([
                    'amount' => $amount,
                    'missileId' => $missileId,
                    'userId' => $userId,
                    'entityId' => $entityId,
                ])->executeQuery();
        }
    }

    public function countEmpty(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select("COUNT(missilelist_id)")
            ->from('missilelist')
            ->where('missilelist_count = 0')
            ->fetchOne();
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
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->select('*')
            ->from('missilelist')
            ->fetchAllAssociative();

        return array_map(fn ($row) => MissileListItem::createFromArray($row), $data);
    }

    public function searchOne(MissileListSearch $search): ?MissileListItem
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('*')
            ->from('missilelist')
            ->fetchAssociative();

        return $data !== false ? MissileListItem::createFromArray($data) : null;
    }

    /**
     * @return MissileListItem[]
     */
    public function findForUser(int $userId, ?int $entityId = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('missilelist')
            ->where('missilelist_user_id = :userId')
            ->setParameter('userId', $userId);

        if ($entityId !== null) {
            $qb
                ->andWhere('missilelist_entity_id = :entityId')
                ->setParameter('entityId', $entityId);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn ($row) => MissileListItem::createFromArray($row), $data);
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

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->delete('missilelist')
            ->where('missilelist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('missilelist')
            ->where('missilelist_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    public function deleteEmpty(): int
    {
        return $this->createQueryBuilder('q')
            ->delete('missilelist')
            ->where('missilelist_count=0')
            ->executeQuery()
            ->rowCount();
    }
}
