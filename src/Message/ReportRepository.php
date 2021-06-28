<?php

declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\AbstractRepository;

class ReportRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('reports')
            ->execute()
            ->fetchOne();
    }

    public function countNotArchived(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('reports')
            ->where('archived = 0')
            ->execute()
            ->fetchOne();
    }

    public function countDeleted(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('reports')
            ->where('deleted = 1')
            ->execute()
            ->fetchOne();
    }
}
