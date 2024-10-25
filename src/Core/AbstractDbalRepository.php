<?php

namespace EtoA\Core;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class AbstractDbalRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    protected function getConnection(): Connection
    {
        return $this->connection;
    }
}