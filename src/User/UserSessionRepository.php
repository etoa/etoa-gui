<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserSessionRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_sessions')
            ->execute()
            ->fetchOne();
    }

    public function countActiveSessions(int $timeout): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_sessions')
            ->where('time_action > :timeout')
            ->setParameter('timeout', time() - $timeout)
            ->execute()
            ->fetchOne();
    }

    public function find(string $id): ?UserSession
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new UserSession($data) : null;
    }

    /**
     * @return UserSession[]
     */
    public function getActiveUserSessions(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('user_sessions')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('time_action', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSession($row), $data);
    }

    /**
     * @return UserSession[]
     */
    public function findByTimeout(int $timeout): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('user_sessions')
            ->where('time_action + :timeout = ' . time())
            ->setParameter('timeout', $timeout)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSession($row), $data);
    }

    public function remove(string $id): void
    {
        $this->createQueryBuilder()
            ->delete('user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function addSessionLog(UserSession $userSession, ?int $logoutTime): void
    {
        $this->createQueryBuilder()
            ->insert('user_sessionlog')
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
                'id' => $userSession->id,
                'user_id' => $userSession->userId,
                'ip_addr' => $userSession->ipAddr,
                'user_agent' => $userSession->userAgent,
                'time_login' => $userSession->timeLogin,
                'time_action' => $userSession->timeAction,
            ])
            ->execute();
    }

    public function removeSessionLogs(int $timestamp): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('user_sessionlog')
            ->where('time_action < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->execute();
    }

    public function countLogs(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_sessionlog')
            ->execute()
            ->fetchOne();
    }
}
