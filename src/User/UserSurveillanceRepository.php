<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;
use EtoA\Entity\UserSurveillance;

class UserSurveillanceRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSurveillance::class);
    }

    /**
     * @return UserSurveillance[]
     */
    public function search(UserSurveillanceSearch $search): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->orderBy('q.timestamp', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<string, int>
     */
    public function countPerSession(UserSurveillanceSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('q.session, COUNT(q.id) as count')
            ->groupBy('q.session')
            ->getQuery()
            ->execute();

        $result = [];
        foreach ($data as $row) {
            $result[(string)$row['session']] = (int)$row['count'];
        }

        return $result;
    }

    /**
     * @return array<string, array{min: int, max: int}>
     */
    public function timestampsPerSession(UserSurveillanceSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('q.session, MAX(q.timestamp) as max, MIN(q.timestamp) as min')
            ->groupBy('q.session')
            ->orderBy('MAX(q.timestamp)', 'DESC')
            ->getQuery()
            ->execute();

        $result = [];

        foreach ($data as $row) {
            $result[(string)$row['session']] = [
                'min' => (int)$row['min'],
                'max' => (int)$row['max'],
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
            ->select("IDENTITY(q.user) as id")
            ->addSelect('COUNT(q) as count')
            ->where('q.user IN (:userIds)')
            ->setParameter('userIds', $userIds, ArrayParameterType::INTEGER)
            ->groupBy('q.user')
            ->getQuery()
            ->execute();

        $counts = [];
        foreach ($data as $row) {
            $counts[(int)$row['id']] = (int)$row['count'];
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

        return array_map(fn(array $row) => (int)$row['user_id'], $data);
    }

    public function removeForUser(User $user): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
