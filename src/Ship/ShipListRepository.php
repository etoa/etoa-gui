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
use EtoA\Universe\Entity\EntityRepository;

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
    public function __construct(ManagerRegistry $registry, private readonly EntityRepository $entityRepository)
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
    public function findForUser(int $userId, ?int $entityId = null, array $shipIds = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('shiplist')
            ->where('shiplist_user_id = :userId')
            ->andWhere('shiplist_count > 0 OR shiplist_bunkered > 0')
            ->setParameter('userId', $userId);

        if ($entityId !== null) {
            $qb
                ->andWhere('shiplist_entity_id = :entityId')
                ->setParameter('entityId', $entityId);
        }

        if ($shipIds !== null) {
            $qb
                ->andWhere('shiplist_ship_id IN (:shipIds)')
                ->setParameter('shipIds', $shipIds, ArrayParameterType::INTEGER);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipListItem::createFromData($row), $data);
    }

    /**
     * @return ShipListItem[]
     */
    public function search(ShipListSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->select('*')
            ->from('shiplist')
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipListItem::createFromData($row), $data);
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

    public function addShip(int $shipId, int $amount, int $userId, int $entityId): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot add negative ship count');
        }

        $this->addShipCount($shipId, $amount, $userId, $entityId);
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

    public function removeForEntity(Entity $entity): void
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
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(shiplist_id)')
            ->from('shiplist')
            ->where('shiplist_count = 0')
            ->andWhere('shiplist_bunkered = 0')
            ->andWhere('shiplist_special_ship = 0')
            ->fetchOne();
    }

    public function getSpecialShipExperienceSumForUser(int $userId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('SUM(shiplist_special_ship_exp)')
            ->from('shiplist')
            ->where('shiplist_user_id = :userId')
            ->andWhere('shiplist_count = 1')
            ->setParameter('userId', $userId)
            ->fetchOne();
    }

    public function cleanUp(): int
    {
        return $this->getConnection()
            ->executeQuery(
                "DELETE FROM
                    `shiplist`
                WHERE
                    `shiplist_count`='0'
                    AND `shiplist_bunkered`='0'
                    AND `shiplist_special_ship`='0'
                    ;"
            )
            ->rowCount();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('shiplist')
            ->where('shiplist_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    private function addShipCount(int $shipId, int $amount, int $userId, int $entityId): void
    {
        $item = $this->findOneBy(['userId'=>$userId,'shipId'=>$shipId,'entityId'=>$entityId]);

        if(!$item) {
            $item = new ShipListItem();
            $item->setUserId($userId);
            $item->setEntityId($entityId);
            $item->setEntity($this->entityRepository->findOneBy(['id'=>$entityId]));
            $item->setShipId($shipId);
        }

        $item->setCount($item->getCount()+max(0, $amount));

        $this->getEntityManager()->persist($item);
        $this->getEntityManager()->flush();
    }
}
