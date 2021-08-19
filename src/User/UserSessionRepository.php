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

    /**
     * @return string[]
     */
    public function getUserSessionIds(): array
    {
        return array_column($this->createQueryBuilder()
            ->select('id')
            ->from('user_sessions')
            ->execute()
            ->fetchAllAssociative(), 'id');
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

    public function findLog(string $id): ?UserSessionLog
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('user_sessionlog')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new UserSessionLog($data) : null;
    }


    public function findByParameters(string $id, int $userId, string $userAgent, int $timeLogin): ?UserSession
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('user_sessions')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->andWhere('user_agent = :userAgent')
            ->andWhere('time_login = :timeLogin')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'userAgent' => $userAgent,
                'timeLogin' => $timeLogin,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new UserSession($data) : null;
    }

    /**
     * @return UserSession[]
     */
    public function getSessions(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('user_sessions')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSession($row), $data);
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

    public function add(string $id, int $userId, string $ipAddress, string $userAgent, int $timeLogin): void
    {
        $this->createQueryBuilder()
            ->insert('user_sessions')
            ->values([
                'id' => ':id',
                'user_id' => ':userId',
                'ip_addr' => ':ipAddress',
                'user_agent' => ':userAgent',
                'time_login' => ':timeLogin',

            ])
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'ipAddress' => $ipAddress,
                'userAgent' => $userAgent,
                'timeLogin' => $timeLogin,
            ])
            ->execute();
    }

    public function update(string $id, int $timeAction, int $botCount, int $lastSpan, string $ipAddress): void
    {
        $this->createQueryBuilder()
            ->update('user_sessions')
            ->set('time_action', ':timeAction')
            ->set('bot_count', ':botCount')
            ->set('last_span', ':lastSpan')
            ->set('ip_addr', ':ipAddress')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'timeAction' => $timeAction,
                'botCount' => $botCount,
                'lastSpan' => $lastSpan,
                'ipAddress' => $ipAddress,
            ])
            ->execute();
    }

    public function remove(string $id): void
    {
        $this->createQueryBuilder()
            ->delete('user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('user_sessions')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
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

    /**
     * @return UserSessionLog[]
     */
    public function getUserSessionLogs(int $userId, int $limit = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('user_sessionlog')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('time_action', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        $rows = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSessionLog($row), $rows);
    }
}
