<?php

declare(strict_types=1);

namespace EtoA\Message\Report;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\OtherReportData;

class OtherReportRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OtherReportData::class);
    }
}
