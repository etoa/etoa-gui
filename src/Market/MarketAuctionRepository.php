<?php declare(strict_types=1);

namespace EtoA\Market;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\MarketAuction;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Universe\Resources\BaseResources;

class MarketAuctionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketAuction::class);
    }

    public function add(int $userId, int $entityId, int $dateEnd, string $text, BaseResources $sell, BaseResources $currency): int
    {
        $this->createQueryBuilder('q')
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
            ])->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function addBid(MarketAuction $auction, User $buyer, Planet $buyerEntity, BaseResources $bid, bool $finalBid = false, int $deleteDate = null): void
    {
        $auction->setCurrentBuyer($buyer);
        $auction->setCurrentBuyerEntity($buyerEntity);
        $auction->setCurrentBuyerDate(time());
        $auction->setBuy0($bid->metal);
        $auction->setBuy1($bid->crystal);
        $auction->setBuy2($bid->plastic);
        $auction->setBuy3($bid->fuel);
        $auction->setBuy4($bid->food);
        $auction->setBidCount($auction->getBidCount()+1);

        if ($finalBid) {
            $auction->setBuyable(false);
            $auction->setDeleted($deleteDate);
        }

        $this->save();
    }

    /**
     * @return MarketAuction[]
     */
    public function getAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('market_auction')
            ->orderBy('date_end', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketAuction($row), $data);
    }

    /**
     * @return MarketAuction[]
     */
    public function getBuyableAuctions(User $user): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.buyable = 1')
            ->andWhere('q.user <> :userId')
            ->orderBy('q.dateEnd', 'ASC')
            ->setParameter('userId', $user)
            ->getQuery()
            ->execute();
    }

    public function getNonUserAuction(int $id, int|User $userId): ?MarketAuction
    {
        return $this->createQueryBuilder('q')
            ->where('q.id = :id')
            ->andWhere('q.user <> :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return MarketAuction[]
     */
    public function getUserAuctions(User $user): array
    {
        return $this->findBy(['user'=>$user,'buyable'=>true],['dateEnd'=>'ASC']);
    }

    public function getUserAuction(int $id, int $userId): ?MarketAuction
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('market_auction')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])
            ->fetchAssociative();

        return $data !== false ? new MarketAuction($data) : null;
    }

    public function deleteUserAuctions(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function deleteAuction(MarketAuction $auction) : void
    {
        $this->remove($auction);
        $this->save();
    }
}
