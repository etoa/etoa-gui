<?php

namespace EtoA\Core;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractRepository
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function createQueryBuilder()
    {
        return new QueryBuilder($this->connection);
    }
}
