<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Core\AbstractRepository;
use EtoA\Universe\Resources\BaseResources;

class MarketShipRepository extends AbstractRepository
{
    public function add(int $userId, int $entityId, int $forUserId, int $forAllianceId, string $text, int $shipId, int $shipCount, BaseResources $costs): int
    {
        $this->createQueryBuilder()
            ->insert('market_ship')
            ->values([
                'user_id' => ':userId',
                'entity_id' => ':entityId',
                'for_user' => ':forUserId',
                'for_alliance' => ':forAllianceId',
                'text' => ':text',
                'datum' => ':now',
                'costs_0' => ':costs0',
                'costs_1' => ':costs1',
                'costs_2' => ':costs2',
                'costs_3' => ':costs3',
                'costs_4' => ':costs4',
                'ship_id' => ':shipId',
                'count' => ':shipCount',
            ])
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'now' => time(),
                'forUserId' => $forUserId,
                'forAllianceId' => $forAllianceId,
                'text' => $text,
                'costs0' => $costs->metal,
                'costs1' => $costs->crystal,
                'costs2' => $costs->plastic,
                'costs3' => $costs->fuel,
                'costs4' => $costs->food,
                'shipId' => $shipId,
                'shipCount' => $shipCount,
            ])->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @return MarketShip[]
     */
    public function getAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ship')
            ->orderBy('datum', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketShip($row), $data);
    }

    /**
     * @return MarketShip[]
     */
    public function getBuyableOffers(int $userId, int $allianceId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ship')
            ->where('user_id <> :userId')
            ->andWhere('for_user = 0 OR for_user = :userId')
            ->andWhere('for_alliance = 0 OR for_alliance = :allianceId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketShip($row), $data);
    }

    public function getBuyableOffer(int $id, int $userId, int $allianceId): ?MarketShip
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ship')
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

        return $data !== false ? new MarketShip($data) : null;
    }

    /**
     * @return MarketShip[]
     */
    public function getUserOffers(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ship')
            ->where('user_id = :userId')
            ->andWhere('buyable = 1')
            ->orderBy('datum', 'ASC')
            ->setParameters([
                'userId' => $userId,
            ])->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketShip($row), $data);
    }

    public function getUserOffer(int $id, int $userId): ?MarketShip
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('market_ship')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])->execute()
            ->fetchAssociative();

        return $data !== false ? new MarketShip($data) : null;
    }

    public function deleteUserOffers(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('market_ship')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }

    public function delete(int $offerId) : void
    {
        $this->createQueryBuilder()
            ->delete('market_ship')
            ->where('id = :id')
            ->setParameter('id', $offerId)
            ->execute();
    }
}
