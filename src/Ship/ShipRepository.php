<?php

declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class ShipRepository extends AbstractRepository
{
    public function getNumberOfShips(int $shipId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(shiplist_id)')
            ->from('shiplist')
            ->where('shiplist_ship_id = :shipId')
            ->setParameter('shipId', $shipId)
            ->execute()
            ->fetchOne();
    }

    /**
     * @return array<int, int>
     */
    public function getEntityShipCounts(int $userId, int $entityId): array
    {
        $data = $this->createQueryBuilder()
            ->select('shiplist_ship_id, shiplist_count')
            ->from('shiplist')
            ->where('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->andWhere('shiplist_count > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @param ?int[] $shipIds
     * @return ShipListItem[]
     */
    public function findForUser(int $userId, ?int $entityId = null, array $shipIds = null): array
    {
        $qb = $this->createQueryBuilder()
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
                ->setParameter('shipIds', $shipIds, Connection::PARAM_INT_ARRAY);
        }

        $data = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipListItem::createFromData($row), $data);
    }

    /**
     * @return ShipListItem[]
     */
    public function search(ShipListSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit, $offset)
            ->select('*')
            ->from('shiplist')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipListItem::createFromData($row), $data);
    }

    public function find(int $id): ?ShipListItem
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('shiplist')
            ->where('shiplist_id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? ShipListItem::createFromData($data) : null;
    }

    /**
     * @return array<int, ShipListItemCount>
     */
    public function getUserShipCounts(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('shiplist_ship_id, SUM(shiplist_count) as count, SUM(shiplist_bunkered) as bunkered, SUM(shiplist_special_ship_exp) as shiplist_special_ship_exp')
            ->from('shiplist')
            ->where('shiplist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->groupBy('shiplist_ship_id')
            ->execute()
            ->fetchAllAssociative();

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

        $available = (int) $this->createQueryBuilder()
            ->select('shiplist_count')
            ->from('shiplist')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'shipId' => $shipId,
            ])->execute()->fetchOne();

        $amount = min($available, $amount);

        $this->createQueryBuilder()
            ->update('shiplist1')
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
            ->execute();

        return $amount;
    }

    private function addShipCount(int $shipId, int $amount, int $userId, int $entityId): void
    {
        $this->getConnection()->executeQuery('INSERT INTO shiplist (
                shiplist_user_id,
                shiplist_entity_id,
                shiplist_ship_id,
                shiplist_count
            ) VALUES (
                :userId,
                :entityId,
                :shipId,
                :amount
            ) ON DUPLICATE KEY
            UPDATE shiplist_count = shiplist_count + VALUES(shiplist_count);
        ', [
            'userId' => $userId,
            'amount' => max(0, $amount),
            'entityId' => $entityId,
            'shipId' => $shipId,
        ]);
    }

    public function removeForEntity(int $entityId): void
    {
        $this->createQueryBuilder()
            ->delete('ship_queue')
            ->where('queue_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('shiplist')
            ->where('shiplist_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute();
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder()
            ->delete('ship_queue')
            ->where('queue_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('shiplist')
            ->where('shiplist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function removeEntry(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('shiplist')
            ->where('shiplist_id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function hasShipsOnEntity(int $entityId): bool
    {
        $count = (int) $this->createQueryBuilder()
            ->select('COUNT(shiplist_id)')
            ->from('shiplist')
            ->where('shiplist_entity_id = :entityId')
            ->andWhere('shiplist_count  > 0')
            ->setParameter('entityId', $entityId)
            ->execute()
            ->fetchOne();

        return $count > 0;
    }

    public function countBuildInProgress(int $userId, int $entityId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(queue_id)')
            ->from('ship_queue')
            ->where('queue_entity_id = :entityId')
            ->andWhere('queue_user_id = :userId')
            ->andWhere('queue_starttime > 0')
            ->andWhere('queue_endtime > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->execute()
            ->fetchOne();
    }

    /**
     * @return ShipQueueItem[]
     */
    public function findQueueItemsForUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('ship_queue')
            ->where('queue_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('queue_starttime', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipQueueItem::createFromData($row), $data);
    }

    public function saveQueueItem(ShipQueueItem $item): void
    {
        $this->createQueryBuilder()
            ->update('ship_queue')
            ->set('queue_user_id', ':userId')
            ->set('queue_ship_id', ':shipId')
            ->set('queue_entity_id', ':entityId')
            ->set('queue_cnt', ':count')
            ->set('queue_starttime', ':startTime')
            ->set('queue_endtime', ':endTime')
            ->set('queue_objtime', ':objectTime')
            ->set('queue_build_type', ':buildType')
            ->where('id = :id')
            ->setParameters([
                'id' => $item->id,
                'userId' => $item->userId,
                'shipId' => $item->shipId,
                'entityId' => $item->entityId,
                'count' => $item->count,
                'startTime' => $item->startTime,
                'endTime' => $item->endTime,
                'objectTime' => $item->objectTime,
                'buildType' => $item->buildType,
            ])
            ->execute();
    }

    public function saveItem(ShipListItem $item): void
    {
        $this->createQueryBuilder()
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
            ->execute();
    }

    public function bunker(int $userId, int $entityId, int $shipId, int $count): int
    {
        $info = $this->createQueryBuilder()
            ->select('shiplist_id', 'shiplist_count')
            ->from('shiplist')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'shipId' => $shipId,
            ])->execute()->fetchAssociative();

        if ($info === false) {
            return 0;
        }

        $delable = max(0, min($count, (int) $info['shiplist_count']));

        $this->createQueryBuilder()
            ->update('shiplist')
            ->set('shiplist_bunkered', 'shiplist_bunkered + :change')
            ->set('shiplist_count', 'shiplist_count - :change')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_id = :id')
            ->setParameters([
                'change' => $delable,
                'id' => $info['shiplist_id'],
                'shipId' => $shipId,
            ])->execute();

        return $delable;
    }

    /**
     * @return array<int, int>
     */
    public function getBunkeredCount(int $userId, int $entityId): array
    {
        $data = $this->createQueryBuilder()
            ->select('shiplist_ship_id, shiplist_bunkered')
            ->from('shiplist')
            ->where('shiplist_entity_id = :entityId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_bunkered  > 0')
            ->setParameters([
                'entityId' => $entityId,
                'userId' => $userId,
            ])
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function leaveBunker(int $userId, int $entityId, int $shipId, int $count): int
    {
        $info = $this->createQueryBuilder()
            ->select('shiplist_id', 'shiplist_bunkered')
            ->from('shiplist')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'shipId' => $shipId,
            ])->execute()->fetchAssociative();

        if ($info === false) {
            return 0;
        }

        $delable = max(0, min($count, (int) $info['shiplist_bunkered']));

        $this->createQueryBuilder()
            ->update('shiplist')
            ->set('shiplist_bunkered', 'shiplist_bunkered - :change')
            ->set('shiplist_count', 'shiplist_count + :change')
            ->where('shiplist_ship_id = :shipId')
            ->andWhere('shiplist_id = :id')
            ->setParameters([
                'change' => $delable,
                'id' => $info['shiplist_id'],
                'shipId' => $shipId,
            ])->execute();

        return $delable;
    }

    public function count(ShipListSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(shiplist_id)')
            ->from('shiplist')
            ->execute()
            ->fetchOne();
    }

    public function countEmpty(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(shiplist_id)')
            ->from('shiplist')
            ->where('shiplist_count = 0')
            ->andWhere('shiplist_bunkered = 0')
            ->andWhere('shiplist_special_ship = 0')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return array<int, array{name: string, cnt: int, max: int}>
     */
    public function getOverallCount(): array
    {
        $data = $this->getConnection()
            ->fetchAllAssociative(
                "SELECT
                    ships.ship_name as name,
                    SUM(shiplist.shiplist_count+shiplist.shiplist_bunkered) as cnt,
                    MAX(shiplist.shiplist_count+shiplist.shiplist_bunkered) as max
                FROM
                    ships
                INNER JOIN
                    (
                        shiplist
                    INNER JOIN
                        users
                    ON
                        shiplist_user_id = user_id
                        AND user_ghost = 0
                        AND user_hmode_from = 0
                        AND user_hmode_to = 0
                    )
                ON
                    shiplist_ship_id = ship_id
                    AND ships.special_ship = 0
                GROUP BY
                    ships.ship_id
                ORDER BY
                    cnt DESC;"
            );

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'cnt' => (int) $arr['cnt'],
            'max' => (int) $arr['max'],
        ], $data);
    }

    /**
     * @return array<int, array{name: string, level: int, exp: int}>
     */
    public function getSpecialShipStats(): array
    {
        $data = $this->getConnection()
            ->fetchAllAssociative(
                "SELECT
                    ships.ship_name as name,
                    MAX(shiplist.shiplist_special_ship_level) as level,
                    MAX(shiplist.shiplist_special_ship_exp) as exp
                FROM
                    ships
                INNER JOIN
                    (
                        shiplist
                    INNER JOIN
                        users
                    ON
                        shiplist_user_id = user_id
                        AND user_ghost = 0
                        AND user_hmode_from = 0
                        AND user_hmode_to = 0
                    )
                ON
                    shiplist_ship_id = ship_id
                    AND ships.special_ship = 1
                GROUP BY
                    ships.ship_id
                ORDER BY
                    exp DESC;"
            );

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'level' => (int) $arr['level'],
            'exp' => (int) $arr['exp'],
        ], $data);
    }

    public function getSpecialShipExperienceSumForUser(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('SUM(shiplist_special_ship_exp)')
            ->from('shiplist')
            ->where('shiplist_user_id = :userId')
            ->andWhere('shiplist_count = 1')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchOne();
    }

    public function cleanUp(): int
    {
        return $this->getConnection()
            ->executeStatement(
                "DELETE FROM
                    `shiplist`
                WHERE
                    `shiplist_count`='0'
                    AND `shiplist_bunkered`='0'
                    AND `shiplist_special_ship`='0'
                    ;"
            );
    }
}
