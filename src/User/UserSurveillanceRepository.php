<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class UserSurveillanceRepository extends AbstractRepository
{
    /**
     * @return UserSurveillance[]
     */
    public function search(UserSurveillanceSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('*')
            ->from('user_surveillance')
            ->orderBy('timestamp', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSurveillance($row), $data);
    }

    /**
     * @return array<string, int>
     */
    public function countPerSession(UserSurveillanceSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('session, COUNT(id)')
            ->from('user_surveillance')
            ->groupBy('session')
            ->orderBy('timestamp', 'DESC')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function count(UserSurveillanceSearch $search): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(*)')
            ->from('user_surveillance')
            ->execute()
            ->fetchOne();
    }

    public function addEntry(int $userId, string $page, string $request, string $requestRaw, string $post, string $sessionId): void
    {
        $this->getConnection()->executeQuery("
            INSERT DELAYED INTO user_surveillance (
                timestamp,
                user_id,
                page,
                request,
                request_raw,
                post,
                session
            ) VALUES (
                UNIX_TIMESTAMP(),
                :userId,
                :page,
                :request,
                :requestRaw,
                :post,
                :session
            )
        ", [
            'userId' => $userId,
            'page' => $page,
            'request' => $request,
            'requestRaw' => $requestRaw,
            'post' => $post,
            'session' => $sessionId,
        ]);
    }

    /**
     * @return int[]
     */
    public function getOrphanedUserIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select('s.user_id')
            ->from('user_surveillance', 's')
            ->innerJoin('s', 'users', 'u', 's.user_id=u.user_id')
            ->where('u.user_observe IS NULL')
            ->groupBy('s.user_id')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['user_id'], $data);
    }

    public function deletedOrphanedEntries(): int
    {
        $userIds = $this->getOrphanedUserIds();
        if (count($userIds) === 0) {
            return 0;
        }

        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('user_surveillance')
            ->where($qb->expr()->notIn('user_id', ':userIds'))
            ->setParameter('userIds', $userIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('user_surveillance')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}
