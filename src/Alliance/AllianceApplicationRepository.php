<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceApplicationRepository extends AbstractRepository
{
    public function removeForAlliance(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }
}
