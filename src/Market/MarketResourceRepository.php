<?php declare(strict_types=1);

namespace EtoA\Market;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\MarketResource;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;

class MarketResourceRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketResource::class);
    }

    public function add(User $user, Planet $entity, int $forUserId, int $forAllianceId, string $text, BaseResources $sell, BaseResources $buy): int
    {
        $this->createQueryBuilder('q')
            ->insert('market_ressource')
            ->values([
                'user_id' => ':userId',
                'entity_id' => ':entityId',
                'for_user' => ':forUserId',
                'for_alliance' => ':forAllianceId',
                'text' => ':text',
                'datum' => ':now',
                'sell_0' => ':sell0',
                'sell_1' => ':sell1',
                'sell_2' => ':sell2',
                'sell_3' => ':sell3',
                'sell_4' => ':sell4',
                'buy_0' => ':buy0',
                'buy_1' => ':buy1',
                'buy_2' => ':buy2',
                'buy_3' => ':buy3',
                'buy_4' => ':buy4',
            ])
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'now' => time(),
                'forUserId' => $forUserId,
                'forAllianceId' => $forAllianceId,
                'text' => $text,
                'sell0' => $sell->metal,
                'sell1' => $sell->crystal,
                'sell2' => $sell->plastic,
                'sell3' => $sell->fuel,
                'sell4' => $sell->food,
                'buy0' => $buy->metal,
                'buy1' => $buy->crystal,
                'buy2' => $buy->plastic,
                'buy3' => $buy->fuel,
                'buy4' => $buy->food,
            ])->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @return MarketResource[]
     */
    public function getAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('market_ressource')
            ->orderBy('datum', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketResource($row), $data);
    }

    /**
     * @return MarketResource[]
     */
    public function getBuyableOffers(User $user, ?BaseResources $sellFilter = null, ?BaseResources $buyFilter = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user <> :userId')
            ->andWhere('q.forUser IS NULL OR q.forUser = :userId')
            ->andWhere('q.forAlliance IS NULL OR q.forAlliance = :allianceId')
            ->setParameters([
                'userId' => $user,
                'allianceId' => $user->getAlliance(),
            ]);


        if ($sellFilter && $sellFilter->getSum() > 0) {
            $filter = [];
            foreach (array_keys(ResourceNames::NAMES) as $index) {
                if ($sellFilter->get($index) > 0) {
                    $filter[] = 'q.sell' . $index . ' > 0';
                }
            }
            $qb->andWhere(implode(' OR ', $filter));
        }

        if ($buyFilter && $buyFilter->getSum() > 0) {
            $filter = [];
            foreach (array_keys(ResourceNames::NAMES) as $index) {
                if ($buyFilter->get($index) > 0) {
                    $filter[] = 'q.buy' . $index . ' > 0';
                }
            }
            $qb->andWhere(implode(' OR ', $filter));
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function countBuyableOffers(int $userId, int $allianceId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('count(*)')
            ->from('market_ressource')
            ->where('user_id <> :userId')
            ->andWhere('for_user = 0 OR for_user = :userId')
            ->andWhere('for_alliance = 0 OR for_alliance = :allianceId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->fetchOne();
    }

    public function getBuyableOffer(int $id, int $userId, int $allianceId): ?MarketResource
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('market_ressource')
            ->where('id = :id')
            ->andWhere('user_id <> :userId')
            ->andWhere('for_user = 0 OR for_user = :userId')
            ->andWhere('for_alliance = 0 OR for_alliance = :allianceId')
            ->andWhere('buyable = 1')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->fetchAssociative();

        return $data !== false ? new MarketResource($data) : null;
    }

    /**
     * @return MarketResource[]
     */
    public function getUserOffers(User $user): array
    {
        return $this->findBy(['user'=>$user,'buyable'=>true],['date'=>'ASC']);
    }

    public function getUserOffer(int $id, int $userId): ?MarketResource
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('market_ressource')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])
            ->fetchAssociative();

        return $data !== false ? new MarketResource($data) : null;
    }

    public function deleteUserOffers(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function delete(MarketResource $offer) : void
    {
        $this->remove($offer);
        $this->save();
    }
}
