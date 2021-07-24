<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class UserMultiRepository extends AbstractRepository
{
    public function addOrUpdateEntry(int $userId, int $multiId, string $reason): void
    {
        $exists = (bool) $this
            ->createQueryBuilder()
            ->select('1')
            ->from('user_multi')
            ->where('user_id = :userId')
            ->andWhere('multi_id = :multiId')
            ->setParameters([
                'userId' => $userId,
                'multiId' => $multiId,
            ])
            ->execute()
            ->fetchOne();

        if ($exists) {
            $this->createQueryBuilder()
                ->update('user_multi')
                ->set('activ', ':active')
                ->set('connection', ':reason')
                ->set('timestamp', ':now')
                ->where('user_id = :userId')
                ->andWhere('multi_id = :multiId')
                ->setParameters([
                    'userId' => $userId,
                    'multiId' => $multiId,
                    'active' => 1,
                    'now' => time(),
                    'reason' => $reason,
                ])
                ->execute();
        } else {
            $this->createQueryBuilder()
                ->insert('user_multi')
                ->values([
                    'connection' => ':reason',
                    'user_id' => ':userId',
                    'multi_id' => ':multiId',
                    'timestamp' => ':now',
                ])
                ->setParameters([
                    'userId' => $userId,
                    'multiId' => $multiId,
                    'now' => time(),
                    'reason' => $reason,
                ])
                ->execute();
        }
    }

    public function addEmptyEntry(int $userId): void
    {
        $this->createQueryBuilder()
            ->insert('user_multi')
            ->values([
                'user_id' => ':userId',
                'timestamp' => ':now',
            ])
            ->setParameters([
                'userId' => $userId,
                'now' => time(),
            ])
            ->execute();
    }

    public function updateEntry(int $id, int $userId, int $multiId, string $reason): void
    {
        $this->createQueryBuilder()
            ->update('user_multi')
            ->set('multi_id', ':multiId')
            ->set('connection', ':reason')
            ->set('timestamp', ':now')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'multiId' => $multiId,
                'reason' => $reason,
                'now' => time(),
            ])
            ->execute();
    }

    public function deactivateEntry(int $id): void
    {
        $this->createQueryBuilder()
            ->update('user_multi')
            ->set('active', ':active')
            ->set('timestamp', ':now')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'active' => 0,
                'now' => time(),
            ])
            ->execute();
    }

    public function deactivate(int $userId, int $multiId): void
    {
        $this->createQueryBuilder()
            ->update('user_multi')
            ->set('activ', ':active')
            ->set('timestamp', ':now')
            ->where('user_id = :userId')
            ->andWhere('multi_id = :multiId')
            ->setParameters([
                'userId' => $userId,
                'multiId' => $multiId,
                'active' => 0,
                'now' => time(),
            ])
            ->execute();
    }

    /**
     * @return UserMulti[]
     */
    public function getUserEntries(int $userId, bool $active = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('m.*, u.user_nick as multi_nick')
            ->from('user_multi', 'm')
            ->leftJoin('m', 'users', 'u', 'u.user_id = m.multi_id')
            ->where('m.user_id = :userId');

        if ($active !== null) {
            $qb
                ->andWhere('m.activ = :active')
                ->setParameter('active', (int) $active);
        }

        $data = $qb
            ->setParameter('userId', $userId, )
            ->orderBy('m.id', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserMulti($row), $data);
    }

    public function getUserEntry(int $userId, int $id): ?UserMulti
    {
        $data = $this->createQueryBuilder()
            ->select('m.*, u.user_nick as multi_nick')
            ->from('user_multi', 'm')
            ->leftJoin('m', 'users', 'u', 'u.user_id = m.multi_id')
            ->where('m.user_id = :userId')
            ->andWhere('m.id = :id')
            ->setParameter('userId', $userId, )
            ->setParameter('id', $id, )
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new UserMulti($data) : null;
    }

    public function existsEntryWith(int $userId, int $otherUserId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('user_multi')
            ->where('user_id = :userId AND multi_id = :otherUserId')
            ->orWhere('user_id = :otherUserId AND multi_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'otherUserId' => $otherUserId,
            ])
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
            ->from('user_multi')
            ->where($qb->expr()->notIn('user_id', ':userIds'))
            ->orWhere($qb->expr()->notIn('multi_id', ':userIds'))
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
            ->delete('user_multi')
            ->where($qb->expr()->notIn('user_id', ':userIds'))
            ->orWhere($qb->expr()->notIn('multi_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function deleteUserEntries(int $userId): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('user_multi')
            ->where('user_id = :userId')
            ->orWhere('multi_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function deleteEntry(int $id): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('user_multi')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
