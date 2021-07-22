<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Core\AbstractRepository;
use EtoA\Universe\Resources\BaseResources;

class MarketAuctionRepository extends AbstractRepository
{
    public function add(int $userId, int $entityId, int $dateEnd, string $text, BaseResources $sell, BaseResources $currency): int
    {
        $this->createQueryBuilder()
            ->insert('market_auction')
            ->values([
                'user_id' => ':userId',
                'entity_id' => ':entityId',
                'date_start' => ':dateStart',
                'date_end' => ':dateEnd',
                'text' => ':text',
                'sell_0' => ':sell0',
                'sell_1' => ':sell1',
                'sell_2' => ':sell2',
                'sell_3' => ':sell3',
                'sell_4' => ':sell4',
                'currency_0' => ':currency0',
                'currency_1' => ':currency1',
                'currency_2' => ':currency2',
                'currency_3' => ':currency3',
                'currency_4' => ':currency4',
                'buyable' => ':buyable',
            ])
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'dateStart' => time(),
                'dateEnd' => $dateEnd,
                'text' => $text,
                'sell0' => $sell->metal,
                'sell1' => $sell->crystal,
                'sell2' => $sell->plastic,
                'sell3' => $sell->fuel,
                'sell4' => $sell->food,
                'currency0' => $currency->metal,
                'currency1' => $currency->crystal,
                'currency2' => $currency->plastic,
                'currency3' => $currency->fuel,
                'currency4' => $currency->food,
                'buyable' => 1,
            ])->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function addBid(int $auctionId, int $buyerId, int $buyerEntityId, BaseResources $bid, bool $finalBid = false, int $deleteDate = null): void
    {
        $parameters = [
            'id' => $auctionId,
            'buyerId' => $buyerId,
            'buyerEntityId' => $buyerEntityId,
            'now' => time(),
            'buy0' => $bid->metal,
            'buy1' => $bid->crystal,
            'buy2' => $bid->plastic,
            'buy3' => $bid->fuel,
            'buy4' => $bid->food,
        ];

        $qb = $this->createQueryBuilder()
            ->update('market_auction')
            ->set('current_buyer_id', ':buyerId')
            ->set('current_buyer_entity_id', ':buyerEntityId')
            ->set('current_buyer_date', ':now')
            ->set('bidcount', 'bidcount + 1')
            ->set('buy_0', ':buy0')
            ->set('buy_1', ':buy1')
            ->set('buy_2', ':buy2')
            ->set('buy_3', ':buy3')
            ->set('buy_4', ':buy4')
            ->where('id = :id');

        if ($finalBid) {
            $qb
                ->set('buyable', '0')
                ->set('date_delete', ':delete');
            $parameters['delete'] = $deleteDate;
        }

        $qb
            ->setParameters($parameters)
            ->execute();

    }

    /**
     * @return MarketAuction[]
     */
    public function getAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_auction')
            ->orderBy('date_end', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketAuction($row), $data);
    }

    /**
     * @return MarketAuction[]
     */
    public function getBuyableAuctions(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_auction')
            ->where('buyable = 1')
            ->andWhere('user_id <> :userId')
            ->orderBy('date_end', 'ASC')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketAuction($row), $data);
    }

    public function getNonUserAuction(int $id, int $userId): ?MarketAuction
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_auction')
            ->where('id = :id')
            ->andWhere('user_id <> :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])->execute()
            ->fetchAssociative();

        return $data !== false ? new MarketAuction($data) : null;
    }

    /**
     * @return MarketAuction[]
     */
    public function getUserAuctions(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_auction')
            ->where('user_id = :userId')
            ->orderBy('date_end', 'ASC')
            ->setParameters([
                'userId' => $userId,
            ])->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketAuction($row), $data);
    }

    public function getUserAuction(int $id, int $userId): ?MarketAuction
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_auction')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])->execute()
            ->fetchAssociative();

        return $data !== false ? new MarketAuction($data) : null;
    }

    public function deleteUserAuctions(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('market_auction')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function deleteAuction(int $auctionId) : void
    {
        $this->createQueryBuilder()
            ->delete('market_auction')
            ->where('id = :id')
            ->setParameter('id', $auctionId)
            ->execute();
    }
}
