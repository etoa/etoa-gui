<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceTechnologyRepository extends AbstractRepository
{
    public function findAll(): array
    {
        return $this->createQueryBuilder()
            ->select("*")
            ->from('alliance_technologies')
            ->execute()
            ->fetchAllAssociative();
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

    public function addToAlliance(int $allianceId, int $technologyId, int $level, int $amount): void
    {
        $this->createQueryBuilder()
            ->insert('alliance_techlist')
            ->values([
                'alliance_techlist_alliance_id' => ':alliance',
                'alliance_techlist_tech_id' => ':technologyId',
                'alliance_techlist_current_level' => ':level',
                'alliance_techlist_build_start_time' => 0,
                'alliance_techlist_build_end_time' => 1,
                'alliance_techlist_member_for' => ' :amount',
            ])
            ->setParameters([
                'alliance' => $allianceId,
                'technologyId' => $technologyId,
                'level' => $level,
                'amount' => $amount,
            ])
            ->execute();
    }

    public function updateForAlliance(int $allianceId, int $technologyId, int $level, int $amount): void
    {
        $this->createQueryBuilder()
            ->update('alliance_techlist')
            ->set('alliance_techlist_current_level', ':level')
            ->set('alliance_techlist_member_for', ':amount')
            ->where('alliance_techlist_alliance_id = :alliance')
            ->andWhere('alliance_techlist_tech_id = :technologyId')
            ->setParameters([
                'level' => $level,
                'amount' => $amount,
                'alliance' => $allianceId,
                'technologyId' => $technologyId,
            ])
            ->execute();
    }
}
