<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\ArrayParameterType;
use EtoA\Core\AbstractRepository;

class UserSurveillanceRepository extends AbstractRepository
{
    /**
     * @return UserSurveillance[]
     */
    public function search(UserSurveillanceSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('*')
            ->from('user_surveillance')
            ->orderBy('timestamp', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSurveillance($row), $data);
    }

    /**
     * @return array<string, int>
     */
    public function countPerSession(UserSurveillanceSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('session, COUNT(id)')
            ->from('user_surveillance')
            ->groupBy('session')
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return array<string, array{min: int, max: int}>
     */
    public function timestampsPerSession(UserSurveillanceSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('session, MAX(timestamp) max, MIN(timestamp) min')
            ->from('user_surveillance')
            ->groupBy('session')
            ->orderBy('MAX(timestamp)', 'DESC')
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $result[(string) $row['session']] = [
                'min' => (int) $row['min'],
                'max' => (int) $row['max'],
            ];
        }

        return $result;
    }

    /**
     * @param list<int> $userIds
     * @return array<int, int>
     */
    public function counts(array $userIds): array
    {
        if (count($userIds) === 0) {
            return [];
        }

        $data = $this->createQueryBuilder('q')
            ->select('user_id, COUNT(*)')
            ->from('user_surveillance')
            ->where('user_id IN (:userIds)')
            ->setParameter('userIds', $userIds, ArrayParameterType::INTEGER)
            ->groupBy('user_id')
            ->fetchAllKeyValue();

        $counts = [];
        foreach ($data as $userId => $count) {
            $counts[(int) $userId] = (int) $count;
        }

        return $counts;
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
        $data = $this->createQueryBuilder('q')
            ->select('s.user_id')
            ->from('user_surveillance', 's')
            ->innerJoin('s', 'users', 'u', 's.user_id=u.user_id')
            ->where('u.user_observe IS NULL')
            ->groupBy('s.user_id')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['user_id'], $data);
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder('q')
            ->delete('user_surveillance')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }
}
