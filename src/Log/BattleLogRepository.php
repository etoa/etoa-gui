<?php declare(strict_types=1);

namespace EtoA\Log;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\BattleLog;

class BattleLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BattleLog::class);
    }

    /**
     * @return BattleLog[]
     */
    public function searchLogs(BattleLogSearch $search): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->orderBy('q.timestamp', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function cleanup(int $threshold): int
    {
        return $this->createQueryBuilder('q')
            ->delete('logs_battle')
            ->where('timestamp < :threshold')
            ->setParameter('threshold', $threshold)
            ->executeQuery()
            ->rowCount();
    }
}
