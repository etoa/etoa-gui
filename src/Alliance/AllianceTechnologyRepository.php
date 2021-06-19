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

    public function existsInAlliance(int $allianceId, string $name): bool
    {
        $test = $this->getConnection()
            ->executeQuery(
                "SELECT alliance_techlist_id
		        FROM alliance_techlist
		        WHERE alliance_techlist_alliance_id = :alliance
			    AND alliance_techlist_tech_id = (
                    SELECT alliance_tech_id
                    FROM alliance_technologies
                    WHERE alliance_tech_name = :name
                )",
                [
                    'alliance' => $allianceId,
                    'name' => $name,
                ]
            )
            ->fetchAllAssociative();

        return count($test) > 0;
    }

    public function addToAlliance(int $allianceId, string $name, int $level, int $amount): void
    {
        $this->getConnection()
            ->executeStatement(
                "INSERT into alliance_techlist
                (
                    alliance_techlist_alliance_id,
                    alliance_techlist_tech_id,
                    alliance_techlist_current_level,
                    alliance_techlist_build_start_time,
                    alliance_techlist_build_end_time,
                    alliance_techlist_member_for
                ) VALUES (
                    :alliance,
                    (
                        SELECT alliance_tech_id
                        FROM alliance_technologies
                        WHERE alliance_tech_name = :name
                    ),
                    :level,
                    0,
                    1,
                    :amount
                )",
                [
                    'alliance' => $allianceId,
                    'name' => $name,
                    'level' => $level,
                    'amount' => $amount,
                ]
            );
    }

    public function updateForAlliance(int $allianceId, string $name, int $level, int $amount): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE alliance_techlist
                SET
                    alliance_techlist_current_level = :level,
                    alliance_techlist_member_for = :amount
                WHERE alliance_techlist_alliance_id = :alliance
                AND alliance_techlist_tech_id = (
                    SELECT alliance_tech_id
                    FROM alliance_technologies
                    WHERE alliance_tech_name = :name
                );",
                [
                    'level' => $level,
                    'amount' => $amount,
                    'alliance' => $allianceId,
                    'name' => $name,
                ]
            );
    }
}
