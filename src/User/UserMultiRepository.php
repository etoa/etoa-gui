<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\ArrayParameterType;
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
                ->executeQuery();
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
                ->executeQuery();
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
            ->executeQuery();
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
            ->executeQuery();
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
            ->executeQuery();
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
            ->executeQuery();
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
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserMulti($row), $data);
    }

    /**
     * @param int[] $userIds
     * @return array<int, UserMulti[]>
     */
    public function getUsersEntries(array $userIds): array
    {
        if (count($userIds) === 0) {
            return [];
        }

        $data = $this->createQueryBuilder()
            ->select('m.*, u.user_nick as multi_nick')
            ->from('user_multi', 'm')
            ->leftJoin('m', 'users', 'u', 'u.user_id = m.multi_id')
            ->where('m.user_id IN (:userIds)')
            ->setParameter('userIds', $userIds, ArrayParameterType::INTEGER)
            ->orderBy('m.id', 'DESC')
            ->fetchAllAssociative();

        $entries = [];
        foreach ($data as $row) {
            $entries[(int) $row['user_id']][] = new UserMulti($row);
        }

        return $entries;
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
            ->fetchOne();
    }

    public function deleteUserEntries(int $userId): int
    {
        $qb = $this->createQueryBuilder();

        return $qb
            ->delete('user_multi')
            ->where('user_id = :userId')
            ->orWhere('multi_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery()
            ->rowCount();
    }

    public function deleteEntry(int $id): int
    {
        $qb = $this->createQueryBuilder();

        return $qb
            ->delete('user_multi')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->rowCount();
    }
}
