<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\AbstractRepository;
use EtoA\Core\Database\AbstractSearch;
use EtoA\Core\Database\AbstractSort;
use EtoA\Ship\ShipListItem;
use EtoA\Universe\Resources\BaseResources;

class FleetRepository extends AbstractRepository
{
    /**
     * @return int[]
     */
    public function getUserIds(FleetSearch $search = null): array
    {
        return array_map(fn (array $row) => (int) $row['user_id'], $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select("DISTINCT user_id")
            ->from('fleet')
            ->fetchAllAssociative());
    }

    /**
     * @return int[]
     */
    public function getEntityToIds(FleetSearch $search = null): array
    {
        return array_map(fn (array $row) => (int) $row['entity_to'], $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select("DISTINCT entity_to")
            ->from('fleet')
            ->fetchAllAssociative());
    }

    public function countLeaderFleets(int $leaderId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select("COUNT(id)")
            ->from('fleet')
            ->where('leader_id = :leaderId')
            ->setParameter('leaderId', $leaderId)
            ->fetchOne();
    }

    public function countShipsInFleet(int $fleetId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('SUM(fs_ship_cnt)')
            ->from('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->setParameter('fleetId', $fleetId)
            ->fetchOne();
    }

    /**
     * @return array<int, int>
     */
    public function getFleetShipCounts(int $fleetId): array
    {
        return array_map(fn ($value) => (int) $value, $this->createQueryBuilder('q')
            ->select('fs_ship_id, fs_ship_cnt')
            ->from('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_cnt > 0')
            ->setParameter('fleetId', $fleetId)
            ->fetchAllKeyValue());
    }

    /**
     * @return array<int, int>
     */
    public function getLeaderShipCounts(int $leaderId): array
    {
        return array_map(fn ($value) => (int) $value, $this->createQueryBuilder('q')
            ->select('fs_ship_id, SUM(fs_ship_cnt)')
            ->from('fleet_ships')
            ->innerJoin('fleet_ships', 'fleet', 'fleet', 'fleet.id = fs_fleet_id')
            ->where('fleet.leader_id = :leaderId')
            ->andWhere('fs_ship_cnt > 0')
            ->groupBy('fs_ship_id')
            ->setParameter('leaderId', $leaderId)
            ->fetchAllKeyValue());
    }

    public function hasFleetsRelatedToEntity(int $entityId): bool
    {
        $count = (int) $this->createQueryBuilder('q')
            ->select('COUNT(id)')
            ->from('fleet')
            ->where('entity_to = :entityId')
            ->orWhere('entity_from  = :entityId')
            ->setParameter('entityId', $entityId)
            ->fetchOne();

        return $count > 0;
    }

    /**
     * @return array<int, int>
     */
    public function getUserFleetShipCounts(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('fs_ship_id, SUM(fs.fs_ship_cnt)')
            ->from('fleet', 'f')
            ->innerJoin('f', 'fleet_ships', 'fs', 'f.id = fs.fs_fleet_id')
            ->where('f.user_id = :userId')
            ->setParameter('userId', $userId, )
            ->groupBy('fs.fs_ship_id')
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return array<Fleet>
     */
    public function findByParameters(FleetSearchParameters $parameters): array
    {
        $qry = $this->createQueryBuilder('q')
            ->select('f.*')
            ->from('fleet', 'f');

        if ($parameters->id !== null) {
            $qry->andWhere('id = :id')
                ->setParameter('id', $parameters->id);
        }

        if ($parameters->entityFrom !== null) {
            $qry->andWhere('entity_from = :entityFrom')
                ->setParameter('entityFrom', $parameters->entityFrom);
        }

        if ($parameters->entityTo !== null) {
            $qry->andWhere('entity_to = :entityTo')
                ->setParameter('entityTo', $parameters->entityTo);
        }

        if ($parameters->userId !== null) {
            $qry->andWhere('user_id = :userId')
                ->setParameter('userId', $parameters->userId);
        }

        if ($parameters->action !== null) {
            $qry->andWhere('action = :action')
                ->setParameter('action', $parameters->action);
        }

        if ($parameters->userNick !== null) {
            $qry->leftJoin('f', 'users', 'u', 'f.user_id = u.user_id')
                ->andWhere('u.user_nick LIKE :userNick')
                ->setParameter('userNick', '%'.$parameters->userNick.'%');
        }

        $data = $qry->orderBy('landtime', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn ($arr) => new Fleet($arr), $data);
    }

    public function add(int $userId, int $launchTime, int $landTime, int $entityFrom, int $entityTo, string $action, int $status, BaseResources $resources, BaseResources $fetch = null, int $pilots = 0, int $fuelUsage = 0, int $foodUsage = 0, int $powerUsage = 0, int $leaderId = 0, int $nextId = 0, int $nextActionTime = 0, int $supportFuelUsage = 0, int $supportFoodUsage = 0): int
    {
        $fetch = $fetch !== null ? $fetch : new BaseResources();

        $this->createQueryBuilder('q')
            ->insert('fleet')
            ->values([
                'user_id' => ':userId',
                'launchtime' => ':launchTime',
                'landtime' => ':landTime',
                'entity_from' => ':entityFrom',
                'entity_to' => ':entityTo',
                'action' => ':action',
                'status' => ':status',
                'res_metal' => ':resMetal',
                'res_crystal' => ':resCrystal',
                'res_plastic' => ':resPlastic',
                'res_fuel' => ':resFuel',
                'res_food' => ':resFood',
                'res_people' => ':resPeople',
                'fetch_metal' => ':fetchMetal',
                'fetch_crystal' => ':fetchCrystal',
                'fetch_plastic' => ':fetchPlastic',
                'fetch_fuel' => ':fetchFuel',
                'fetch_food' => ':fetchFood',
                'fetch_people' => ':fetchPeople',
                'pilots' => ':pilots',
                'usage_fuel' => ':usageFuel',
                'usage_food' => ':usageFood',
                'usage_power' => ':usagePower',
                'support_usage_food' => ':supportUsageFood',
                'support_usage_fuel' => ':supportUsageFuel',
                'leader_id' => ':leaderId',
                'next_id' => ':nextId',
                'nextactiontime' => ':nextActionTime',
            ])
            ->setParameters([
                'userId' => $userId,
                'launchTime' => $launchTime,
                'landTime' => $landTime,
                'entityFrom' => $entityFrom,
                'entityTo' => $entityTo,
                'action' => $action,
                'status' => $status,
                'resMetal' => $resources->metal,
                'resCrystal' => $resources->crystal,
                'resPlastic' => $resources->plastic,
                'resFuel' => $resources->fuel,
                'resFood' => $resources->food,
                'resPeople' => $resources->people,
                'fetchMetal' => $fetch->metal,
                'fetchCrystal' => $fetch->crystal,
                'fetchPlastic' => $fetch->plastic,
                'fetchFuel' => $fetch->fuel,
                'fetchFood' => $fetch->food,
                'fetchPeople' => $fetch->people,
                'pilots' => $pilots,
                'usageFuel' => $fuelUsage,
                'usageFood' => $foodUsage,
                'usagePower' => $powerUsage,
                'supportUsageFood' => $supportFoodUsage,
                'supportUsageFuel' => $supportFuelUsage,
                'leaderId' => $leaderId,
                'nextId' => $nextId,
                'nextActionTime' => $nextActionTime,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(int $id, int $launchTime, int $landTime, int $entityFrom, int $entityTo, int $status, int $leaderId = 0, BaseResources $resources = null, int $usageFuel = null, int $usageFood = null): bool
    {
        $qb = $this->createQueryBuilder('q')
            ->update('fleet')
            ->set('launchtime', ':launchTime')
            ->set('landtime', ':landTime')
            ->set('entity_from', ':entityFrom')
            ->set('entity_to', ':entityTo')
            ->set('status', ':status')
            ->set('leader_id', ':leaderId')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'launchTime' => $launchTime,
                'landTime' => $landTime,
                'entityFrom' => $entityFrom,
                'entityTo' => $entityTo,
                'status' => $status,
                'leaderId' => $leaderId,
            ]);

        if ($resources !== null) {
            $qb
                ->set('res_metal', ':resMetal')
                ->set('res_crystal', ':resCrystal')
                ->set('res_plastic', ':resPlastic')
                ->set('res_fuel', ':resFuel')
                ->set('res_food', ':resFood')
                ->set('res_people', ':resPeople')
                ->setParameter('resMetal', $resources->metal)
                ->setParameter('resCrystal', $resources->crystal)
                ->setParameter('resPlastic', $resources->plastic)
                ->setParameter('resFuel', $resources->fuel)
                ->setParameter('resFood', $resources->food)
                ->setParameter('resPeople', $resources->people);
        }

        if ($usageFuel !== null) {
            $qb
                ->set('usage_fuel', ':usageFuel')
                ->setParameter('usageFuel', $usageFuel);
        }

        if ($usageFood !== null) {
            $qb
                ->set('usage_food', ':usageFood')
                ->setParameter('usageFood', $usageFood);
        }

        return (bool) $qb
            ->executeQuery()
            ->rowCount();
    }

    public function markAsLeader(int $id, int $allianceId): void
    {
        $this->createQueryBuilder('q')
            ->update('fleet')
            ->set('leader_id', ':id')
            ->set('next_id', ':allianceId')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'allianceId' => $allianceId,
            ])
            ->executeQuery();
    }

    public function promoteNewAllianceFleetLeader(int $newLeader, int $existingLeader, int $landTime): void
    {
        $this->createQueryBuilder('q')
            ->update('fleet')
            ->set('status', ':status')
            ->set('landtime', ':landTime')
            ->where('id = :id')
            ->setParameters([
                'status' => FleetStatus::DEPARTURE,
                'landTime' => $landTime,
                'id' => $newLeader,
            ])
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->update('fleet')
            ->set('leader_id', ':newLeaderId')
            ->where('leader_id = :existingLeaderId')
            ->setParameters([
                'existingLeaderId' => $existingLeader,
                'newLeaderId' => $newLeader,
            ])
            ->executeQuery();
    }

    public function removeSupportRes(int $fleetId): void
    {
        $this->createQueryBuilder('q')
            ->update('fleet')
            ->set('support_usage_fuel', '0')
            ->set('support_usage_food', '0')
            ->where('id = :id')
            ->setParameters([
                'id' => $fleetId,
            ])
            ->executeQuery();
    }

    public function save(Fleet $fleet): void
    {
        $this->createQueryBuilder('q')
            ->update('fleet')
            ->set('user_id', ':userId')
            ->set('launchtime', ':launchTime')
            ->set('landtime', ':landTime')
            ->set('entity_from', ':entityFrom')
            ->set('entity_to', ':entityTo')
            ->set('action', ':action')
            ->set('status', ':status')
            ->set('pilots', ':pilots')
            ->set('usage_fuel', ':usageFuel')
            ->set('usage_food', ':usageFood')
            ->set('usage_power', ':usagePower')
            ->set('res_metal', ':resMetal')
            ->set('res_crystal', ':resCrystal')
            ->set('res_plastic', ':resPlastic')
            ->set('res_fuel', ':resFuel')
            ->set('res_food', ':resFood')
            ->set('res_power', ':resPower')
            ->set('res_people', ':resPeople')
            ->set('fetch_metal', ':fetchMetal')
            ->set('fetch_crystal', ':fetchCrystal')
            ->set('fetch_plastic', ':fetchPlastic')
            ->set('fetch_fuel', ':fetchFuel')
            ->set('fetch_food', ':fetchFood')
            ->set('fetch_power', ':fetchPower')
            ->set('fetch_people', ':fetchPeople')
            ->where('id = :id')
            ->setParameters([
                'id' => $fleet->id,
                'userId' => $fleet->userId,
                'launchTime' => $fleet->launchTime,
                'landTime' => $fleet->landTime,
                'entityFrom' => $fleet->entityFrom,
                'entityTo' => $fleet->entityTo,
                'action' => $fleet->action,
                'status' => $fleet->status,
                'pilots' => $fleet->pilots,
                'usageFuel' => $fleet->usageFuel,
                'usageFood' => $fleet->usageFood,
                'usagePower' => $fleet->usagePower,
                'resMetal' => $fleet->resMetal,
                'resCrystal' => $fleet->resCrystal,
                'resPlastic' => $fleet->resPlastic,
                'resFuel' => $fleet->resFuel,
                'resFood' => $fleet->resFood,
                'resPower' => $fleet->resPower,
                'resPeople' => $fleet->resPeople,
                'fetchMetal' => $fleet->fetchMetal,
                'fetchCrystal' => $fleet->fetchCrystal,
                'fetchPlastic' => $fleet->fetchPlastic,
                'fetchFuel' => $fleet->fetchFuel,
                'fetchFood' => $fleet->fetchFood,
                'fetchPower' => $fleet->fetchPower,
                'fetchPeople' => $fleet->fetchPeople,
            ])
            ->executeQuery();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('fleet')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    /**
     * @return array<FleetShip>
     */
    public function findAllShipsInFleet(int $fleetId, ?bool $faked = false): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_faked = :faked')
            ->setParameters([
                'fleetId' => $fleetId,
                'faked' => $faked,
            ])
            ->fetchAllAssociative();

        return array_map(fn ($arr) => new FleetShip($arr), $data);
    }

    /**
     * @return array<FleetShip>
     */
    public function findAllShipsForLeader(int $leaderId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('fs.*')
            ->from('fleet_ships', 'fs')
            ->innerJoin('fs', 'fleet', 'f', 'f.id = fs.fleet_id')
            ->where('f.leader_id = :leaderId')
            ->setParameters([
                'leaderId' => $leaderId,
            ])
            ->fetchAllAssociative();

        return array_map(fn ($arr) => new FleetShip($arr), $data);
    }

    public function findShipsInFleet(int $fleetId, int $shipId): ?FleetShip
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_id = :shipId')
            ->setParameters([
                'fleetId' => $fleetId,
                'shipId' => $shipId,
            ])
            ->fetchAssociative();

        return $data !== false ? new FleetShip($data) : null;
    }

    public function addSpecialShipsToFleet(int $fleetId, int $shipId, int $count, ShipListItem $item): void
    {
        $this->createQueryBuilder('q')
            ->insert('fleet_ships')
            ->values([
                'fs_fleet_id' => ':fleetId',
                'fs_ship_id' => ':shipId',
                'fs_ship_cnt' => ':count',
                'fs_special_ship' => '1',
                'fs_special_ship_level' => ':level',
                'fs_special_ship_exp' => ':exp',
                'fs_special_ship_bonus_weapon' => ':bonusWeapon',
                'fs_special_ship_bonus_structure' => ':bonusStructure',
                'fs_special_ship_bonus_shield' => ':bonusShield',
                'fs_special_ship_bonus_heal' => ':bonusHeal',
                'fs_special_ship_bonus_capacity' => ':bonusCapacity',
                'fs_special_ship_bonus_speed' => ':bonusSpeed',
                'fs_special_ship_bonus_readiness' => ':bonusReadiness',
                'fs_special_ship_bonus_pilots' => ':bonusPilots',
                'fs_special_ship_bonus_tarn' => ':bonusTarn',
                'fs_special_ship_bonus_antrax' => ':bonusAntrax',
                'fs_special_ship_bonus_forsteal' => ':bonusForsteal',
                'fs_special_ship_bonus_build_destroy' => ':bonusBuildDestroy',
                'fs_special_ship_bonus_antrax_food' => ':bonusAntraxFood',
                'fs_special_ship_bonus_deactivade' => ':bonusDeactivade',
            ])
            ->setParameters([
                'fleetId' => $fleetId,
                'shipId' => $shipId,
                'count' => $count,
                'level' => $item->specialShipLevel,
                'exp' => $item->specialShipExp,
                'bonusWeapon' => $item->specialShipBonusWeapon,
                'bonusStructure' => $item->specialShipBonusStructure,
                'bonusShield' => $item->specialShipBonusShield,
                'bonusHeal' => $item->specialShipBonusHeal,
                'bonusCapacity' => $item->specialShipBonusCapacity,
                'bonusSpeed' => $item->specialShipBonusSpeed,
                'bonusReadiness' => $item->specialShipBonusReadiness,
                'bonusPilots' => $item->specialShipBonusPilots,
                'bonusTarn' => $item->specialShipBonusTarn,
                'bonusAntrax' => $item->specialShipBonusAnthrax,
                'bonusForsteal' => $item->specialShipBonusForSteal,
                'bonusBuildDestroy' => $item->specialShipBonusBuildDestroy,
                'bonusAntraxFood' => $item->specialShipBonusAnthraxFood,
                'bonusDeactivade' => $item->specialShipBonusDeactivate,
            ])
            ->executeQuery();
    }

    public function addShipsToFleet(int $fleetId, int $shipId, int $count, int $fakeId = 0): void
    {
        $entry = $this->findShipsInFleet($fleetId, $shipId);
        if ($entry !== null) {
            $this->createQueryBuilder('q')
                ->update('fleet_ships')
                ->set('fs_ship_cnt', 'fs_ship_cnt + :count')
                ->where('fs_fleet_id = :fleetId')
                ->andWhere('fs_ship_id = :shipId')
                ->setParameters([
                    'fleetId' => $fleetId,
                    'shipId' => $shipId,
                    'count' => $count,
                ])
                ->executeQuery();

            return;
        }

        $this->createQueryBuilder('q')
            ->insert('fleet_ships')
            ->values([
                'fs_fleet_id' => ':fleetId',
                'fs_ship_id' => ':shipId',
                'fs_ship_cnt' => ':count',
                'fs_ship_faked' => ':fakeId',
            ])
            ->setParameters([
                'fleetId' => $fleetId,
                'shipId' => $shipId,
                'count' => $count,
                'fakeId' => $fakeId,
            ])
            ->executeQuery();
    }

    public function updateShipsInFleet(int $fleetId, int $shipId, int $count): void
    {
        $this->createQueryBuilder('q')
            ->update('fleet_ships')
            ->set('fs_ship_cnt', ':count')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_id = :shipId')
            ->setParameters([
                'fleetId' => $fleetId,
                'shipId' => $shipId,
                'count' => $count,
            ])
            ->executeQuery();
    }

    public function removeShipsFromFleet(int $fleetId, int $shipId): void
    {
        $this->createQueryBuilder('q')
            ->delete('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_id = :shipId')
            ->setParameters([
                'fleetId' => $fleetId,
                'shipId' => $shipId,
            ])
            ->executeQuery();
    }

    public function removeAllShipsFromFleet(int $fleetId): void
    {
        $this->createQueryBuilder('q')
            ->delete('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->setParameter('fleetId', $fleetId)
            ->executeQuery();
    }

    public function getSpecialShipExperienceSumForUser(int $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('SUM(fs_special_ship_exp)')
            ->from('fleet_ships', 'fs')
            ->innerJoin('fs', 'fleet', 'f', 'f.id = fs.fs_fleet_id AND f.user_id = :userId')
            ->andWhere('fs_ship_cnt = 1')
            ->setParameter('userId', $userId)
            ->fetchOne();
    }

    public function getGlobalResources(): BaseResources
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'SUM(res_metal) as metal',
                'SUM(res_crystal) as crystal',
                'SUM(res_plastic) as plastic',
                'SUM(res_fuel) as fuel',
                'SUM(res_food) as food'
            )
            ->from('fleet')
            ->fetchAssociative();

        $res = new BaseResources();
        $res->metal = (int) $data['metal'];
        $res->crystal = (int) $data['crystal'];
        $res->plastic = (int) $data['plastic'];
        $res->fuel = (int) $data['fuel'];
        $res->food = (int) $data['food'];

        return $res;
    }

    public function exists(FleetSearch $search): bool
    {
        return (bool) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('1')
            ->from('fleet')
            ->setMaxResults(1)
            ->fetchOne();
    }

    /**
     * @return Fleet[]
     */
    public function search(FleetSearch $search, FleetSort $sort = null): array
    {
        $sort = $sort !== null ? $sort : FleetSort::landtime('DESC');

        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort)
            ->select('fleet.*')
            ->from('fleet')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Fleet($row), $data);
    }

    public function getFleetSpecialTarnBonus(int $fleetId): float
    {
        $data = $this->createQueryBuilder('q')
            ->select('s.special_ship_bonus_tarn, fs.fs_special_ship_bonus_tarn')
            ->from('fleet_ships', 'fs')
            ->innerJoin('fs', 'ships', 's', 's.ship_id = fs.fs_ship_id')
            ->where('fs.fs_fleet_id = :fleetId')
            ->andWhere('s.special_ship = 1')
            ->setParameter('fleetId', $fleetId)
            ->fetchAllAssociative();

        $value = 0;
        foreach ($data as $row) {
            $value += (int) $row['fs_special_ship_bonus_tarn'] * (float) $row['special_ship_bonus_tarn'];
        }

        return $value;
    }
}
