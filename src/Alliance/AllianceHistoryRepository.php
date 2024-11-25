<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceHistory;

class AllianceHistoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceHistory::class);
    }

    public function addEntry(int $allianceId, string $text): int
    {
        $this->createQueryBuilder('q')
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
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @return array<AllianceHistory>
     */
    public function findForAlliance(int $allianceId, ?int $limit = null): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.alliance = :allianceId')
            ->orderBy('q.timestamp', 'DESC')
            ->setParameter('allianceId', $allianceId)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function removeForAlliance(int $allianceId): void
    {
        $this->createQueryBuilder('q')
            ->delete('alliance_history')
            ->where('history_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->executeQuery();
    }
}
