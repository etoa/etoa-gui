<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\AbstractRepository;

class ShipRepository extends AbstractRepository
{
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
}
