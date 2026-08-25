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
    public function __construct(ManagerRegistry $registry, private readonly UserRepository $userRepository)
    {
        parent::__construct($registry, UserRating::class);
    }

    /**
     * Mixed result: one row per rating, each `[0 => UserRating, 'points' => int]`.
     *
     * @return array<int, array{0: UserRating, points: int}>
     */
    public function getDiplomacyRating(?UserRatingSearch $search = null, ?UserRatingSort $sort = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.diplomacyRating as points')
            ->orderBy('points', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * Mixed result: one row per rating, each `[0 => UserRating, 'points' => int]`.
     *
     * @return array<int, array{0: UserRating, points: int}>
     */
    public function getBattleRating(?UserRatingSearch $search = null, ?UserRatingSort $sort = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.battleRating as points')
            ->orderBy('points', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * Mixed result: one row per rating, each `[0 => UserRating, 'points' => int]`.
     *
     * @return array<int, array{0: UserRating, points: int}>
     */
    public function getTradeRating(?UserRatingSearch $search = null, ?UserRatingSort $sort = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->createSpecialRatingQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('r.tradeRating as points')
            ->orderBy('points', 'DESC')
            ->getQuery()
            ->execute();
    }

    private function createSpecialRatingQueryBuilder(?UserRatingSearch $search = null, ?UserRatingSort $sort = null, ?int $limit = null, ?int $offset = null): QueryBuilder
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('r'), $search, $sort, $limit, $offset)
            ->innerJoin('App:User', 'u', 'WITH', 'r.user = u.id')
            ->innerJoin('App:Race', 'ra', 'WITH', 'ra.id = u.race')
            ->leftJoin('App:Alliance', 'a', 'WITH', 'u.alliance = a.id');
    }

    public function addTradeRating(User $user, int $rating, bool $sell = true): void
    {
        $qry = $this->createQueryBuilder('q')
            ->update()
            ->set('q.tradeRating', 'q.tradeRating + :rating')
            ->where('q.user = :userId')
            ->setParameters([
                'rating' => $rating,
                'userId' => $user,
            ]);
        if ($sell) {
            $qry->set('q.tradesSell', 'q.tradesSell + 1');
        } else {
            $qry->set('q.tradesBuy', 'q.tradesBuy + 1');
        }
        $qry->getQuery()->execute();
    }

    public function addDiplomacyRating(User $user, int $rating): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.diplomacyRating', 'q.diplomacyRating + :rating')
            ->where('q.user = :userId')
            ->setParameters([
                'rating' => $rating,
                'userId' => $user->getId(),
            ])
            ->getQuery()
            ->execute();
    }

    public function addBlank(User $user): void
    {
        $rating = new UserRating();

        $rating->setUser($user);
        $user->setUserRating($rating);

        $this->userRepository->save();
    }

    public function removeForUser(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
