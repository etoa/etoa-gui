<?php

declare(strict_types=1);

namespace EtoA\Admin;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;

class AdminSessionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminSession::class);
    }

    public function countActiveSessions(int $timeout): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(*)')
            ->from('admin_user_sessions')
            ->where('time_action > :timeout')
            ->setParameter('timeout', time() - $timeout)
            ->fetchOne();
    }

    /**
     * @return string[]
     */
    public function findByTimeout(int $timeout): array
    {
        return $this->createQueryBuilder('q')
            ->select("id")
            ->from('admin_user_sessions')
            ->where('time_action + :timeout = ' . time())
            ->setParameter('timeout', $timeout)
            ->fetchFirstColumn();
    }

    /**
     * @return AdminSession[]
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('s.*', 'u.user_nick')
            ->from('admin_user_sessions', 's')
            ->innerJoin('s', 'admin_users', 'u', 's.user_id=u.user_id')
            ->orderBy('time_action', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AdminSession($row), $data);
    }

    public function exists(string $id, int $userId, string $userAgent): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select("COUNT(*)")
            ->from('admin_user_sessions')
            ->where('id = :id')
            ->andWhere('user_id = :user_id')
            ->andWhere('user_agent = :user_agent')
            ->setParameters([
                'id' => $id,
                'user_id' => $userId,
                'user_agent' => $userAgent,
            ])
            ->fetchOne();
    }

    public function create(string $id, int $userId, string $ipAddr, string $userAgent, int $timeLogin): void
    {
        $this->createQueryBuilder('q')
            ->insert('admin_user_sessions')
            ->values([
                'id' => ':id',
                'user_id' => ':user_id',
                'ip_addr' => ':ip_addr',
                'user_agent' => ':user_agent',
                'time_login' => ':time_login',
            ])
            ->setParameters([
                'id' => $id,
                'user_id' => $userId,
                'ip_addr' => $ipAddr,
                'user_agent' => $userAgent,
                'time_login' => $timeLogin,
            ])
            ->executeQuery();
    }

    public function update(string $id, int $userId, int $time, string $ipAddress): void
    {
        $this->createQueryBuilder('q')
            ->update('admin_user_sessions')
            ->set('time_action', ':time')
            ->set('ip_addr', ':ip_addr')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'time' => $time,
                'ip_addr' => $ipAddress,
            ])
            ->executeQuery();
    }

    public function remove(string $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('admin_user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    public function removeByUserOrId(string $id, int $userId): void
    {
        $this->createQueryBuilder('q')
            ->delete('admin_user_sessions')
            ->where('id = :id')
            ->orWhere('user_id = :user_id')
            ->setParameter('id', $id)
            ->setParameter('user_id', $userId)
            ->executeQuery();
    }

    public function countSessionLog(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select("COUNT(*)")
            ->from("admin_user_sessionlog")
            ->fetchOne();
    }

    public function addSessionLog(AdminSession $adminSession, ?int $logoutTime): void
    {
        // TODO: Introduce admin session class for $adminSession and set it as type

        $this->createQueryBuilder('q')
            ->insert('admin_user_sessionlog')
            ->values([
                'session_id' => ':id',
                'user_id' => ':user_id',
                'ip_addr' => ':ip_addr',
                'user_agent' => ':user_agent',
                'time_login' => ':time_login',
                'time_action' => ':time_action',
                'time_logout' => $logoutTime ?? time(),
            ])
            ->setParameters([
                'id' => $adminSession->id,
                'user_id' => $adminSession->userId,
                'ip_addr' => $adminSession->ipAddr,
                'user_agent' => $adminSession->userAgent,
                'time_login' => $adminSession->timeLogin,
                'time_action' => $adminSession->timeAction,
            ])
            ->executeQuery();
    }

    public function removeSessionLogs(int $timestamp): int
    {
        return $this->createQueryBuilder('q')
            ->delete('admin_user_sessionlog')
            ->where('time_action < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->executeQuery()
            ->rowCount();
    }

    /**
     * @return AdminSessionLog[]
     */
    public function findSessionLogsByUser(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("l.*", 'u.user_nick')
            ->from("admin_user_sessionlog", 'l')
            ->innerJoin('l', 'admin_users', 'u', 'l.user_id=u.user_id AND l.user_id = :user_id')
            ->orderBy('time_action', 'DESC')
            ->setParameter('user_id', $userId)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AdminSessionLog($row), $data);
    }

    /**
     * @return AdminSessionCount[]
     */
    public function findUsersWithSessionLogs(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("user_nick", 'u.user_id', 'COUNT(*) as cnt')
            ->from("admin_users", 'u')
            ->innerJoin('u', 'admin_user_sessionlog', 'l', 'l.user_id=u.user_id')
            ->groupBy('u.user_id')
            ->orderBy('u.user_nick')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AdminSessionCount($row), $data);
    }
}
