<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\AbstractRepository;

class LogRepository extends AbstractRepository
{
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

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
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
