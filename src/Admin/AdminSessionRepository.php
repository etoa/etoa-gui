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
     * @return AdminSession[]
     */
    public function findByTimeout(int $timeout): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.timeAction + :timeout < :now')
            ->setParameter('timeout', $timeout)
            ->setParameter('now', time())
            ->getQuery()
            ->execute();
    }

    /**
     * The session row of an admin, whatever session id it currently carries.
     */
    public function findForUser(int|AdminUser $user): ?AdminSession
    {
        return $this->findOneBy(['user' => $user]);
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

}
