<?php declare(strict_types=1);

namespace EtoA\Log;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Log;

class LogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * @return Log[]
     */
    public function searchLogs(LogSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->select('*')
            ->from('logs')
            ->orderBy('timestamp', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Log($row), $data);
    }

    public function add(int $facility, int $severity, string $message): void
    {
        $this->getConnection()->executeQuery('INSERT DELAYED INTO logs (
				facility,
				severity,
				timestamp,
				ip,
				message
			) VALUES (
				:facility,
				:severity,
				:timestamp,
				:ip,
				:message
			)', [
            'facility' => $facility,
            'severity' => $severity,
            'timestamp' => time(),
            'ip' => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
            'message' => $message,
        ]);
    }

    public function cleanup(int $threshold): int
    {
        return $this->createQueryBuilder('q')
            ->delete('logs')
            ->where('timestamp < :threshold')
            ->setParameter('threshold', $threshold)
            ->executeQuery()
            ->rowCount();
    }
}
