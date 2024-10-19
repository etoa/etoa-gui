<?php

declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\AbstractRepository;

class DefenseRepository extends AbstractRepository
{
    /**
     * @return DefenseListItem[]
     */
    public function findForUser(int $userId, ?int $entityId = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_count > 0')
            ->setParameter('userId', $userId);

        if ($entityId !== null) {
            $qb
                ->andWhere('deflist_entity_id = :entityId')
                ->setParameter('entityId', $entityId);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn ($row) => DefenseListItem::createFromData($row), $data);
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

    public function removeDefense(int $defenseId, int $amount, int $userId, int $entityId): int
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot remove negative defense count');
        }

        $available = (int) $this->createQueryBuilder('q')
            ->select('deflist_count')
            ->from('deflist')
            ->where('deflist_def_id = :defenseId')
            ->andWhere('deflist_user_id = :userId')
            ->andWhere('deflist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'defenseId' => $defenseId,
            ])->fetchOne();

        $amount = min($available, $amount);

        $this->createQueryBuilder('q')
            ->update('deflist')
            ->set('deflist_count', 'deflist_count - :amount')
            ->where('deflist_def_id = :defenseId')
            ->andWhere('deflist_user_id = :userId')
            ->andWhere('deflist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'defenseId' => $defenseId,
                'amount' => $amount,
            ])
            ->executeQuery()
            ->rowCount();

        return $amount;
    }

    private function addDefenseCount(int $defenseId, int $amount, int $userId, int $entityId): void
    {
        $this->getConnection()
            ->executeQuery(
                'INSERT INTO deflist (
                    deflist_user_id,
                    deflist_entity_id,
                    deflist_def_id,
                    deflist_count
                ) VALUES (
                    :userId,
                    :entityId,
                    :defenseId,
                    :amount
                ) ON DUPLICATE KEY
                UPDATE deflist_count = deflist_count + VALUES(deflist_count);
            ',
                [
                    'userId' => $userId,
                    'amount' => max(0, $amount),
                    'entityId' => $entityId,
                    'defenseId' => $defenseId,
                ]
            );
    }


    /**
     * @return array<int, int>
     */
    public function getEntityDefenseCounts(int $userId, int $entityId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('deflist_def_id, deflist_count')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_entity_id = :entityId')
            ->andWhere('deflist_count > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
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

    public function countJammingDevicesOnEntity(int $entityId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('dl.deflist_count')
            ->from('deflist', 'dl')
            ->where('dl.deflist_entity_id = :entityId')
            ->andWhere('dl.deflist_count > 0')
            ->innerJoin('dl', 'defense', 'd', 'dl.deflist_def_id = d.def_id AND def_jam = 1')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->fetchOne();
    }

    public function removeForEntity(int $entityId): void
    {
        $this->createQueryBuilder('q')
            ->delete('def_queue')
            ->where('queue_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('deflist')
            ->where('deflist_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->executeQuery();
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->delete('def_queue')
            ->where('queue_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('deflist')
            ->where('deflist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
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
        $data = $this->getConnection()
            ->fetchAllAssociative(
                "SELECT
                    defense.def_name as name,
                    SUM(deflist.deflist_count) as cnt,
                    MAX(deflist.deflist_count) as max
                FROM
                    defense
                INNER JOIN
                    (
                        deflist
                    INNER JOIN
                        users
                    ON
                        deflist_user_id = user_id
                        AND user_ghost = 0
                        AND user_hmode_from = 0
                        AND user_hmode_to = 0
                    )
                ON
                    deflist_def_id = def_id
                GROUP BY
                    defense.def_id
                ORDER BY
                    cnt DESC;"
            );

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
}
