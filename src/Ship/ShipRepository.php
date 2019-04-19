<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\AbstractRepository;

class ShipRepository extends AbstractRepository
{
    public function addShip(int $shipId, int $amount, int $userId, int $entityId): void
    {
        $hasShips = $this->createQueryBuilder()
            ->select('shiplist_id')
            ->from('shiplist')
            ->where('shiplist_user_id = :userId')
            ->andWhere('shiplist_entity_id = :entityId')
            ->andWhere('shiplist_ship_id = :shipId')
            ->setParameters([
                'shipId' => $shipId,
                'userId' => $userId,
                'entityId' => $entityId,
            ])->execute()->fetchColumn();

        if ($hasShips) {
            $this->createQueryBuilder()
                ->update('shiplist')
                ->set('shiplist_count', 'shiplist_count + :amount')
                ->where('shiplist_ship_id = :shipId')
                ->andWhere('shiplist_entity_id = :entityId')
                ->andWhere('shiplist_user_id = :userId')
                ->setParameters([
                    'amount' => $amount,
                    'shipId' => $shipId,
                    'userId' => $userId,
                    'entityId' => $entityId,
                ])->execute();
        } else {
            $this->createQueryBuilder()
                ->insert('shiplist')
                ->values([
                    'shiplist_count' => ':amount',
                    'shiplist_ship_id' => ':shipId',
                    'shiplist_entity_id' => ':entityId',
                    'shiplist_user_id' => ':userId',
                ])
                ->setParameters([
                    'amount' => $amount,
                    'shipId' => $shipId,
                    'userId' => $userId,
                    'entityId' => $entityId,
                ])->execute();
        }
    }
}
