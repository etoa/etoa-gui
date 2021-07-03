<?php declare(strict_types=1);

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
}
