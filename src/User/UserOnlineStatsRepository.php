<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\UserOnlineStats;

class UserOnlineStatsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserOnlineStats::class);
    }

    public function addEntry(int $userCount, int $sessionCount): void
    {
        $stats = new UserOnlineStats();
        $stats->setTimestamp(time());
        $stats->setSessionCount($sessionCount);
        $stats->setUserCount($userCount);

        $this->persist($stats);
        $this->save();
    }

    /**
     * @return UserOnlineStats[]
     */
    public function getEntries(int $limit): array
    {
        return $this->createQueryBuilder('q')
            ->setMaxResults($limit)
            ->orderBy('q.timestamp', 'DESC')
            ->getQuery()
            ->execute();
    }
}
