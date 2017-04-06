<?php

namespace EtoA\Core;

use Doctrine\DBAL\Connection;

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
        return $this->connection->createQueryBuilder();
    }
}
