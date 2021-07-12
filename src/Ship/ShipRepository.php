<?php

declare(strict_types=1);

namespace EtoA\Ship;

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

    public function addShip(int $shipId, int $amount, int $userId, int $entityId): void
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

        return array_map(fn ($row) => new ShipQueueItem($row), $data);
    }

    public function saveQueueItem(ShipQueueItem $item): void
    {
        $this->createQueryBuilder()
            ->update('ship_queue')
            ->set('queue_user_id', 'userId')
            ->set('queue_ship_id', 'shipId')
            ->set('queue_entity_id', 'entityId')
            ->set('queue_cnt', 'count')
            ->set('queue_starttime', 'startTime')
            ->set('queue_endtime', 'endTime')
            ->set('queue_objtime', 'objectTime')
            ->set('queue_build_type', 'buildType')
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
}
