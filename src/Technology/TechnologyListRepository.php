<?php

namespace EtoA\Technology;

use Doctrine\DBAL\Query\QueryBuilder;

class TechnologyListRepository
{
    /** @var QueryBuilder */
    private $queryBuilder;

    /**
     * @param int $userId
     *
     * @return int[]
     */
    public function getTechnologyLevels($userId)
    {
        return $this->queryBuilder
            ->select('techlist_tech_id, techlist_current_level')
            ->from('techlist')
            ->where('techlist_user_id = :entityId')
            ->setParameter('entityId', $userId)
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
