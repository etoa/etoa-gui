<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\AbstractRepository;
use Log;

class UserRatingRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('user_ratings', 'r')
            ->innerJoin('r', 'users', 'u', 'u.user_id = r.id')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return UserDiplomacyRating[]
     */
    public function getDiplomacyRating(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        $data = $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.diplomacy_rating')
            ->orderBy('diplomacy_rating', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserDiplomacyRating($row), $data);
    }

    /**
     * @return UserBattleRating[]
     */
    public function getBattleRating(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        $data = $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.battle_rating, r.battles_lost, r.battles_won, r.battles_fought, r.elorating')
            ->orderBy('battle_rating', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserBattleRating($row), $data);
    }

    /**
     * @return UserTradeRating[]
     */
    public function getTradeRating(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        $data = $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.trade_rating, r.trades_buy, r.trades_sell')
            ->orderBy('trade_rating', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserTradeRating($row), $data);
    }

    private function createSpecialRatingQueryBuilder(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): QueryBuilder
    {
        return $this->applySearchSortLimit($this->createQueryBuilder(), $search, $sort, $limit, $offset)
            ->select('u.user_id', 'u.user_nick', 'ra.race_name', 'a.alliance_tag')
            ->from('user_ratings', 'r')
            ->innerJoin('r', 'users', 'u', 'u.user_id = r.id')
            ->innerJoin('u', 'races', 'ra', 'u.user_race_id = ra.race_id')
            ->leftJoin('u', 'alliances', 'a', 'u.user_alliance_id = a.alliance_id');
    }

    public function addBattleRating(int $userId, int $rating, string $reason = ""): void
    {
        if ($rating != 0) {
            $this->createQueryBuilder()
                ->update('user_ratings')
                ->set('battle_rating', 'battle_rating + :rating')
                ->where('id = :userId')
                ->setParameters([
                    'rating' => $rating,
                    'userId' => $userId,
                ])
                ->execute();

            if ($reason != "") {
                Log::add(17, Log::INFO, "KP: Der Spieler " . $userId . " erhält " . $rating . " Kampfpunkte. Grund: " . $reason);
            }
        }
    }

    public function addTradeRating(int $userId, int $rating, bool $sell = true, string $reason = ""): void
    {
        $qry = $this->createQueryBuilder()
            ->update('user_ratings')
            ->set('trade_rating', 'trade_rating + :rating')
            ->where('id = :userId')
            ->setParameters([
                'rating' => $rating,
                'userId' => $userId,
            ]);
        if ($sell) {
            $qry->set('trades_sell', 'trades_sell + 1');
        } else {
            $qry->set('trades_buy', 'trades_buy + 1');
        }
        $qry->execute();

        if ($reason != "") {
            Log::add(17, Log::INFO, "HP: Der Spieler " . $userId . " erhält " . $rating . " Handelspunkte. Grund: " . $reason);
        }
    }

    public function addDiplomacyRating(int $userId, int $rating, string $reason = ""): void
    {
        $this->createQueryBuilder()
            ->update('user_ratings')
            ->set('diplomacy_rating', 'diplomacy_rating + :rating')
            ->where('id = :userId')
            ->setParameters([
                'rating' => $rating,
                'userId' => $userId,
            ])
            ->execute();

        if ($reason != "") {
            Log::add(17, Log::INFO, "DP: Der Spieler " . $userId . " erhält " . $rating . " Diplomatiepunkte. Grund: " . $reason);
        }
    }

    public function addBlank(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('user_ratings')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute();

        $this->createQueryBuilder()
            ->insert('user_ratings')
            ->values([
                'id' => ':id',
            ])
            ->setParameters([
                'id' => $id,
            ])
            ->execute();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(id)')
            ->from('user_ratings')
            ->where($qb->expr()->notIn('id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchOne();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function deleteOrphaned(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('user_ratings')
            ->where($qb->expr()->notIn('id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('user_ratings')
            ->where('id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}
