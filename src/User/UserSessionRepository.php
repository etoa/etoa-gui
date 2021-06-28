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

    public function find(string $id): ?array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? $data : null;
    }

    public function findByTimeout(int $timeout): array
    {
        return $this->createQueryBuilder()
            ->select("id")
            ->from('user_sessions')
            ->where('time_action + :timeout = ' . time())
            ->setParameter('timeout', $timeout)
            ->execute()
            ->fetchAllAssociative();
    }

    public function remove(string $id): void
    {
        $this->createQueryBuilder()
            ->delete('user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function addSessionLog(array $userSession, ?int $logoutTime): void
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
                'id' => $userSession['id'],
                'user_id' => $userSession['user_id'],
                'ip_addr' => $userSession['ip_addr'],
                'user_agent' => $userSession['user_agent'],
                'time_login' => $userSession['time_login'],
                'time_action' => $userSession['time_action'],
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
}
