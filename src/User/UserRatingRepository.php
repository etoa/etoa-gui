<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\AbstractRepository;

class UserRatingRepository extends AbstractRepository
{
    /**
     * @return UserDiplomacyRating[]
     */
    public function getDiplomacyRating(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        $data = $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.diplomacy_rating')
            ->orderBy('diplomacy_rating', 'DESC')
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
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserTradeRating($row), $data);
    }

    private function createSpecialRatingQueryBuilder(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): QueryBuilder
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort, $limit, $offset)
            ->select('u.user_id', 'u.user_nick', 'ra.race_name', 'a.alliance_tag')
            ->from('user_ratings', 'r')
            ->innerJoin('r', 'users', 'u', 'u.user_id = r.id')
            ->innerJoin('u', 'races', 'ra', 'u.user_race_id = ra.race_id')
            ->leftJoin('u', 'alliances', 'a', 'u.user_alliance_id = a.alliance_id');
    }

    public function addTradeRating(int $userId, int $rating, bool $sell = true): void
    {
        $qry = $this->createQueryBuilder('q')
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
        $qry->executeQuery();
    }

    public function addDiplomacyRating(int $userId, int $rating): void
    {
        $this->createQueryBuilder('q')
            ->update('user_ratings')
            ->set('diplomacy_rating', 'diplomacy_rating + :rating')
            ->where('id = :userId')
            ->setParameters([
                'rating' => $rating,
                'userId' => $userId,
            ])
            ->executeQuery();
    }

    public function addBlank(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('user_ratings')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->insert('user_ratings')
            ->values([
                'id' => ':id',
            ])
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder('q')
            ->delete('user_ratings')
            ->where('id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }
}
