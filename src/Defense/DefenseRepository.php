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
        $qb = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new DefenseListItem($row), $data);
    }

    public function getItem(int $id): ?DefenseListItem
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('deflist')
            ->where('deflist_id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new DefenseListItem($data) : null;
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
        $this->createQueryBuilder()
            ->update('deflist')
            ->set('deflist_count', ':count')
            ->where('deflist_id = :id')
            ->setParameters([
                'count' => $count,
                'id' => $id,
            ])->execute();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('deflist')
            ->where('deflist_id = :id')
            ->setParameters([
                'id' => $id,
            ])->execute();
    }

    public function removeDefense(int $defenseId, int $amount, int $userId, int $entityId): int
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot remove negative defense count');
        }

        $available = (int) $this->createQueryBuilder()
            ->select('deflist_count')
            ->from('deflist')
            ->where('deflist_def_id = :defenseId')
            ->andWhere('deflist_user_id = :userId')
            ->andWhere('deflist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'defenseId' => $defenseId,
            ])->execute()->fetchOne();

        $amount = min($available, $amount);

        $this->createQueryBuilder()
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
            ->execute();

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
        $data = $this->createQueryBuilder()
            ->select('deflist_def_id, deflist_count')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_entity_id = :entityId')
            ->andWhere('deflist_count > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function getDefenseCount(int $userId, int $defenseId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('SUM(deflist_count)')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_def_id = :defenseId')
            ->setParameters([
                'userId' => $userId,
                'defenseId' => $defenseId,
            ])->execute()
            ->fetchOne();
    }

    public function countBuildInProgress(int $userId, int $entityId): int
    {
        return (int) $this->createQueryBuilder()
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
            ->execute()
            ->fetchOne();
    }

    public function countJammingDevicesOnEntity(int $entityId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('dl.deflist_count')
            ->from('deflist', 'dl')
            ->where('dl.deflist_entity_id = :entityId')
            ->andWhere('dl.deflist_count > 0')
            ->innerJoin('dl', 'defense', 'd', 'dl.deflist_def_id = d.def_id AND def_jam = 1')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function removeForEntity(int $entityId): void
    {
        $this->createQueryBuilder()
            ->delete('def_queue')
            ->where('queue_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('deflist')
            ->where('deflist_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute();
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('def_queue')
            ->where('queue_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('deflist')
            ->where('deflist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function cleanupEmpty(): void
    {
        $this->createQueryBuilder()
            ->delete('deflist')
            ->where('deflist_count = 0')
            ->execute();
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('deflist')
            ->execute()
            ->fetchOne();
    }

    public function countEmpty(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(deflist_id)')
            ->from('deflist')
            ->where('deflist_count = 0')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return array<int, array{name: string, cnt: int, max: int}>
     */
    public function getOverallCount(): array
    {
        $data = $this->getConnection()
            ->executeQuery(
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
            )
            ->fetchAllAssociative();

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'cnt' => (int) $arr['cnt'],
            'max' => (int) $arr['max'],
        ], $data);
    }

    public function cleanUp(): int
    {
        return $this->getConnection()
            ->executeStatement(
                "DELETE FROM
                    `deflist`
                WHERE
                    `deflist_count`='0'
                ;"
            );
    }

    /**
     * @return AdminDefenseListItem[]
     */
    public function adminSearchQueueItems(DefenseListSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('deflist.*')
            ->addSelect('def_name')
            ->addSelect('planet_name, planet_user_id')
            ->addSelect('entities.id, entities.pos, entities.code, cells.sx, cells.sy, cells.cx, cells.cy, cells.id as cid')
            ->addSelect('user_nick, user_points')
            ->from('deflist')
            ->innerJoin('deflist', 'planets', 'planets', 'planets.id = deflist_entity_id')
            ->innerJoin('planets', 'entities', 'entities', 'planets.id = entities.id')
            ->innerJoin('planets', 'cells', 'cells', 'cells.id = entities.cell_id')
            ->innerJoin('deflist', 'users', 'users', 'users.user_id = deflist_user_id')
            ->innerJoin('deflist', 'defense', 'defense', 'defense.def_id = deflist_def_id')
            ->groupBy('deflist_id')
            ->orderBy('deflist_entity_id')
            ->addOrderBy('def_order')
            ->addOrderBy('def_name')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new AdminDefenseListItem($row), $data);
    }
}
