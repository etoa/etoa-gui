<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;

class AllianceBuildingRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceBuilding::class);
    }

    /**
     * @return array<int, string>
     */
    public function getNames(bool $orderById = false): array
    {
        return $this->fetchIdsWithNames('alliance_buildings', 'alliance_building_id', 'alliance_building_name', $orderById);
    }

    /**
     * @return AllianceBuilding[]
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('alliance_buildings')
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $building = new AllianceBuilding($row);
            $result[$building->id] = $building;
        }

        return $result;
    }

    public function existsInAlliance(int $allianceId, int $buildingId): bool
    {
        $test = $this->createQueryBuilder('q')
            ->select('alliance_buildlist_id')
            ->from('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
            ])
            ->fetchAllAssociative();

        return count($test) > 0;
    }

    public function getLevel(int $allianceId, int $buildingId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('alliance_buildlist_current_level')
            ->from('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
            ])
            ->fetchOne();
    }

    /**
     * @return array<int, int>
     */
    public function getLevels(int $allianceId): array
    {
        return $this->createQueryBuilder('q')
            ->select('alliance_buildlist_building_id, alliance_buildlist_current_level')
            ->from('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_current_level > 0')
            ->setParameters([
                'alliance' => $allianceId,
            ])
            ->fetchAllKeyValue();
    }

    public function getCooldown(int $allianceId, int $buildingId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('alliance_buildlist_cooldown')
            ->from('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :allianceId')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'allianceId' => $allianceId,
                'buildingId' => $buildingId,
            ])
            ->fetchOne();
    }

    public function setCooldown(int $allianceId, int $buildingId, int $cooldownEnd): void
    {
        $this->createQueryBuilder('q')
            ->update('alliance_buildlist')
            ->set('alliance_buildlist_cooldown', ':cooldownEnd')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
                'cooldownEnd' => $cooldownEnd,
            ])
            ->executeQuery();
    }

    public function getUserCooldown(int $userId, int $buildingId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('cooldown_end')
            ->from('alliance_building_cooldown')
            ->where('cooldown_user_id = :userId')
            ->andWhere('cooldown_alliance_building_id = :buildingId')
            ->setParameters([
                'userId' => $userId,
                'buildingId' => $buildingId,
            ])
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

    /**
     * @return AllianceBuildListItem[]
     */
    public function getBuildList(int $allianceId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $entry = AllianceBuildListItem::createFromData($row);
            $result[$entry->buildingId] = $entry;
        }

        return $result;
    }

    /**
     * @return ?array{name: string, endTime: int}
     */
    public function getInProgress(int $allianceId): ?array
    {
        $data = $this->createQueryBuilder('q')
            ->select('alliance_building_name, alliance_buildlist_build_end_time')
            ->from('alliance_buildlist')
            ->innerJoin('alliance_buildlist', 'alliance_buildings', 'alliance_buildings', 'alliance_building_id=alliance_buildlist_building_id')
            ->where('alliance_buildlist_alliance_id = :allianceId')
            ->andWhere('alliance_buildlist_build_end_time > 0')
            ->setParameter('allianceId', $allianceId)
            ->fetchAssociative();

        return $data !== false ? ['name' => $data['alliance_building_name'], 'endTime' => (int) $data['alliance_buildlist_build_end_time']] : null;
    }

    public function addToAlliance(int $allianceId, int $buildingId, int $level, int $amount, int $startTime = 0, int $endTime = 0): void
    {
        $this->createQueryBuilder('q')
            ->insert('alliance_buildlist')
            ->values([
                'alliance_buildlist_alliance_id' => ':alliance',
                'alliance_buildlist_building_id' => ':buildingId',
                'alliance_buildlist_current_level' => ':level',
                'alliance_buildlist_build_start_time' => ':startTime',
                'alliance_buildlist_build_end_time' => ':endTime',
                'alliance_buildlist_cooldown' => 0,
                'alliance_buildlist_member_for' => ' :amount',
            ])
            ->setParameters([
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
                'level' => $level,
                'amount' => $amount,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ])
            ->executeQuery();
    }

    public function updateMembersForAlliance(int $allianceId, int $amount): void
    {
        $this->createQueryBuilder('q')
            ->update('alliance_buildlist')
            ->set('alliance_buildlist_member_for', ':amount')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_member_for < :amount')
            ->setParameters([
                'amount' => $amount,
                'alliance' => $allianceId,
            ])
            ->executeQuery();
    }

    public function updateForAlliance(int $allianceId, int $buildingId, int $level, int $amount, int $startTime = 0, int $endTime = 0): void
    {
        $this->createQueryBuilder('q')
            ->update('alliance_buildlist')
            ->set('alliance_buildlist_current_level', ':level')
            ->set('alliance_buildlist_member_for', ':amount')
            ->set('alliance_buildlist_build_start_time', ':startTime')
            ->set('alliance_buildlist_build_end_time', ':endTime')
            ->where('alliance_buildlist_alliance_id = :alliance')
            ->andWhere('alliance_buildlist_building_id = :buildingId')
            ->setParameters([
                'level' => $level,
                'amount' => $amount,
                'alliance' => $allianceId,
                'buildingId' => $buildingId,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ])
            ->executeQuery();
    }

    public function removeForAlliance(int $allianceId): void
    {
        $this->createQueryBuilder('q')
            ->delete('alliance_buildlist')
            ->where('alliance_buildlist_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->executeQuery();
    }


    /**
     * @return array<int, int>
     */
    public function getShipyardLevelsWhereNonNegativeResources(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('alliance_id, alliance_buildlist_current_level')
            ->addSelect('alliance_buildlist_current_level')
            ->from('alliances')
            ->innerJoin('alliances', 'alliance_buildlist', 'alliance_buildlist', 'alliance_id = alliance_buildlist_alliance_id')
            ->where('alliance_buildlist_building_id = :buildingId')
            ->andWhere('alliance_res_metal >= 0')
            ->andWhere('alliance_res_crystal >= 0')
            ->andWhere('alliance_res_plastic >= 0')
            ->andWhere('alliance_res_fuel >= 0')
            ->andWhere('alliance_res_food >= 0')
            ->andWhere('alliance_buildlist_current_level > 0')
            ->setParameter('buildingId', AllianceBuildingId::SHIPYARD)
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }
}
