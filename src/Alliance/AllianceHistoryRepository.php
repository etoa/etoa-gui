<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceHistory;

class AllianceHistoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceHistory::class);
    }

    public function addEntry(Alliance $alliance, string $text): void
    {
        $entry = new AllianceHistory();
        $entry->setAlliance($alliance);
        $entry->setText($text);
        $entry->setTimestamp(time());

        $this->persist($entry);
        $this->save();
    }

    /**
     * @return array<AllianceHistory>
     */
    public function findForAlliance(Alliance $alliance, ?int $limit = null): array
    {
        return $this->findBy(['alliance'=>$alliance],['timestamp'=>'DESC'],$limit);
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
