<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\AbstractRepository;

class LogRepository extends AbstractRepository
{
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
