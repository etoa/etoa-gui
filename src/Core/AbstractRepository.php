<?php declare(strict_types=1);

namespace EtoA\Core;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    protected function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return int[]
     */
    protected function fetchIds(string $table, string $idField): array
    {
        $data = $this->createQueryBuilder()
            ->select($idField)
            ->from($table)
            ->execute()
            ->fetchFirstColumn();

        return array_map(fn ($val) => (int) $val, $data);
    }

    /**
     * @return array<int, string>
     */
    protected function fetchIdsWithNames(string $table, string $idField, string $nameField, bool $orderById = false): array
    {
        return $this->createQueryBuilder()
            ->select($idField, $nameField)
            ->from($table)
            ->orderBy($orderById ? $idField : $nameField)
            ->execute()
            ->fetchAllKeyValue();
    }
}
