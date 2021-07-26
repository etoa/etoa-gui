<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserLoginFailureRepository extends AbstractRepository
{
    public function add(int $userId, int $time, string $ip, string $client): void
    {
        $this->createQueryBuilder()
            ->insert('login_failures')
            ->values([
                'failure_time' => ':time',
                'failure_ip' => ':ip',
                'failure_user_id' => ':userId',
                'failure_client' => ':client',
            ])
            ->setParameters([
                'time' => $time,
                'ip' => $ip,
                'userId' => $userId,
                'client' => $client,
            ])
            ->execute();
    }

    /**
     * @return UserLoginFailure[]
     */
    public function getUserLoginFailures(int $userId, int $limit = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('l.*')
            ->addSelect('u.user_nick')
            ->from('login_failures', 'l')
            ->leftJoin('l', 'users', 'u', 'u.user_id = l.failure_user_id')
            ->where('l.failure_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('l.failure_time', 'DESC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $data = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserLoginFailure($row), $data);
    }

    /**
     * @return UserLoginFailure[]
     */
    public function getIpLoginFailures(string $ip): array
    {
        $data = $this->createQueryBuilder()
            ->select('l.*')
            ->addSelect('u.user_nick')
            ->from('login_failures', 'l')
            ->leftJoin('l', 'users', 'u', 'u.user_id = l.failure_user_id')
            ->where('l.failure_ip = :ip')
            ->setParameter('ip', $ip)
            ->orderBy('l.failure_time', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserLoginFailure($row), $data);
    }

    public function countLoginFailuresSince(int $userId, int $since): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(failure_user_id)')
            ->from('login_failures')
            ->where('failure_user_id = :userId')
            ->andWhere('failure_time > :since')
            ->setParameters([
                'userId' => $userId,
                'since' => $since,
            ])
            ->execute()
            ->fetchOne();
    }

    /**
     * @return array<array{count: int, userId: int, userNick: ?string}>
     */
    public function getLoginFailureCountsByIp(string $ip): array
    {
        $data = $this->createQueryBuilder()
            ->select('COUNT(failure_user_id) as count, failure_user_id, user_nick')
            ->from('login_failures', 'l')
            ->leftJoin('l', 'users', 'u', 'u.user_id = l.failure_user_id')
            ->where('l.failure_ip = :ip')
            ->setParameter('ip', $ip)
            ->groupBy('failure_user_id')
            ->orderBy('count', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => [
            'userId' => (int) $row['failure_user_id'],
            'userNick' => $row['user_nick'],
            'count' => (int) $row['count'],
        ], $data);
    }

    /**
     * @return array<array{count: int, ip: string, host: ?string}>
     */
    public function getLoginFailureCountsByUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('COUNT(failure_ip) as count, failure_ip, failure_host')
            ->from('login_failures', 'l')
            ->leftJoin('l', 'users', 'u', 'u.user_id = l.failure_user_id')
            ->where('l.failure_user_id = :userId')
            ->setParameter('userId', $userId)
            ->groupBy('failure_ip, failure_host')
            ->orderBy('count', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => [
            'ip' => $row['failure_ip'],
            'host' => $row['failure_host'],
            'count' => (int) $row['count'],
        ], $data);
    }

    /**
     * @return UserLoginFailure[]
     */
    public function findLoginFailures(string $sort, string $order): array
    {
        $data = $this->createQueryBuilder()
            ->select('l.*')
            ->addSelect('u.user_nick')
            ->from('login_failures', 'l')
            ->leftJoin('l', 'users', 'u', 'u.user_id = l.failure_user_id')
            ->orderBy('l.' . $sort, $order)
            ->setMaxResults(300)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserLoginFailure($row), $data);
    }
}
