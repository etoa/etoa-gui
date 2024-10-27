<?php

declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\ShipListItem;
use EtoA\Entity\ShipQueueItem;

class ShipRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipListItem::class);
    }

    public function removeForEntity(int $entityId): void
    {
        $this->createQueryBuilder('q')
            ->delete('ship_queue')
            ->where('queue_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('shiplist')
            ->where('shiplist_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->executeQuery();
    }

    public function removeForUser(int $userId): void
    {
        $this->createQueryBuilder('q')
            ->delete('ship_queue')
            ->where('queue_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('shiplist')
            ->where('shiplist_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
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
            'name' => $arr['name'],
            'level' => (int) $arr['level'],
            'exp' => (int) $arr['exp'],
        ], $data);
    }


}
