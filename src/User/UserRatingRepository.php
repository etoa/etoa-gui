<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\AbstractRepository;

class UserRatingRepository extends AbstractRepository
{
    /**
     * @return UserDiplomacyRating[]
     */
    public function getDiplomacyRating(): array
    {
        $data = $this->createSpecialRatingQueryBuilder()
            ->addSelect('r.diplomacy_rating')
            ->orderBy('diplomacy_rating', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserDiplomacyRating($row), $data);
    }

    /**
     * @return UserBattleRating[]
     */
    public function getBattleRating(): array
    {
        $data = $this->createSpecialRatingQueryBuilder()
            ->addSelect('r.battle_rating, r.battles_lost, r.battles_won, r.battles_fought')
            ->orderBy('battle_rating', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserBattleRating($row), $data);
    }

    /**
     * @return UserTradeRating[]
     */
    public function getTradeRating(): array
    {
        $data = $this->createSpecialRatingQueryBuilder()
            ->addSelect('r.trade_rating, r.trades_buy, r.trades_sell')
            ->orderBy('trade_rating', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserTradeRating($row), $data);
    }

    private function createSpecialRatingQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->select('u.user_id', 'u.user_nick', 'ra.race_name', 'a.alliance_tag')
            ->from('user_ratings', 'r')
            ->innerJoin('r', 'users', 'u', 'u.user_id = r.id')
            ->innerJoin('u', 'races', 'ra', 'u.user_race_id = ra.race_id')
            ->leftJoin('u', 'alliances', 'a', 'u.user_alliance_id = a.alliance_id');
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
