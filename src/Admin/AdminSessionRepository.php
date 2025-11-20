<?php

declare(strict_types=1);

namespace EtoA\Admin;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AdminSession;
use EtoA\Entity\AdminSessionLog;
use EtoA\Entity\AdminUser;

class AdminSessionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminSession::class);
    }

    public function countActiveSessions(int $timeout): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(q)')
            ->where('q.timeAction > :timeout')
            ->setParameter('timeout', time() - $timeout)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return string[]
     */
    public function findByTimeout(int $timeout): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.timeAction + :timeout = ' . time())
            ->setParameter('timeout', $timeout)
            ->getQuery()
            ->execute();
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

    public function exists(string $id, int|AdminUser $user, string $userAgent): bool
    {
        return (bool) $this->findOneBy(['id'=>$id,'user'=>$user,'userAgent'=>$userAgent]);
    }

    public function create(string $id, AdminUser $user, string $ipAddr, string $userAgent, int $timeLogin): void
    {
        $session = new AdminSession();
        $session->setUser($user);
        $session->setId($id);
        $session->setIpAddr($ipAddr);
        $session->setUserAgent($userAgent);
        $session->setTimeLogin($timeLogin);

        $this->persist($session);
        $this->save();
    }

    public function update(string $id, int|AdminUser $user, int $time, string $ipAddress): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.timeAction', ':time')
            ->set('q.ipAddr', ':ip_addr')
            ->where('q.id = :id')
            ->andWhere('q.user = :user')
            ->setParameters([
                'id' => $id,
                'user' => $user,
                'time' => $time,
                'ip_addr' => $ipAddress,
            ])
            ->getQuery()
            ->execute();
    }

    public function removeByUserOrId(string $id, int|AdminUser $user): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.id = :id')
            ->orWhere('q.user = :user')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function countSessionLog(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select("COUNT(*)")
            ->from("admin_user_sessionlog")
            ->fetchOne();
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
