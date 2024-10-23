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
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->select('*')
            ->from('logs_debris')
            ->orderBy('time', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new DebrisLog($row), $data);
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
}
