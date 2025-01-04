<?php

namespace EtoA\Fleet;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Fleet;
use EtoA\Entity\FleetShip;
use EtoA\Entity\Ship;
use EtoA\Entity\ShipListItem;

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

}
