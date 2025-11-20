<?php

namespace EtoA\User;

use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\UserSession;
use EtoA\Entity\UserSessionLog;

class UserSessionLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSessionLog::class);
    }

    public function addSessionLog(UserSession $userSession, ?int $logoutTime): void
    {
        $log = new UserSessionLog();
        $log->setSessionId($userSession->getId());
        $log->setUser($userSession->getUser());
        $log->setIpAddr($userSession->getIpAddr());
        $log->setUserAgent($userSession->getUserAgent());
        $log->setTimeLogin($userSession->getTimeLogin());
        $log->setTimeAction($userSession->getTimeAction());
        $log->setTimeLogout($logoutTime??time());

        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();
    }

    public function removeSessionLogs(int $timestamp): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.timeAction < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->getQuery()
            ->execute();
    }

    public function countLogs(UserSessionSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return UserSessionLog[]
     */
    public function getSessionLogs(UserSessionSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.timeAction', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return string[]
     * @throws Exception
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
