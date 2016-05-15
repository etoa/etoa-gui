<?php

namespace EtoA\Technology;

use Doctrine\DBAL\Query\QueryBuilder;

class TechnologyDataRepository
{
    /** @var QueryBuilder */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Returns an array of technology names indexed by the technology id.
     *
     * @return string[]
     */
    public function getTechnologyNames()
    {
        return $this->queryBuilder
            ->select('tech_id, tech_name')
            ->from('technologies')
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
