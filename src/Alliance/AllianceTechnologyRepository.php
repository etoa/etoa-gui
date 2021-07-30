<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceTechnologyRepository extends AbstractRepository
{
    /**
     * @return array<int, string>
     */
    public function getNames(bool $orderById = false): array
    {
        return $this->fetchIdsWithNames('alliance_technologies', 'alliance_tech_id', 'alliance_tech_name', $orderById);
    }

    /**
     * @return AllianceTechnology[]
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('alliance_technologies')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceTechnology($row), $data);
    }

    public function existsInAlliance(int $allianceId, int $technologyId): bool
    {
        $test = $this->createQueryBuilder()
            ->select('alliance_techlist_id')
            ->from('alliance_techlist')
            ->where('alliance_techlist_alliance_id = :alliance')
            ->andWhere('alliance_techlist_tech_id = :technologyId')
            ->setParameters([
                'alliance' => $allianceId,
                'technologyId' => $technologyId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return count($test) > 0;
    }

    public function getLevel(int $allianceId, int $technologyId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('alliance_techlist_current_level')
            ->from('alliance_techlist')
            ->where('alliance_techlist_alliance_id = :alliance')
            ->andWhere('alliance_techlist_tech_id = :technologyId')
            ->setParameters([
                'alliance' => $allianceId,
                'technologyId' => $technologyId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function addToAlliance(int $allianceId, int $technologyId, int $level, int $amount, int $startTime = 0, int $endTime = 0): void
    {
        $this->createQueryBuilder()
            ->insert('alliance_techlist')
            ->values([
                'alliance_techlist_alliance_id' => ':alliance',
                'alliance_techlist_tech_id' => ':technologyId',
                'alliance_techlist_current_level' => ':level',
                'alliance_techlist_build_start_time' => ':startTime',
                'alliance_techlist_build_end_time' => ':endTime',
                'alliance_techlist_member_for' => ' :amount',
            ])
            ->setParameters([
                'alliance' => $allianceId,
                'technologyId' => $technologyId,
                'level' => $level,
                'amount' => $amount,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ])
            ->execute();
    }

    public function updateForAlliance(int $allianceId, int $technologyId, int $level, int $amount, int $startTime = 0, int $endTime = 0): void
    {
        $this->createQueryBuilder()
            ->update('alliance_techlist')
            ->set('alliance_techlist_current_level', ':level')
            ->set('alliance_techlist_member_for', ':amount')
            ->set('alliance_techlist_build_start_time', ':startTime')
            ->set('alliance_techlist_build_end_time', ':endTime')
            ->where('alliance_techlist_alliance_id = :alliance')
            ->andWhere('alliance_techlist_tech_id = :technologyId')
            ->setParameters([
                'level' => $level,
                'amount' => $amount,
                'alliance' => $allianceId,
                'technologyId' => $technologyId,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ])
            ->execute();
    }
}
