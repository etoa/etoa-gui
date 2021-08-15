<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Core\AbstractRepository;
use EtoA\Universe\Resources\BaseResources;

class FleetRepository extends AbstractRepository
{
    public function count(FleetSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select("COUNT(id)")
            ->from('fleet')
            ->execute()
            ->fetchOne();
    }

    public function countLeaderFleets(int $leaderId): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('fleet')
            ->where('leader_id = :leaderId')
            ->setParameter('leaderId', $leaderId)
            ->execute()
            ->fetchOne();
    }

    public function countShipsInFleet(int $fleetId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('SUM(fs_ship_cnt)')
            ->from('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->setParameter('fleetId', $fleetId)
            ->execute()
            ->fetchOne();
    }
    public function hasFleetsRelatedToEntity(int $entityId): bool
    {
        $count = (int) $this->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('fleet')
            ->where('entity_to = :entityId')
            ->orWhere('entity_from  = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute()
            ->fetchOne();

        return $count > 0;
    }

    public function find(int $id): ?Fleet
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('fleet')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Fleet($data) : null;
    }

    /**
     * @return array<int, int>
     */
    public function getUserFleetShipCounts(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('fs_ship_id, SUM(fs.fs_ship_cnt)')
            ->from('fleet', 'f')
            ->innerJoin('f', 'fleet_ships', 'fs', 'f.id = fs.fs_fleet_id')
            ->where('f.user_id = :userId')
            ->setParameter('userId', $userId, )
            ->groupBy('fs.fs_ship_id')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return array<Fleet>
     */
    public function findByParameters(FleetSearchParameters $parameters): array
    {
        $qry = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($arr) => new Fleet($arr), $data);
    }

    public function add(int $userId, int $launchTime, int $landTime, int $entityFrom, int $entityTo, string $action, int $status, BaseResources $resources): int
    {
        $this->createQueryBuilder()
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
            ])
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(int $id, int $launchTime, int $landTime, int $entityFrom, int $entityTo, int $status): void
    {
        $this->createQueryBuilder()
            ->update('fleet')
            ->set('launchtime', ':launchTime')
            ->set('landtime', ':landTime')
            ->set('entity_from', ':entityFrom')
            ->set('entity_to', ':entityTo')
            ->set('status', ':status')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'launchTime' => $launchTime,
                'landTime' => $landTime,
                'entityFrom' => $entityFrom,
                'entityTo' => $entityTo,
                'status' => $status,
            ])
            ->execute();
    }

    public function save(Fleet $fleet): void
    {
        $this->createQueryBuilder()
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
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('fleet')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    /**
     * @return array<FleetShip>
     */
    public function findAllShipsInFleet(int $fleetId, bool $faked = false): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_faked = :faked')
            ->setParameters([
                'fleetId' => $fleetId,
                'faked' => $faked,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($arr) => new FleetShip($arr), $data);
    }

    public function findShipsInFleet(int $fleetId, int $shipId): ?FleetShip
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_id = :shipId')
            ->setParameters([
                'fleetId' => $fleetId,
                'shipId' => $shipId,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new FleetShip($data) : null;
    }

    public function addShipsToFleet(int $fleetId, int $shipId, int $count, int $fakeId = 0): void
    {
        $entry = $this->findShipsInFleet($fleetId, $shipId);
        if ($entry !== null) {
            $this->createQueryBuilder()
                ->update('fleet_ships')
                ->set('fs_ship_cnt', 'fs_ship_cnt + :count')
                ->where('fs_fleet_id = :fleetId')
                ->andWhere('fs_ship_id = :shipId')
                ->setParameters([
                    'fleetId' => $fleetId,
                    'shipId' => $shipId,
                    'count' => $count,
                ])
                ->execute();

            return;
        }

        $this->createQueryBuilder()
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
            ->execute();
    }

    public function updateShipsInFleet(int $fleetId, int $shipId, int $count): void
    {
        $this->createQueryBuilder()
            ->update('fleet_ships')
            ->set('fs_ship_cnt', ':count')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_id = :shipId')
            ->setParameters([
                'fleetId' => $fleetId,
                'shipId' => $shipId,
                'count' => $count,
            ])
            ->execute();
    }

    public function removeShipsFromFleet(int $fleetId, int $shipId): void
    {
        $this->createQueryBuilder()
            ->delete('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->andWhere('fs_ship_id = :shipId')
            ->setParameters([
                'fleetId' => $fleetId,
                'shipId' => $shipId,
            ])
            ->execute();
    }

    public function removeAllShipsFromFleet(int $fleetId): void
    {
        $this->createQueryBuilder()
            ->delete('fleet_ships')
            ->where('fs_fleet_id = :fleetId')
            ->setParameter('fleetId', $fleetId)
            ->execute();
    }

    public function getSpecialShipExperienceSumForUser(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('SUM(fs_special_ship_exp)')
            ->from('fleet_ships', 'fs')
            ->innerJoin('fs', 'fleet', 'f', 'f.id = fs.fs_fleet_id AND f.user_id = :userId')
            ->andWhere('fs_ship_cnt = 1')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchOne();
    }

    public function getGlobalResources(): BaseResources
    {
        $data = $this->createQueryBuilder()
            ->select(
                'SUM(res_metal) as metal',
                'SUM(res_crystal) as crystal',
                'SUM(res_plastic) as plastic',
                'SUM(res_fuel) as fuel',
                'SUM(res_food) as food'
            )
            ->from('fleet')
            ->execute()
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
        $qb = $this->applySearchSortLimit($this->createQueryBuilder(), $search);

        if (isset($search->parameters['planetUserId'])) {
            $qb->innerJoin('fleet', 'planets', 'planets', 'fleet.entity_to = planets.id');
        }

        return (bool) $qb
            ->select('1')
            ->from('fleet')
            ->setMaxResults(1)
            ->execute()
            ->fetchOne();
    }
}
