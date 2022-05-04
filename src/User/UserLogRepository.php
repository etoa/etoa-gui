<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserLogRepository extends AbstractRepository
{
    public function add(int $userId, string $zone, string $message, string $host, bool $public): void
    {
        $this->createQueryBuilder()
            ->insert('user_log')
            ->values([
                'user_id' => ':userId',
                'timestamp' => ':now',
                'zone' => ':zone',
                'message' => ':message',
                'host' => ':host',
                'public' => ':public',
            ])
            ->setParameters([
                'userId' => $userId,
                'now' => time(),
                'zone' => $zone,
                'message' => $message,
                'host' => $host,
                'public' => (int) $public,
            ])
            ->executeQuery();
    }

    /**
     * @return UserLog[]
     */
    public function getUserLogs(int $userId, int $limit, bool $public = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('user_log')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('timestamp', 'DESC')
            ->setMaxResults($limit);

        if ($public !== null) {
            $qb
                ->andWhere('public = :public')
                ->setParameter('public', (int) $public);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserLog($row), $data);
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder()
            ->delete('user_log')
            ->executeQuery();
    }
}
