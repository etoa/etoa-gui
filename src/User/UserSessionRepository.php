<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;
use EtoA\Entity\UserSession;
use EtoA\Entity\UserSessionLog;

class UserSessionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry,private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, UserSession::class);
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
            ->select('COUNT(q)')
            ->where('q.timeAction > :timeout')
            ->setParameter('timeout', time() - $timeout)
            ->getQuery()
            ->getSingleScalarResult();
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
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->orderBy('q.timeAction', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return UserSession[]
     */
    public function getActiveUserSessions(int|User $userId): array
    {
        return $this->findBy(['user'=>$userId],['timeAction'=>'DESC']);
    }

    /**
     * @return UserSession[]
     */
    public function findByTimeout(int $timeout): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.timeAction + :timeout = ' . time())
            ->setParameter('timeout', $timeout)
            ->getQuery()
            ->execute();
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



    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->where('q.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }


}
