<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Core\AbstractRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;

class MarketResourceRepository extends AbstractRepository
{
    public function add(int $userId, int $entityId, int $forUserId, int $forAllianceId, string $text, BaseResources $sell, BaseResources $buy): int
    {
        $this->createQueryBuilder()
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
            ])->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @return MarketResource[]
     */
    public function getAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ressource')
            ->orderBy('datum', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketResource($row), $data);
    }

    /**
     * @return MarketResource[]
     */
    public function getBuyableOffers(int $userId, int $allianceId, BaseResources $sellFilter, BaseResources $buyFilter): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ressource')
            ->where('user_id <> :userId')
            ->andWhere('for_user = 0 OR for_user = :userId')
            ->andWhere('for_alliance = 0 OR for_alliance = :allianceId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ]);

        if ($sellFilter->getSum() > 0) {
            $filter = [];
            foreach (array_keys(ResourceNames::NAMES) as $index) {
                if ($sellFilter->get($index) > 0) {
                    $filter[] = 'sell_' . $index . ' > 0';
                }
            }
            $qb->andWhere(implode(' OR ', $filter));
        }

        if ($buyFilter->getSum() > 0) {
            $filter = [];
            foreach (array_keys(ResourceNames::NAMES) as $index) {
                if ($buyFilter->get($index) > 0) {
                    $filter[] = 'buy_' . $index . ' > 0';
                }
            }
            $qb->andWhere(implode(' OR ', $filter));
        }

        $data = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketResource($row), $data);
    }

    public function countBuyableOffers(int $userId, int $allianceId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('count(*)')
            ->from('market_ressource')
            ->where('user_id <> :userId')
            ->andWhere('for_user = 0 OR for_user = :userId')
            ->andWhere('for_alliance = 0 OR for_alliance = :allianceId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function getBuyableOffer(int $id, int $userId, int $allianceId): ?MarketResource
    {
        $data = $this->createQueryBuilder()
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
            ])->execute()
            ->fetchAssociative();

        return $data !== false ? new MarketResource($data) : null;
    }

    /**
     * @return MarketResource[]
     */
    public function getUserOffers(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ressource')
            ->where('user_id = :userId')
            ->andWhere('buyable = 1')
            ->orderBy('datum', 'ASC')
            ->setParameters([
                'userId' => $userId,
            ])->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketResource($row), $data);
    }

    public function getUserOffer(int $id, int $userId): ?MarketResource
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ressource')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])->execute()
            ->fetchAssociative();

        return $data !== false ? new MarketResource($data) : null;
    }

    public function deleteUserOffers(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('market_ressource')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function delete(int $offerId) : void
    {
        $this->createQueryBuilder()
            ->delete('market_ressource')
            ->where('id = :id')
            ->setParameter('id', $offerId)
            ->execute();
    }
}
