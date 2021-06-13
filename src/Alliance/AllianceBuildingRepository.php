<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceBuildingRepository extends AbstractRepository
{
    function findAll(): array
    {
        return $this->createQueryBuilder()
            ->select("*")
            ->from('alliance_buildings')
            ->execute()
            ->fetchAllAssociative();
    }

    function existsInAlliance(int $allianceId, string $name): bool
    {
        $test = $this->getConnection()
            ->executeQuery(
                "SELECT alliance_buildlist_id
		        FROM alliance_buildlist
		        WHERE alliance_buildlist_alliance_id = :alliance
			    AND alliance_buildlist_building_id = (
                    SELECT alliance_building_id
                    FROM alliance_buildings
                    WHERE alliance_building_name = :name
                )",
                [
                    'alliance' => $allianceId,
                    'name' => $name,
                ]
            )
            ->fetchAllAssociative();

        return count($test) > 0;
    }

    function addToAlliance(int $allianceId, string $name, int $level, int $amount): void
    {
        $this->getConnection()
            ->executeStatement(
                "INSERT into alliance_buildlist
                (
                    alliance_buildlist_alliance_id,
                    alliance_buildlist_building_id,
                    alliance_buildlist_current_level,
                    alliance_buildlist_build_start_time,
                    alliance_buildlist_build_end_time,
                    alliance_buildlist_cooldown,
                    alliance_buildlist_member_for
                ) VALUES (
                    :alliance,
                    (
                        SELECT alliance_building_id
                        FROM alliance_buildings
                        WHERE alliance_building_name = :name
                    ),
                    :level,
                    0,
                    1,
                    0,
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

    function updateForAlliance(int $allianceId, string $name, int $level, int $amount): void
    {
        $this->getConnection()
            ->executeStatement(
                "UPDATE alliance_buildlist
                SET
                    alliance_buildlist_current_level = :level,
                    alliance_buildlist_member_for = :amount
                WHERE alliance_buildlist_alliance_id = :alliance
                AND alliance_buildlist_building_id = (
                    SELECT alliance_building_id
                    FROM alliance_buildings
                    WHERE alliance_building_name = :name
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
