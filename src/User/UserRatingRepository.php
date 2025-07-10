<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;
use EtoA\Entity\UserRating;

class UserRatingRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRating::class);
    }

    /**
     * @return UserDiplomacyRating[]
     */
    public function getDiplomacyRating(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        return $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.diplomacyRating as points')
            ->orderBy('points', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return UserBattleRating[]
     */
    public function getBattleRating(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        return $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.battleRating as points')
            ->orderBy('points', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return UserTradeRating[]
     */
    public function getTradeRating(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        return $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.tradeRating as points')
            ->orderBy('points', 'DESC')
            ->getQuery()
            ->execute();
    }

    private function createSpecialRatingQueryBuilder(UserRatingSearch $search = null, UserRatingSort $sort = null, int $limit = null, int $offset = null): QueryBuilder
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('r'), $search, $sort, $limit, $offset)
            ->innerJoin('App:User', 'u', 'WITH', 'r.userId = u.id')
            ->innerJoin('App:Race', 'ra', 'WITH', 'ra.id = u.race')
            ->leftJoin('App:Alliance', 'a', 'WITH', 'u.alliance = a.id');
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

    public function addDiplomacyRating(User $user, int $rating): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.diplomacyRating', 'q.diplomacyRating + :rating')
            ->where('q.userId = :userId')
            ->setParameters([
                'rating' => $rating,
                'userId' => $user->getId(),
            ])
            ->getQuery()
            ->execute();
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
