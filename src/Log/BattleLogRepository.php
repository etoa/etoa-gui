<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\AbstractRepository;

class BattleLogRepository extends AbstractRepository
{
    public function cleanup(int $threshold): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('logs_battle')
            ->where('timestamp < :threshold')
            ->setParameter('threshold', $threshold)
            ->execute();
    }
}
