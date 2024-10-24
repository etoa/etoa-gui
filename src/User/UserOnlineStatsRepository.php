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
        $this->createQueryBuilder('q')
            ->insert('user_onlinestats')
            ->values([
                'stats_timestamp' => ':now',
                'stats_count' => ':sessionCount',
                'stats_regcount' => ':userCount',
            ])
            ->setParameters([
                'now' => time(),
                'sessionCount' => $sessionCount,
                'userCount' => $userCount,
            ])
            ->executeQuery();
    }

    /**
     * @return UserOnlineStats[]
     */
    public function getEntries(int $limit): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('user_onlinestats')
            ->setMaxResults($limit)
            ->orderBy('stats_timestamp', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserOnlineStats($row), $data);
    }
}
