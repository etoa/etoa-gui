<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserStatRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('user_stats')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return UserStat[]
     */
    public function searchStats(UserStatSearch $search, UserRatingSort $sort = null, int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('id', 'nick', 'blocked', 'hmod', 'inactive', 'race_name', 'alliance_tag', 'sx', 'sy')
            ->addSelect($search->order . ' AS rank')
            ->addSelect($search->field . ' AS points')
            ->addSelect($search->shift . ' AS shift')
            ->from('user_stats');

        if ($sort == null || count($sort->sorts) === 0) {
            $qb
                ->orderBy($search->order, 'ASC')
                ->addOrderBy('nick', 'ASC');
        }

        $data = $this->applySearchSortLimit($qb, $search, $sort, $limit, $offset)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserStat($row), $data);
    }
}
