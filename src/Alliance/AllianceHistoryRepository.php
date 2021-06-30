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

    /**
     * @return array<AllianceHistoryEntry>
     */
    public function findForAlliance(int $allianceId, ?int $limit = null): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('alliance_history')
            ->where('history_alliance_id = :allianceId')
            ->orderBy('history_timestamp', 'DESC')
            ->setParameter('allianceId', $allianceId)
            ->setMaxResults($limit)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceHistoryEntry($row), $data);
    }

    public function removeForAlliance(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_history')
            ->where('history_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }
}
