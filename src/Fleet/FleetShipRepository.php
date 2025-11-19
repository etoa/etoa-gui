<?php

namespace EtoA\Fleet;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Fleet;
use EtoA\Entity\FleetShip;
use EtoA\Entity\Ship;
use EtoA\Entity\ShipListItem;
use EtoA\Entity\User;

/**
 * @extends ServiceEntityRepository<FleetShip>
 *
 * @method FleetShip|null find($id, $lockMode = null, $lockVersion = null)
 * @method FleetShip|null findOneBy(array $criteria, array $orderBy = null)
 * @method FleetShip[]    findAll()
 * @method FleetShip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FleetShipRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FleetShip::class);
    }

    public function addShipsToFleet(Fleet $fleet, Ship $ship, int $count, int $fakeId = 0): void
    {
        $entry = $this->findShipsInFleet($fleet, $ship);
        if ($entry) {
            $this->createQueryBuilder('q')
                ->update()
                ->set('q.count', 'q.count + :count')
                ->where('q.fleet = :fleet')
                ->andWhere('q.ship = :ship')
                ->setParameters([
                    'fleet' => $fleet,
                    'ship' => $ship,
                    'count' => $count,
                ])
                ->getQuery()
                ->execute();

            return;
        }

        $fleetShip = new FleetShip();
        $fleetShip->setFleet($fleet);
        $fleetShip->setShip($ship);
        $fleetShip->setCount($count);
        $fleetShip->setShipFaked($fakeId);

        $this->persist($fleetShip);
        $this->save();
    }

    /**
     * @return array<FleetShip>
     */
    public function findAllShipsInFleet(Fleet $fleet, int $faked = 0): array
    {
        return $this->findBy(['fleet'=>$fleet,'shipFaked'=>$faked]);
    }

    public function findShipsInFleet(Fleet $fleet, Ship $ship): ?FleetShip
    {
        return $this->findOneBy(['fleet'=>$fleet,'ship'=>$ship]);
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

    public function removeShipsFromFleet(Fleet $fleet, Ship $ship): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.fleet = :fleet')
            ->andWhere('q.ship = :ship')
            ->setParameters([
                'fleet' => $fleet,
                'ship' => $ship,
            ])
            ->getQuery()
            ->execute();
    }

    public function removeAllShipsFromFleet(Fleet $fleet): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.fleet = :fleet')
            ->setParameter('fleet', $fleet)
            ->getQuery()
            ->execute();
    }

    public function getSpecialShipExperienceSumForUser(int|User $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('SUM(q.specialShipExperience)')
            ->innerJoin('App:Fleet', 'f', 'with', 'f.id = q.fleet AND f.user = :userId')
            ->andWhere('q.count = 1')
            ->setParameter('userId', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFleetSpecialTarnBonus(Fleet $fleet): float
    {
        $data = $this->createQueryBuilder('q')
            ->select('s.specialBonusTarn, q.specialShipBonusTarn')
            ->innerJoin('App:Ship', 's', 'WITH', 's.id = q.ship')
            ->where('q.fleet = :fleet')
            ->andWhere('s.special = 1')
            ->setParameter('fleet', $fleet)
            ->getQuery()
            ->execute();

        $value = 0;
        foreach ($data as $row) {
            $value += $row->getSpecialShipBonusTarn() * $row->getSpecialBonusTarn();
        }

        return $value;
    }

    /**
     * @return array<FleetShip>
     */
    public function findAllShipsForLeader(User $leader): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.fleet.leader = :leader')
            ->setParameters([
                'leader' => $leader,
            ])
            ->getQuery()
            ->execute();
    }

    public function countShipsInFleet(Fleet $fleet): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('SUM(q.count)')
            ->where('q.fleet = :fleet')
            ->setParameter('fleet', $fleet)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
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

}
