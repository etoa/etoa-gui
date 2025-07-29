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



    public function hasFleetsRelatedToEntity(Entity $entity): bool
    {
        $count = $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->where('q.entityTo = :entity')
            ->orWhere('q.entityFrom  = :entity')
            ->setParameter('entity', $entity)
            ->getQuery()
            ->getSingleScalarResult();

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
        $qry = $this->createQueryBuilder('q');

        if ($parameters->id !== null) {
            $qry->andWhere('q.id = :id')
                ->setParameter('id', $parameters->id);
        }

        if ($parameters->entityFrom !== null) {
            $qry->andWhere('q.entityFrom = :entityFrom')
                ->setParameter('entityFrom', $parameters->entityFrom);
        }

        if ($parameters->entityTo !== null) {
            $qry->andWhere('q.entityTo = :entityTo')
                ->setParameter('entityTo', $parameters->entityTo);
        }

        if ($parameters->user !== null) {
            $qry->andWhere('q.user = :user')
                ->setParameter('user', $parameters->user);
        }

        if ($parameters->action !== null) {
            $qry->andWhere('q.action = :action')
                ->setParameter('action', $parameters->action);
        }

        if ($parameters->userNick !== null) {
            $qry->leftJoin('App:User', 'u', 'WITH', 'q.user = u.id')
                ->andWhere('u.nick LIKE :userNick')
                ->setParameter('userNick', '%'.$parameters->userNick.'%');
        }

        return $qry->orderBy('q.landTime', 'ASC')
            ->getQuery()
            ->execute();
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

    public function update(Fleet $fleet, int $launchTime, int $landTime, Entity $entityFrom, Entity $entityTo, int $status, User $leader = null, BaseResources $resources = null, int $usageFuel = null, int $usageFood = null): void
    {
        $fleet->setLaunchTime($launchTime);
        $fleet->setLandTime($landTime);
        $fleet->setEntityFrom($entityFrom);
        $fleet->setEntityTo($entityTo);
        $fleet->setStatus($status);
        $fleet->setLeader($leader);

        if ($resources) {
            $fleet->setResMetal($resources->metal);
            $fleet->setResCrystal($resources->crystal);
            $fleet->setResPlastic($resources->plastic);
            $fleet->setResFuel($resources->fuel);
            $fleet->setResFood($resources->food);
            $fleet->setResPeople($resources->people);
        }

        if ($usageFuel) {
            $fleet->setUsageFuel($usageFuel);
        }

        if ($usageFood) {
            $fleet->setResFood($usageFood);
        }

        $this->save();
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

    public function promoteNewAllianceFleetLeader(Fleet $newLeader, Fleet $existingLeader, int $landTime): void
    {
        $newLeader->setStatus(FleetStatus::DEPARTURE->value);
        $newLeader->setLandTime($landTime);

        $existingLeader->setLeader($newLeader);

        $this->save();
    }

    public function removeSupportRes(Fleet $fleet): void
    {
        $fleet->setSupportUsageFood(0);
        $fleet->setSupportUsageFuel(0);

        $this->save();
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

        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort)->getQuery()->execute();
    }


}
