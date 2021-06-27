<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceHistoryRepository extends AbstractRepository
{
    public function addEntry(int $allianceId, string $text): void
    {
        $this->getConnection()->executeQuery('INSERT INTO alliance_history (
            history_alliance_id,
            history_text,
            history_timestamp
        ) VALUES (
            :allianceId,
            :text,
            :timestamp
        )', [
            'allianceId' => $allianceId,
            'text' => $text,
            'timestamp' => time(),
        ]);
    }
}
