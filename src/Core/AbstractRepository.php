<?php declare(strict_types=1);

namespace EtoA\Core;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\Database\AbstractSearch;
use EtoA\Core\Database\AbstractSort;

abstract class AbstractRepository
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

    /**
     * @return int[]
     * @throws Exception
     */
    protected function fetchIds(string $table, string $idField): array
    {
        $data = $this->createQueryBuilder()
            ->select($idField)
            ->from($table)
            ->fetchFirstColumn();

        return array_map(fn($val) => (int)$val, $data);
    }

    /**
     * @return array<int, string>
     * @throws Exception
     */
    protected function fetchIdsWithNames(string $table, string $idField, string $nameField, bool $orderById = false): array
    {
        return $this->createQueryBuilder()
            ->select($idField, $nameField)
            ->from($table)
            ->orderBy($orderById ? $idField : $nameField)
            ->fetchAllKeyValue();
    }

    protected function applySearchSortLimit(QueryBuilder $qb, AbstractSearch $search = null, AbstractSort $sorts = null, int $limit = null, int $offset = null): QueryBuilder
    {
        if ($search !== null) {
            $qb->setParameters($search->parameters);
            foreach ($search->stringArrayParameters as $parameter => $value) {
                $qb->setParameter($parameter, $value, ArrayParameterType::STRING);
            }
            foreach ($search->parts as $query) {
                $qb->andWhere($query);
            }
        }

        if ($sorts !== null) {
            foreach ($sorts->sorts as $sort => $order) {
                $qb->addOrderBy($sort, $order);
            }
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }
}
