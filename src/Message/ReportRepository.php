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
}
