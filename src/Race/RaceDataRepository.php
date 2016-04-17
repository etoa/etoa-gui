<?php

namespace EtoA\Race;

use Doctrine\DBAL\Query\QueryBuilder;

class RaceDataRepository
{
    /** @var QueryBuilder */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Returns an array of race names indexed by the race id.
     *
     * @return string[]
     */
    public function getRaceNames()
    {
        return $this->queryBuilder
            ->select('race_id, race_name')
            ->from('races')
            ->orderBy('race_name')
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
