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

    public function findForAlliance(int $allianceId): array
    {
        return $this->createQueryBuilder()
            ->select('*')
            ->from('alliance_history')
            ->where('history_alliance_id = :allianceId')
            ->orderBy('history_timestamp', 'DESC')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchAllAssociative();
    }

}
