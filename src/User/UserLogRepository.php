<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class UserLogRepository extends AbstractRepository
{
    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(id)')
            ->from('user_log')
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
            ->delete('user_log')
            ->where($qb->expr()->notIn('user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }
}
