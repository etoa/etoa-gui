<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceBuildingRepository extends AbstractRepository
{
    /**
     * @return AllianceBuilding[]
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('alliance_buildings')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceBuilding($row), $data);
    }

    public function existsInAlliance(int $allianceId, int $buildingId): bool
    {
        $test = $this->createQueryBuilder()
            ->select('alliance_buildlist_id')
            ->from('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return count($test) > 0;
    }

    public function getLevel(int $allianceId, int $buildingId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('alliance_buildlist_current_level')
            ->from('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function getCooldown(int $allianceId, int $buildingId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('alliance_buildlist_cooldown')
            ->from('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :allianceId')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'allianceId' => $allianceId,
                'buildingId' => $buildingId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function setCooldown(int $allianceId, int $buildingId, int $cooldownEnd): void
    {
        $this->createQueryBuilder()
            ->update('alliance_buildlist')
            ->set('alliance_buildlist_cooldown', ':cooldownEnd')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
                'cooldownEnd' => $cooldownEnd,
            ])
            ->execute();
    }

    public function getUserCooldown(int $userId, int $buildingId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('cooldown_end')
            ->from('alliance_building_cooldown')
            ->where('cooldown_user_id = :userId')
            ->andWhere('cooldown_alliance_building_id = :buildingId')
            ->setParameters([
                'userId' => $userId,
                'buildingId' => $buildingId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function setUserCooldown(int $userId, int $buildingId, int $cooldownEnd): void
    {
        $this->getConnection()->executeStatement(
            "REPLACE INTO
                alliance_building_cooldown
            (
                cooldown_user_id,
                cooldown_alliance_building_id,
                cooldown_end
            ) VALUES (
                :userId,
                :buildingId,
                :cooldownEnd
            );",
            [
                'userId' => $userId,
                'buildingId' => $buildingId,
                'cooldownEnd' => $cooldownEnd,
            ]
        );
    }

    public function addToAlliance(int $allianceId, int $buildingId, int $level, int $amount): void
    {
        $this->createQueryBuilder()
            ->insert('alliance_buildlist')
            ->values([
                'alliance_buildlist_alliance_id' => ':alliance',
                'alliance_buildlist_building_id' => ':buildingId',
                'alliance_buildlist_current_level' => ':level',
                'alliance_buildlist_build_start_time' => 0,
                'alliance_buildlist_build_end_time' => 1,
                'alliance_buildlist_cooldown' => 0,
                'alliance_buildlist_member_for' => ' :amount',
            ])
            ->setParameters([
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
                'level' => $level,
                'amount' => $amount,
            ])
            ->execute();
    }

    public function updateForAlliance(int $allianceId, int $buildingId, int $level, int $amount): void
    {
        $this->createQueryBuilder()
            ->update('alliance_buildlist')
            ->set('alliance_buildlist_current_level', ':level')
            ->set('alliance_buildlist_member_for', ':amount')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'level' => $level,
                'amount' => $amount,
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
            ])
            ->execute();
    }
}
