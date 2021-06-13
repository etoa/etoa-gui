<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Core\AbstractRepository;

class AdminSessionRepository extends AbstractRepository
{
    public function find($id): ?array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('admin_user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();
        return $data ? $data : null;
    }

    public function findByTimeout(int $timeout): array
    {
        return $this->createQueryBuilder()
            ->select("id")
            ->from('admin_user_sessions')
            ->where('time_action + :timeout = ' . time())
            ->setParameter('timeout', $timeout)
            ->execute()
            ->fetchAllAssociative();
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

    public function update($id, int $time, string $ipAddress): void
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

    public function remove($id): void
    {
        $this->createQueryBuilder()
            ->delete('admin_user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function removeByUserOrId($id, int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('admin_user_sessions')
            ->where('id = :id')
            ->orWhere('user_id = :user_id')
            ->setParameter('id', $id)
            ->setParameter('user_id', $userId)
            ->execute();
    }

    public function addSessionLog(array $data, bool $logoutPressed): void
    {
        $this->createQueryBuilder()
            ->insert('admin_user_sessionlog')
            ->values([
                'session_id' => ':id',
                'user_id' => ':user_id',
                'ip_addr' => ':ip_addr',
                'user_agent' => ':user_agent',
                'time_login' => ':time_login',
                'time_action' => ':time_action',
                'time_logout' => $logoutPressed ? time() : 0,
            ])
            ->setParameters([
                'id' => $data['id'],
                'user_id' => $data['user_id'],
                'ip_addr' => $data['ip_addr'],
                'user_agent' => $data['user_agent'],
                'time_login' => $data['time_login'],
                'time_action' => $data['time_action'],
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
}
