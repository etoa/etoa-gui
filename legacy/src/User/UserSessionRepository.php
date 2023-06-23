<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserSessionRepository extends AbstractRepository
{
    /**
     * @return array<string, int>
     */
    public function logCountPerIp(UserSessionSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('s.ip_addr, COUNT(s.ip_addr) cnt')
            ->from('user_sessionlog', 's')
            ->groupBy('s.ip_addr')
            ->orderBy('s.cnt', 'DESC')
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return array<int, int>
     */
    public function countPerUserId(UserSessionSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('s.user_id, COUNT(s.user_id) cnt')
            ->from('user_session', 's')
            ->groupBy('s.user_id')
            ->orderBy('s.cnt', 'DESC')
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return array<int, int>
     */
    public function logCountPerUserId(UserSessionSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('s.user_id, COUNT(s.user_id) cnt')
            ->from('user_sessionlog', 's')
            ->groupBy('s.user_id')
            ->orderBy('s.cnt', 'DESC')
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_sessions')
            ->fetchOne();
    }

    /**
     * @return string[]
     */
    public function getUserSessionIds(): array
    {
        return $this->createQueryBuilder()
            ->select('id')
            ->from('user_sessions')
            ->fetchFirstColumn();
    }

    public function countActiveSessions(int $timeout): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_sessions')
            ->where('time_action > :timeout')
            ->setParameter('timeout', time() - $timeout)
            ->fetchOne();
    }

    public function find(string $id): ?UserSession
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? new UserSession($data) : null;
    }

    public function findLog(string $sessionId): ?UserSessionLog
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('user_sessionlog')
            ->where('session_id = :id')
            ->setParameter('id', $sessionId)
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
            ->fetchAssociative();

        return $data !== false ? new UserSession($data) : null;
    }

    /**
     * @return UserSession[]
     */
    public function getSessions(UserSessionSearch $search = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('*')
            ->from('user_sessions', 's')
            ->orderBy('s.time_action', 'DESC')
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
            ->executeQuery();
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
            ->executeQuery();
    }

    public function remove(string $id): void
    {
        $this->createQueryBuilder()
            ->delete('user_sessions')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('user_sessions')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
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
            ->executeQuery();
    }

    public function removeSessionLogs(int $timestamp): int
    {
        return $this->createQueryBuilder()
            ->delete('user_sessionlog')
            ->where('time_action < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->executeQuery()
            ->rowCount();
    }

    public function countLogs(UserSessionSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(*)')
            ->from('user_sessionlog', 's')
            ->fetchOne();
    }

    /**
     * @return UserSessionLog[]
     */
    public function getSessionLogs(UserSessionSearch $search, int $limit = null, int $offset = null): array
    {
        $rows = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit, $offset)
            ->select('*')
            ->from('user_sessionlog', 's')
            ->orderBy('s.time_action', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSessionLog($row), $rows);
    }

    /**
     * @return string[]
     */
    public function getLatestUserIps(): array
    {
        $data = $this->getConnection()->fetchAllAssociative('
            SELECT
                user_sessionlog.ip_addr AS log_ip,
                user_sessions.ip_addr
            FROM
                user_sessionlog
            INNER JOIN (
                SELECT
                    user_id,
                    MAX( time_action ) AS last_action
                FROM
                    user_sessionlog
                GROUP BY
                    user_id
            ) AS log
            ON
                user_sessionlog.user_id = log.user_id
                AND user_sessionlog.time_action = log.last_action
            LEFT JOIN
                user_sessions
            ON
                user_sessionlog.user_id = user_sessions.user_id
        ');

        $ips = [];
        foreach ($data as $row) {
            $ips[] = $row['ip_addr'] == null ? $row['log_ip'] : $row['ip_addr'];
        }

        return $ips;
    }
}
