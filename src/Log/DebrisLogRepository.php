<?php declare(strict_types=1);

namespace EtoA\Log;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\DebrisLog;
use EtoA\Entity\User;

class DebrisLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DebrisLog::class);
    }

    /**
     * @return DebrisLog[]
     */
    public function searchLogs(DebrisLogSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.time', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function add(int $adminId, int $userId, int $metal, int $crystal, int $plastic): void
    {
        $this->createQueryBuilder('q')
            ->insert('logs_debris')
            ->values([
                'time' => ':now',
                'admin_id' => ':adminId',
                'user_id' => ':userId',
                'metal' => ':metal',
                'crystal' => ':crystal',
                'plastic' => ':plastic',
            ])
            ->setParameters([
                'now' => time(),
                'adminId' => $adminId,
                'userId' => $userId,
                'metal' => $metal,
                'crystal' => $crystal,
                'plastic' => $plastic,
            ])
            ->executeQuery();
    }

    public function countBySearch(DebrisLogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
