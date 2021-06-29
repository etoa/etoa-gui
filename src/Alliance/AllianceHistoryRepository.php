<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceHistoryRepository extends AbstractRepository
{
    public function addEntry(int $allianceId, string $text): void
    {
        $this->createQueryBuilder()
            ->insert('alliance_history')
            ->values([
                'history_alliance_id' => ':allianceId',
                'history_text' => ':text',
                'history_timestamp' => ':timestamp',
            ])
            ->setParameters([
                'allianceId' => $allianceId,
                'text' => $text,
                'timestamp' => time(),
            ])
            ->execute();
    }
}
