<?php

declare(strict_types=1);

namespace EtoA\Message\Report;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\BattleReportData;

class BattleReportRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BattleReportData::class);
    }
}
