<?php

namespace EtoA\Ship;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\Entity;
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
class ShipListRepository extends ServiceEntityRepository
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
     * @return array<int, int>
     */
    public function getEntityShipCounts(int $userId, int $entityId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('shiplist_ship_id, shiplist_count')
            ->from('shiplist')
            ->where('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->andWhere('shiplist_count > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
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

    public function removeShips(int $shipId, int $amount, int $userId, int $entityId): int
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot remove negative ship count');
        }

        $available = (int) $this->createQueryBuilder('q')
            ->select('shiplist_count')
            ->from('shiplist')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'shipId' => $shipId,
            ])->fetchOne();

        $amount = min($available, $amount);

        $this->createQueryBuilder('q')
            ->update('shiplist')
            ->set('shiplist_count', 'shiplist_count - :amount')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'shipId' => $shipId,
                'amount' => $amount,
            ])
            ->executeQuery();

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

    public function bunker(int $userId, int $entityId, int $shipId, int $count): int
    {
        $info = $this->createQueryBuilder('q')
            ->select('shiplist_id', 'shiplist_count')
            ->from('shiplist')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'shipId' => $shipId,
            ])->fetchAssociative();

        if ($info === false) {
            return 0;
        }

        $delable = max(0, min($count, (int) $info['shiplist_count']));

        $this->createQueryBuilder('q')
            ->update('shiplist')
            ->set('shiplist_bunkered', 'shiplist_bunkered + :change')
            ->set('shiplist_count', 'shiplist_count - :change')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_id = :id')
            ->setParameters([
                'change' => $delable,
                'id' => $info['shiplist_id'],
                'shipId' => $shipId,
            ])->executeQuery();

        return $delable;
    }

    /**
     * @return array<int, int>
     */
    public function getBunkeredCount(int $userId, int $entityId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('shiplist_ship_id, shiplist_bunkered')
            ->from('shiplist')
            ->where('shiplist_entity_id = :entityId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_bunkered  > 0')
            ->setParameters([
                'entityId' => $entityId,
                'userId' => $userId,
            ])
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function leaveBunker(int $userId, int $entityId, int $shipId, int $count): int
    {
        $info = $this->createQueryBuilder('q')
            ->select('shiplist_id', 'shiplist_bunkered')
            ->from('shiplist')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'shipId' => $shipId,
            ])->fetchAssociative();

        if ($info === false) {
            return 0;
        }

        $delable = max(0, min($count, (int) $info['shiplist_bunkered']));

        $this->createQueryBuilder('q')
            ->update('shiplist')
            ->set('shiplist_bunkered', 'shiplist_bunkered - :change')
            ->set('shiplist_count', 'shiplist_count + :change')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_id = :id')
            ->setParameters([
                'change' => $delable,
                'id' => $info['shiplist_id'],
                'shipId' => $shipId,
            ])->executeQuery();

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
