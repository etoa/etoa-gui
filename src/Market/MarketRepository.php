<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Core\AbstractRepository;

class MarketRepository extends AbstractRepository
{
    public function getOfferCountOnCurrentEntity(int $userId, int $entityId): int
    {
        return (int) $this->getConnection()->fetchOne('
            SELECT ress_cnt.count + ship_cnt.count + auction_cnt.count FROM
                    (
                        SELECT
                            COUNT(*) count
                        FROM
                            market_ressource
                        WHERE
                            user_id = :userId
                            AND entity_id = :entityId
                            AND buyer_entity_id=0
                    ) AS ress_cnt,
                    (
                        SELECT
                            COUNT(*) count
                        FROM
                            market_ship
                        WHERE
                            user_id = :userId
                            AND entity_id = :entityId
                            AND buyer_entity_id=0
                    ) AS ship_cnt,
                    (
                        SELECT
                            COUNT(*) count
                        FROM
                            market_auction
                        WHERE
                            user_id = :userId
                            AND entity_id = :entityId
                    ) AS auction_cnt
        ', [
            'userId' => $userId,
            'entityId' => $entityId,
        ]);
    }
}
