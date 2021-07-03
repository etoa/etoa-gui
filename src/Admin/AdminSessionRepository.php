<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Core\AbstractRepository;

class AdminSessionRepository extends AbstractRepository
{
    public function countActiveSessions(int $timeout): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('admin_user_sessions')
            ->where('time_action > :timeout')
            ->setParameter('timeout', time() - $timeout)
            ->execute()
            ->fetchOne();
    }

    public function find(string $id): ?AdminSession
    {
        $data = $this->createQueryBuilder()
            ->select('s.*', 'u.user_nick')
            ->from('admin_user_sessions', 's')
            ->innerJoin('s', 'admin_users', 'u', 's.user_id=u.user_id')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new AdminSession($data) : null;
    }

    /**
     * @return string[]
     */
    public function findByTimeout(int $timeout): array
    {
        return array_column($this->createQueryBuilder()
            ->select("id")
            ->from('admin_user_sessions')
            ->where('time_action + :timeout = ' . time())
            ->setParameter('timeout', $timeout)
            ->execute()
            ->fetchAllAssociative(), 'id');
    }

    /**
     * @return AdminSession[]
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select('s.*', 'u.user_nick')
            ->from('admin_user_sessions', 's')
            ->innerJoin('s', 'admin_users', 'u', 's.user_id=u.user_id')
            ->orderBy('time_action', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AdminSession($row), $data);
    }

    public function exists(string $id, int $userId, string $userAgent, int $timeLogin): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('admin_user_sessions')
            ->where('id = :id')
            ->andWhere('user_id = :user_id')
            ->andWhere('user_agent = :user_agent')
            ->andWhere('time_login = :time_login')
            ->setParameters([
                'id' => $id,
                'user_id' => $userId,
                'user_agent' => $userAgent,
                'time_login' => $timeLogin,
            ])
            ->execute()
            ->fetchOne();
    }

    public function create(string $id, int $userId, string $ipAddr, string $userAgent, int $timeLogin): void
    {
        $this->createQueryBuilder()
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
            ->execute();
    }

    public function update(string $id, int $time, string $ipAddress): void
    {
        $this->createQueryBuilder()
            ->update('admin_user_sessions')
            ->set('time_action', ':time')
            ->set('ip_addr', ':ip_addr')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'time' => $time,
                'ip_addr' => $ipAddress,
            ])
            ->execute();
    }

    public function remove(string $id): void
    {
        $this->createQueryBuilder()
            ->delete('admin_user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function removeByUserOrId(string $id, int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('admin_user_sessions')
            ->where('id = :id')
            ->orWhere('user_id = :user_id')
            ->setParameter('id', $id)
            ->setParameter('user_id', $userId)
            ->execute();
    }

    public function countSessionLog(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from("admin_user_sessionlog")
            ->execute()
            ->fetchOne();
    }

    public function addSessionLog(AdminSession $adminSession, ?int $logoutTime): void
    {
        // TODO: Introduce admin session class for $adminSession and set it as type

        $this->createQueryBuilder()
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
            ->execute();
    }

    public function removeSessionLogs(int $timestamp): int
    {
        return $this->createQueryBuilder()
            ->delete('admin_user_sessionlog')
            ->where('time_action < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->execute();
    }

    /**
     * @return AdminSessionLog[]
     */
    public function findSessionLogsByUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select("l.*", 'u.user_nick')
            ->from("admin_user_sessionlog", 'l')
            ->innerJoin('l', 'admin_users', 'u', 'l.user_id=u.user_id AND l.user_id = :user_id')
            ->orderBy('time_action', 'DESC')
            ->setParameter('user_id', $userId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AdminSessionLog($row), $data);
    }

    /**
     * @return AdminSessionCount[]
     */
    public function findUsersWithSessionLogs(): array
    {
        $data = $this->createQueryBuilder()
            ->select("user_nick", 'u.user_id', 'COUNT(*) as cnt')
            ->from("admin_users", 'u')
            ->innerJoin('u', 'admin_user_sessionlog', 'l', 'l.user_id=u.user_id')
            ->groupBy('u.user_id')
            ->orderBy('u.user_nick')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AdminSessionCount($row), $data);
    }
}
