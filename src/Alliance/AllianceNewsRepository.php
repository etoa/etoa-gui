<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceNewsRepository extends AbstractRepository
{
    public function removeForAlliance(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_news')
            ->where('alliance_news_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }
}
