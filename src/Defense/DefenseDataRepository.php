<?php

namespace EtoA\Defense;

use Doctrine\DBAL\Query\QueryBuilder;

class DefenseDataRepository
{
    /** @var QueryBuilder */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Returns an array of defense names indexed by the defense id.
     *
     * @return string[]
     */
    public function getDefenseNames()
    {
        return $this->queryBuilder
            ->select('def_id, def_name')
            ->from('defense')
            ->orderBy('def_name')
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
