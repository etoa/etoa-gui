<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\Fleet;
use EtoA\Entity\User;
use EtoA\Universe\Resources\BaseResources;

class FleetRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fleet::class);
    }

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

    public function countFleet(FleetSearch $search = null): int
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select("COUNT(q.id)")
            ->getQuery()
            ->getSingleScalarResult();
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
    public function getUserFleetShipCounts(User $user): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('IDENTITY(fs.ship), SUM(fs.count)')
            ->innerJoin('App:FleetShip', 'fs', 'WITH', 'q.id = fs.fleet')
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->groupBy('fs.ship')
            ->getQuery()
            ->execute();

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

    public function add(User $user, int $launchTime, int $landTime, Entity $entityFrom, Entity $entityTo, string $action, int $status, BaseResources $resources, BaseResources $fetch = null, int $pilots = 0, int $fuelUsage = 0, int $foodUsage = 0, int $powerUsage = 0, User $leader = null, int $nextId = 0, int $nextActionTime = 0, int $supportFuelUsage = 0, int $supportFoodUsage = 0): Fleet
    {
        $fetch = $fetch !== null ? $fetch : new BaseResources();

        $fleet = new Fleet();
        $fleet->setUser($user);
        $fleet->setLaunchTime($launchTime);
        $fleet->setLandTime($landTime);
        $fleet->setEntityFrom($entityFrom);
        $fleet->setEntityTo($entityTo);
        $fleet->setAction($action);
        $fleet->setStatus($status);
        $fleet->setLandTime($landTime);
        $fleet->setResMetal($resources->metal);
        $fleet->setResCrystal($resources->crystal);
        $fleet->setResPlastic($resources->plastic);
        $fleet->setResFuel($resources->fuel);
        $fleet->setResFood($resources->food);
        $fleet->setResPeople($resources->people);
        $fleet->setFetchMetal($fetch->metal);
        $fleet->setFetchCrystal($fetch->crystal);
        $fleet->setFetchPlastic($fetch->plastic);
        $fleet->setFetchFuel($fetch->fuel);
        $fleet->setFetchFood($fetch->food);
        $fleet->setFetchPeople($fetch->people);
        $fleet->setPilots($pilots);
        $fleet->setUsageFood($foodUsage);
        $fleet->setUsagePower($powerUsage);
        $fleet->setUsageFuel($fuelUsage);
        $fleet->setSupportUsageFuel($supportFuelUsage);
        $fleet->setSupportUsageFood($supportFoodUsage);
        $fleet->setLeader($leader);
        $fleet->setNextId($nextId);
        $fleet->setNextActionTime($nextActionTime);

        $this->persist($fleet);
        $this->save();

        return $fleet;

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
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
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


}
