<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\DBAL\Connection;

class MissileRepository extends \EtoA\Core\AbstractRepository
{
    public function addMissile(int $missileId, int $amount, int $userId, int $entityId): void
    {
        $hasMissiles = (bool) $this->createQueryBuilder()
            ->select('missilelist_id')
            ->from('missilelist')
            ->where('missilelist_user_id = :userId')
            ->andWhere('missilelist_entity_id = :entityId')
            ->andWhere('missilelist_missile_id = :missileId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'missileId' => $missileId,
            ])->execute()->fetchOne();

        if ($hasMissiles) {
            $this->createQueryBuilder()
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
                ])->execute();
        } else {
            $this->createQueryBuilder()
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
                ])->execute();
        }
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(missilelist_id)")
            ->from('missilelist')
            ->execute()
            ->fetchOne();
    }

    public function countEmpty(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(missilelist_id)")
            ->from('missilelist')
            ->where('missilelist_count = 0')
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
            ->select('count(missilelist_id)')
            ->from('missilelist')
            ->where($qb->expr()->notIn('missilelist_user_id', ':userIds'))
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
            ->delete('missilelist')
            ->where($qb->expr()->notIn('missilelist_user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    /**
     * @return array<int, int>
     */
    public function getMissilesCounts(int $userId, int $entityId): array
    {
        $data = $this->createQueryBuilder()
            ->select("missilelist_missile_id, missilelist_count")
            ->from('missilelist')
            ->where('missilelist_user_id = :userId')
            ->andWhere('missilelist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return MissileListItem[]
     */
    public function findForUser(int $userId, ?int $entityId = null): array
    {
        $qb = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new MissileListItem($row), $data);
    }

    public function setMissileCount(int $id, int $count): void
    {
        $this->createQueryBuilder()
            ->update('missilelist')
            ->set('missilelist_count', ':count')
            ->where('missilelist_id = :id')
            ->setParameters([
                'count' => $count,
                'id' => $id,
            ])->execute();
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('missilelist')
            ->where('missilelist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('missilelist')
            ->where('missilelist_id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
