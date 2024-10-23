<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\UserSession;

class UserSessionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry,private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, UserSession::class);
    }

    /**
     * @return array<string, int>
     */
    public function logCountPerIp(UserSessionSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
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
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
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
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('s.user_id, COUNT(s.user_id) cnt')
            ->from('user_sessionlog', 's')
            ->groupBy('s.user_id')
            ->orderBy('s.cnt', 'DESC')
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return string[]
     */
    public function getUserSessionIds(): array
    {
        return $this->createQueryBuilder('q')
            ->select('id')
            ->from('user_sessions')
            ->fetchFirstColumn();
    }

    public function countActiveSessions(int $timeout): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(*)')
            ->from('user_sessions')
            ->where('time_action > :timeout')
            ->setParameter('timeout', time() - $timeout)
            ->fetchOne();
    }

    public function findLog(string $sessionId): ?UserSessionLog
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->where('session_id = :id')
            ->setParameter('id', $sessionId)
            ->fetchAssociative();

        return $data !== false ? new UserSessionLog($data) : null;
    }


    public function findByParameters(string $id, int $userId, string $userAgent, int $timeLogin): ?UserSession
    {
        $data = $this->createQueryBuilder('q')
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
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
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
        $data = $this->createQueryBuilder('q')
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
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('user_sessions')
            ->where('time_action + :timeout = ' . time())
            ->setParameter('timeout', $timeout)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserSession($row), $data);
    }

    public function add(UserSession $userSession): void
    {
        $this->entityManager->persist($userSession);
        $this->entityManager->flush();
    }

    public function update(string $id, int $timeAction, int $botCount, int $lastSpan, string $ipAddress): void
    {
        $this->createQueryBuilder('q')
            ->set('q.time_action', $timeAction)
            ->set('q.bot_count',  $botCount)
            ->set('q.last_span',  $lastSpan)
            ->set('q.ip_addr',  $ipAddress)
            ->where('q.id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->getQuery()
            ->execute();
    }

    public function remove(UserSession $userSession): void
    {
        $this->entityManager->remove($userSession);
        $this->entityManager->flush();
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->where('q.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }

    public function addSessionLog(UserSession $userSession, ?int $logoutTime): void
    {
        $this->createQueryBuilder('q')
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
        return $this->createQueryBuilder('q')
            ->delete('user_sessionlog')
            ->where('time_action < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->executeQuery()
            ->rowCount();
    }

    public function countLogs(UserSessionSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(*)')
            ->from('user_sessionlog', 's')
            ->fetchOne();
    }

    /**
     * @return UserSessionLog[]
     */
    public function getSessionLogs(UserSessionSearch $search, int $limit = null, int $offset = null): array
    {
        $rows = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
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
