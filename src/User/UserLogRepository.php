<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;
use EtoA\Entity\UserLog;

class UserLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLog::class);
    }

    public function add(int $userId, string $zone, string $message, string $host, bool $public): void
    {

        $userLog = new UserLog();
        $userLog->setUserId($userId);
        $userLog->setTimestamp(time());
        $userLog->setZone($zone);
        $userLog->setMessage($message);
        $userLog->setHost($host);
        $userLog->setPublic($public);
        $this->getEntityManager()->persist($userLog);
        $this->save();
    }

    /**
     * @return UserLog[]
     */
    public function getUserLogs(User $user, int $limit, bool $public = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->orderBy('q.timestamp', 'DESC')
            ->setMaxResults($limit);

        if ($public !== null) {
            $qb
                ->andWhere('q.public = :public')
                ->setParameter('public', $public);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('q')
            ->delete('user_log')
            ->executeQuery();
    }
}
