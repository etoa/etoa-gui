<?php

namespace EtoA\Ship;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\Planet;
use EtoA\Entity\Ship;
use EtoA\Entity\ShipListItem;
use EtoA\Entity\User;

/**
 * @extends ServiceEntityRepository<ShipListItem>
 *
 * @method ShipListItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipListItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipListItem[]    findAll()
 * @method ShipListItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipListRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipListItem::class);
    }

    public function getNumberOfShips(int $shipId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(shiplist_id)')
            ->from('shiplist')
            ->where('shiplist_ship_id = :shipId')
            ->setParameter('shipId', $shipId)
            ->fetchOne();
    }

    /**
     * @return array<int, ShipListItem>
     */
    public function getRecyclable (User $user, Planet $entity): array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('App:Ship', 's', 'WITH', 'q.ship = s.id')
            ->where('q.user = :user')
            ->andWhere('q.entity = :entity')
            ->andWhere('q.count > 0')
            ->andWhere('s.special = 0')
            ->setParameters([
                'user' => $user,
                'entity' => $entity,
            ])
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, ShipListItem>
     */
    public function getEntityShipCounts(User $user, Planet $entity): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.entity = :entity')
            ->andWhere('q.count > 0')
            ->setParameters([
                'user' => $user,
                'entity' => $entity,
            ])
            ->getQuery()
            ->execute();
    }

    /**
     * @param ?int[] $shipIds
     * @return ShipListItem[]
     */
    public function findForUser(int|User $user, int|Planet $entity = null, array $ships = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user = :user')
            ->andWhere('q.count > 0 OR q.bunkered > 0')
            ->setParameter('user', $user);

        if ($entity) {
            $qb
                ->andWhere('q.entity = :entity')
                ->setParameter('entity', $entity);
        }

        if ($ships !== null) {
            $qb
                ->andWhere('q.ship IN (:ships)')
                ->setParameter('ships', $ships);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    /**
     * @return ShipListItem[]
     */
    public function search(ShipListSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->getQuery()
            ->execute();
    }


    /**
     * @return array<int, ShipListItemCount>
     */
    public function getUserShipCounts(User $user): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('IDENTITY(q.ship) as shiplist_ship_id, SUM(q.count) as count, SUM(q.bunkered) as bunkered, SUM(q.specialShipExp) as shiplist_special_ship_exp')
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->groupBy('q.ship')
            ->getQuery()
            ->execute();

        $result = [];
        foreach ($data as $row) {
            $count = new ShipListItemCount($row);
            $result[$count->shipId] = $count;
        }

        return $result;
    }

    public function addShip(Ship $ship, int $amount, User $user, Planet $entity): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot add negative ship count');
        }

        $this->addShipCount($ship, $amount, $user, $entity);
    }

    public function removeShips(ShipListItem $shipListItem, int $amount): int
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot remove negative ship count');
        }
        $amount = min($shipListItem->getCount(), $amount);

        $shipListItem->setCount($shipListItem->getCount()-$amount);
        $this->save();

        return $amount;
    }

    public function hasShipsOnEntity(Entity $entity): bool
    {
        $count = $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->where('q.entity = :entity')
            ->andWhere('q.count  > 0')
            ->setParameter('entity', $entity)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function removeForEntity(Planet $entity): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.entity = :entity')
            ->setParameter('entity', $entity)
            ->getQuery()
            ->execute();
    }


    public function saveItem(ShipListItem $item): void
    {
        $this->createQueryBuilder('q')
            ->update('shiplist')
            ->set('shiplist_user_id', ':userId')
            ->set('shiplist_ship_id', ':shipId')
            ->set('shiplist_entity_id', ':entityId')
            ->set('shiplist_bot_id', ':botId')
            ->set('shiplist_count', ':count')
            ->set('shiplist_bunkered', ':bunkered')
            ->set('shiplist_special_ship', ':special')
            ->set('shiplist_special_ship_level', ':specialLevel')
            ->set('shiplist_special_ship_exp', ':specialExp')
            ->set('shiplist_special_ship_bonus_weapon', ':specialWeapon')
            ->set('shiplist_special_ship_bonus_structure', ':specialStructure')
            ->set('shiplist_special_ship_bonus_shield', ':specialShield')
            ->set('shiplist_special_ship_bonus_heal', ':specialHeal')
            ->set('shiplist_special_ship_bonus_capacity', ':specialCapacity')
            ->set('shiplist_special_ship_bonus_speed', ':specialSpeed')
            ->set('shiplist_special_ship_bonus_pilots', ':specialPilots')
            ->set('shiplist_special_ship_bonus_tarn', ':specialTarn')
            ->set('shiplist_special_ship_bonus_antrax', ':specialAntrax')
            ->set('shiplist_special_ship_bonus_forsteal', ':specialForsteal')
            ->set('shiplist_special_ship_bonus_build_destroy', ':specialDestroy')
            ->set('shiplist_special_ship_bonus_antrax_food', ':specialAntraxFood')
            ->set('shiplist_special_ship_bonus_deactivade', ':specialDeactivate')
            ->set('shiplist_special_ship_bonus_readiness', ':specialReadiness')
            ->where('shiplist_id = :id')
            ->setParameters([
                'id' => $item->id,
                'userId' => $item->userId,
                'shipId' => $item->shipId,
                'entityId' => $item->entityId,
                'botId' => $item->botId,
                'count' => $item->count,
                'bunkered' => $item->bunkered,
                'special' => (int) $item->specialShip,
                'specialLevel' => $item->specialShipLevel,
                'specialExp' => $item->specialShipExp,
                'specialWeapon' => $item->specialShipBonusWeapon,
                'specialStructure' => $item->specialShipBonusStructure,
                'specialShield' => $item->specialShipBonusShield,
                'specialHeal' => $item->specialShipBonusHeal,
                'specialCapacity' => $item->specialShipBonusCapacity,
                'specialSpeed' => $item->specialShipBonusSpeed,
                'specialPilots' => $item->specialShipBonusPilots,
                'specialTarn' => $item->specialShipBonusTarn,
                'specialAntrax' => $item->specialShipBonusAnthrax,
                'specialForsteal' => $item->specialShipBonusForSteal,
                'specialDestroy' => $item->specialShipBonusBuildDestroy,
                'specialAntraxFood' => $item->specialShipBonusAnthraxFood,
                'specialDeactivate' => $item->specialShipBonusDeactivate,
                'specialReadiness' => $item->specialShipBonusReadiness,
            ])
            ->executeQuery();
    }

    public function bunker(ShipListItem $shipListItem, int $count): int
    {

        $delable = max(0, min($shipListItem->getCount(), $count));

        $shipListItem->setBunkered($shipListItem->getBunkered()+$delable);
        $shipListItem->setCount($shipListItem->getBunkered()-$delable);

        $this->save();

        return $delable;
    }

    /**
     * @return array<int, ShipListItem>
     */
    public function getBunkered(User $user, Planet $entity): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.entity = :entity')
            ->andWhere('q.user = :user')
            ->andWhere('q.bunkered  > 0')
            ->setParameters([
                'entity' => $entity,
                'user' => $user,
            ])
            ->getQuery()
            ->execute();
    }

    public function leaveBunker(ShipListItem $shipListItem): int
    {
        $org = $this->getOriginal($shipListItem);

        $delable = max(0, min($shipListItem->getBunkered(), $org['bunkered']));

        $shipListItem->setBunkered($org['bunkered']-$delable);
        $shipListItem->setCount($org['count']+$delable);

        $this->save();

        return $delable;
    }

    public function countEmpty(): int
    {
        return $this->count(['count'=>0,'bunkered'=>0,'specialShip'=>0]);
    }

    public function getSpecialShipExperienceSumForUser(int|User $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('SUM(q.specialShipExp)')
            ->where('q.user = :userId')
            ->andWhere('q.count = 1')
            ->setParameter('userId', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function cleanUp(): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.count = 0')
            ->andWhere('q.bunkered = 0')
            ->andWhere('q.specialShip = 0')
            ->getQuery()
            ->execute();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('shiplist')
            ->where('shiplist_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    private function addShipCount(Ship $ship, int $amount, User $user, Planet $entity): void
    {
        $item = $this->findOneBy(['user'=>$user,'ship'=>$ship,'entity'=>$entity]);

        if(!$item) {
            $item = new ShipListItem();
            $this->persist($item);
        }

        $item->setCount($item->getCount()+max(0, $amount));

        $item->setUser($user);
        $item->setEntity($entity);
        $item->setShip($ship);

        $this->save();
    }

    /**
     * @return array<int, array{name: string, cnt: int, max: int}>
     */
    public function getOverallCount(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select( 'SUM(q.count+q.bunkered) as cnt')
            ->addSelect('s.name as name')
            ->addSelect('MAX(q.count+q.bunkered) as max')
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->innerJoin('App:Ship', 's', 'WITH', 's.id = q.ship')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->andWhere('s.special = 0')
            ->groupBy('s.id')
            ->orderBy('cnt', 'DESC')
            ->getQuery()
            ->execute();

        return array_map(fn ($arr) => [
            'name' => $arr['name'],
            'cnt' => (int) $arr['cnt'],
            'max' => (int) $arr['max'],
        ], $data);
    }

    /**
     * @return array<int, array{name: string, level: int, exp: int}>
     */
    public function getSpecialShipStats(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select( 'MAX(q.specialShipLevel) as level')
            ->addSelect('s.name as name')
            ->addSelect('MAX(q.specialShipExp) as exp')
            ->innerJoin('App:User', 'u', 'WITH', 'u.id = q.user')
            ->innerJoin('App:Ship', 's', 'WITH', 's.id = q.ship')
            ->andWhere('u.ghost = 0')
            ->andWhere('u.hmodFrom = 0')
            ->andWhere('u.hmodTo = 0')
            ->andWhere('s.special = 1')
            ->groupBy('s.id')
            ->orderBy('exp', 'DESC')
            ->getQuery()
            ->execute();

        return array_map(fn ($arr) => [
            'name' => $arr['name'],
            'level' => (int) $arr['level'],
            'exp' => (int) $arr['exp'],
        ], $data);
    }

    public function removeForUser(User $user): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function countBySearch(ShipListSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTradeableShipsOnPlanet(Planet $planet):array
    {
        return $this->createQueryBuilder('q')
            ->join('App:Ship','s','WITH','q.ship = s.id')
            ->where('s.special = 0')
            ->andWhere('q.entity = :planet')
            ->andWhere('q.count > 0')
            ->andWhere('s.shipTradeable = 1')
            ->andWhere('s.allianceCosts = 0')
            ->setParameter('planet',$planet)
            ->orderBy('s.name')
            ->getQuery()
            ->execute();
    }
}
