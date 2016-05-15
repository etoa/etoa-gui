<?php

namespace EtoA\Ship;

use Doctrine\DBAL\Query\QueryBuilder;

class ShipDataRepository
{
    /** @var QueryBuilder */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Returns an array of ship names indexed by the ship id.
     *
     * @return \string[]
     */
    public function getShipNames()
    {
        return $this->queryBuilder
            ->select('ship_id, ship_name')
            ->from('ships')
            ->orderBy('ship_name')
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
