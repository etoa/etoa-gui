<?php

declare(strict_types=1);

namespace EtoA\Support\DB;

use EtoA\Core\AbstractRepository;

class DatabaseManagerRepository extends AbstractRepository
{
    public function getDatabaseSize(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('round(sum( data_length + index_length ) / 1024 / 1024,2)')
            ->from('information_schema.TABLES')
            ->where('table_schema = :database')
            ->groupBy('table_schema')
            ->setParameter('database', $this->getDatabaseName())
            ->fetchOne();
    }

    public function getDatabasePlatform(): string
    {
        return $this->getConnection()->getDatabasePlatform() ? get_class($this->getConnection()->getDatabasePlatform()) : '';
    }

    public function getDatabaseName(): string
    {
        return (string) $this->getConnection()->getDatabase();
    }

    public function getUser(): string
    {
        return $this->getConnection()->getParams()['user'];
    }

    public function getPassword(): string
    {
        return $this->getConnection()->getParams()['password'];
    }

    public function getHost(): string
    {
        return explode(':', $this->getConnection()->getParams()['host'])[0];
    }

    public function getPort(): int
    {
        $arr = explode(':', $this->getConnection()->getParams()['host'], 2);
        if (count($arr) > 1) {
            return (int) $arr[1];
        }

        if (isset($this->getConnection()->getParams()['port'])) {
            return $this->getConnection()->getParams()['port'];
        }

        return 3306;
    }

    /**
     * @param string[] $tables
     */
    public function truncateTables(array $tables): void
    {
        $this->getConnection()
            ->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tables as $t) {
            $this->getConnection()
                ->executeStatement('TRUNCATE ' . $t . ';');
        }

        $this->getConnection()
            ->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * @return array<string, string|int>
     */
    public function getGlobalStatus(): array
    {
        $data = $this->getConnection()->fetchAllAssociative('SHOW GLOBAL STATUS');

        $result = [];
        foreach ($data as $row) {
            $result[strtolower($row['Variable_name'])] = is_numeric($row['Value']) ? (int) $row['Value'] : $row['Value'];
        }

        return $result;
    }

    /**
     * @return array<array{Name: string, Rows: string, Data_length: string, Index_length: string, Engine: string}>
     */
    public function getTableStatus(): array
    {
        return $this->getConnection()->fetchAllAssociative('SHOW TABLE STATUS');
    }

    /**
     * @return string[]
     */
    public function getTables(): array
    {
        return $this->getConnection()->fetchFirstColumn('SHOW TABLES;');
    }

    public function getCreateTableStatement(string $table): string
    {
        return $this->getConnection()->fetchNumeric('SHOW CREATE TABLE ' . $table)[1];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function selectAllFromTable(string $table): array
    {
        return $this->getConnection()->fetchAllAssociative('SELECT * FROM ' . $table);
    }

    public function executeQuery(string $query): void
    {
        $this->getConnection()->executeQuery($query);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function analyzeTables(): array
    {
        $tables = $this->getTables();
        if (count($tables) == 0) {
            return [];
        }

        return $this->getConnection()->fetchAllAssociative("ANALYZE TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function checkTables(): array
    {
        $tables = $this->getTables();
        if (count($tables) == 0) {
            return [];
        }

        return $this->getConnection()->fetchAllAssociative("CHECK TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function optimizeTables(): array
    {
        $tables = $this->getTables();
        if (count($tables) == 0) {
            return [];
        }

        return $this->getConnection()->fetchAllAssociative("OPTIMIZE TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function repairTables(): array
    {
        $tables = $this->getTables();
        if (count($tables) == 0) {
            return [];
        }

        return $this->getConnection()->fetchAllAssociative("REPAIR TABLE " . implode(',', $tables) . ";");
    }

    public function dropAllTables(): int
    {
        $tables = $this->getTables();
        if (count($tables) > 0) {
            $this->getConnection()->executeStatement("SET FOREIGN_KEY_CHECKS=0;");
            $this->getConnection()->executeStatement("DROP TABLE " . implode(',', $tables) . ";");
            $this->getConnection()->executeStatement("SET FOREIGN_KEY_CHECKS=1;");
        }

        return count($tables);
    }
}
