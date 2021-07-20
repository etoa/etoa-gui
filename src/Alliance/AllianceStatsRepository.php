<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceStatsRepository extends AbstractRepository
{
    /**
     * @return AllianceStats[]
     */
    public function getStats(AllianceStatsSearch $search): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('alliance_stats');

        $data = $search->apply($qb)
            ->addOrderBy('alliance_name', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => AllianceStats::createFromDbRow($row), $data);
    }

    public function add(AllianceStats $stats): void
    {
        $this->createQueryBuilder()
            ->insert('alliance_stats')
            ->values([
                'alliance_id' => ':allianceId',
                'alliance_tag' => ':allianceTag',
                'alliance_name' => ':allianceName',
                'points' => ':points',
                'upoints' => ':userPoints',
                'apoints' => ':alliancePoints',
                'spoints' => ':shipPoints',
                'tpoints' => ':technologyPoints',
                'bpoints' => ':buildingPoints',
                'uavg' => ':userAverage',
                'cnt' => ':count',
                'alliance_rank_current' => ':currentRank',
                'alliance_rank_last' => ':lastRank',
            ])
            ->setParameters([
                'allianceId' => $stats->allianceId,
                'allianceTag' => $stats->allianceTag,
                'allianceName' => $stats->allianceName,
                'points' => $stats->points,
                'userPoints' => $stats->userPoints,
                'alliancePoints' => $stats->alliancePoints,
                'shipPoints' => $stats->shipPoints,
                'technologyPoints' => $stats->technologyPoints,
                'buildingPoints' => $stats->buildingPoints,
                'userAverage' => $stats->userAverage,
                'count' => $stats->count,
                'currentRank' => $stats->currentRank,
                'lastRank' => $stats->lastRank,
            ])->execute();
    }

    public function deleteAll(): void
    {
        $this->getConnection()->executeQuery('TRUNCATE TABLE alliance_stats');
    }
}
