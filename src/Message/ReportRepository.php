<?php

declare(strict_types=1);

namespace EtoA\Message;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class ReportRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('reports')
            ->execute()
            ->fetchOne();
    }

    public function countNotArchived(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('reports')
            ->where('archived = 0')
            ->execute()
            ->fetchOne();
    }

    public function countDeleted(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('reports')
            ->where('deleted = 1')
            ->execute()
            ->fetchOne();
    }

    public function countUserUnread(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('reports')
            ->where('user_id = :userId')
            ->andWhere('`read` = 0')
            ->andWhere('`deleted` = 0')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchOne();
    }

    /**
     * @return Report[]
     */
    public function searchReports(ReportSearch $search, int $limit, int $first = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('reports')
            ->orderBy('timestamp', 'DESC');

        $data = $this->applySearchSortLimit($qb, $search, null, $limit, $first)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Report($row), $data);
    }

    public function countReports(ReportSearch $search): int
    {
        $qb = $this->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('reports');

        return (int) $this->applySearchSortLimit($qb, $search)
            ->execute()
            ->fetchOne();
    }

    protected function addReport(string $type, int $userId, int $allianceId, ?string $content, int $entity1Id, int $entity2Id, int $opponentId): int
    {
        $this->createQueryBuilder()
            ->insert('reports')
            ->values([
                'timestamp' => ':time',
                'type' => ':type',
                'user_id' => ':userId',
                'alliance_id' => ':allianceId',
                'content' => ':content',
                'entity1_id' => ':entity1Id',
                'entity2_id' => ':entity2Id',
                'opponent1_id' => ':opponentId',
            ])
            ->setParameters([
                ':time' => time(),
                'type' => $type,
                'userId' => $userId,
                'allianceId' => $allianceId,
                'content' => $content,
                'entity1Id' => $entity1Id,
                'entity2Id' => $entity2Id,
                'opponentId' => $opponentId,
            ])
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @param int[] $ids
     */
    public function archive(int $userId, array $ids): void
    {
        if (count($ids) === 0) {
            return;
        }

        $this->createQueryBuilder()
            ->update('reports')
            ->set('archived', '1')
            ->where('user_id = :userId')
            ->andWhere('id IN (:ids)')
            ->setParameter('userId', $userId)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    /**
     * @param int[] $ids
     */
    public function delete(int $userId, bool $archived, array $ids = null, string $type = null): void
    {
        if ($ids !== null && count($ids) === 0) {
            return;
        }

        $qb = $this->createQueryBuilder()
            ->update('reports')
            ->set('deleted', '1')
            ->where('user_id = :userId')
            ->andWhere('archived = :archived')
            ->setParameter('userId', $userId)
            ->setParameter('archived', $archived);

        if ($ids !== null) {
            $qb
                ->andWhere('id IN (:ids)')
                ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);
        }

        if ($type !== null) {
            $qb
                ->andWhere('type = :type')
                ->setParameter('type', $type);
        }

        $qb
            ->execute();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(id)')
            ->from('reports')
            ->where($qb->expr()->notIn('user_id', ':userIds'))
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
            ->delete('reports')
            ->where($qb->expr()->notIn('user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function removeUnarchivedread(int $beforeTimestamp): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('reports')
            ->where('archived = 0')
            ->andWhere('read = 1')
            ->andWhere('timestamp < :timestamp')
            ->setParameter('timestamp', $beforeTimestamp)
            ->execute();
    }

    public function removeDeleted(int $beforeTimestamp): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('reports')
            ->where('deleted = 1')
            ->andWhere('timestamp < :timestamp')
            ->setParameter('timestamp', $beforeTimestamp)
            ->execute();
    }
}
