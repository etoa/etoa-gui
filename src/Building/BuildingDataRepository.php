<?php

namespace EtoA\Building;

use Doctrine\DBAL\Query\QueryBuilder;

class BuildingDataRepository
{
    /** @var QueryBuilder */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Returns an array of building names indexed by the building id.
     *
     * @return string[]
     */
    public function getBuildingNames()
    {
        return $this->queryBuilder
            ->select('building_id, building_name')
            ->from('buildings')
            ->orderBy('building_name')
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
