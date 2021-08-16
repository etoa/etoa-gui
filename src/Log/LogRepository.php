<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\AbstractRepository;

class LogRepository extends AbstractRepository
{
    /**
     * @return Log[]
     */
    public function searchLogs(LogSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit, $offset)
            ->select('l.*')
            ->from('logs', 'l')
            ->orderBy('timestamp', 'DESC')
            ->execute()
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
            'ip' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''),
            'message' => $message,
        ]);
    }

    public function count(LogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(id)')
            ->from('logs')
            ->execute()
            ->fetchOne();
    }

    public function cleanup(int $threshold): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('logs')
            ->where('timestamp < :threshold')
            ->setParameter('threshold', $threshold)
            ->execute();
    }
}
