<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AlliancePollRepository extends AbstractRepository
{
    public function removeForAlliance(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_polls')
            ->where('poll_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('alliance_poll_votes')
            ->where('vote_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }
}
