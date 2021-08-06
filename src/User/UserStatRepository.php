<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserStatRepository extends AbstractRepository
{
    /**
     * @return UserStat[]
     */
    public function searchStats(UserStatSearch $search): array
    {
        $qb = $this->createQueryBuilder()
            ->select('id', 'nick', 'blocked', 'hmod', 'inactive', 'race_name', 'alliance_tag', 'sx', 'sy')
            ->addSelect($search->order . ' AS rank')
            ->addSelect($search->field . ' AS points')
            ->addSelect($search->shift . ' AS shift')
            ->from('user_stats')
            ->orderBy($search->order, 'ASC')
            ->addOrderBy('nick', 'ASC')
        ;

        $data = $this->applySearchSortLimit($qb, $search)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserStat($row), $data);
    }
}
