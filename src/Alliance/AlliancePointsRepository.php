<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AlliancePoints;

class AlliancePointsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlliancePoints::class);
    }

    /**
     * @return AlliancePoints[]
     */
    public function getPoints(Alliance $alliance, int $limit, int $start = null, int $end = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.alliance = :alliance')
            ->andWhere('q.points > 0')
            ->setParameter('alliance', $alliance)
            ->orderBy('q.timestamp', 'DESC')
            ->setMaxResults($limit);

        if ($start > 0) {
            $qb
                ->andWhere('q.timestamp > :start')
                ->setParameter('start', $start);
        }

        if ($end > 0) {
            $qb
                ->andWhere('q.timestamp < :end')
                ->setParameter('end', $end);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function add(AllianceStats $stats): void
    {
        $this->createQueryBuilder('q')
            ->insert('alliance_stats')
            ->values([
                'alliance_id' => ':allianceId',
                'alliance_tag' => ':allianceTag',
                'alliance_name' => ':allianceName',
                'timestamp' => ':time',
                'points' => ':points',
                'upoints' => ':userPoints',
                'apoints' => ':alliancePoints',
                'bpoints' => ':buildingPoints',
                'spoints' => ':shipPoints',
                'tpoints' => ':technologyPoints',
                'uavg' => ':avg',
                'cnt' => ':count',
                'alliance_rank_current' => ':currentRank',
                'alliance_rank_last' => ':lastRank',
            ])
            ->setParameters([
                'allianceId' => $stats->allianceId,
                'allianceTag' => $stats->allianceTag,
                'allianceName' => $stats->allianceName,
                'time' => time(),
                'points' => $stats->points,
                'userPoints' => $stats->userPoints,
                'alliancePoints' => $stats->alliancePoints,
                'buildingPoints' => $stats->buildingPoints,
                'technologyPoints' => $stats->technologyPoints,
                'shipPoints' => $stats->shipPoints,
                'avg' => $stats->userAverage,
                'count' => $stats->count,
                'currentRank' => $stats->currentRank,
                'lastRank' => $stats->lastRank,
            ])->executeQuery();
    }

    public function removeForAlliance(Alliance $alliance): void
    {
        $entries = $this->findBy(['alliance'=>$alliance]);

        foreach ($entries as $entry) {
            $this->remove($entry);
        }

        $this->save();
    }
}
