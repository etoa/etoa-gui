<?php

declare(strict_types=1);

namespace EtoA\Support;

use EtoA\Core\AbstractRepository;

class DatabaseManagerRepository extends AbstractRepository
{
    public function getDatabaseSize(): int
    {
        $database = $this->getConnection()->getDatabase();

        return (int) $this->createQueryBuilder()
            ->select('round(sum( data_length + index_length ) / 1024 / 1024,2)')
            ->from('information_schema.TABLES')
            ->where('table_schema = :database')
            ->groupBy('table_schema')
            ->setParameter('database', $database)
            ->execute()
            ->fetchOne();
    }

    public function getDatabasePlatform(): string
    {
        return $this->getConnection()->getDatabasePlatform()->getName();
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
            $result[strtolower($row['Variable_name'])] = $row['Value'];
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
     * @return array<int,array<string,mixed>>
     */
    public function analyzeTables(): array
    {
        $tables = $this->getConnection()->fetchFirstColumn('SHOW TABLES;');

        return $this->getConnection()->fetchAllAssociative("ANALYZE TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function checkTables(): array
    {
        $tables = $this->getConnection()->fetchFirstColumn('SHOW TABLES;');

        return $this->getConnection()->fetchAllAssociative("CHECK TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function optimizeTables(): array
    {
        $tables = $this->getConnection()->fetchFirstColumn('SHOW TABLES;');

        return $this->getConnection()->fetchAllAssociative("OPTIMIZE TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function repairTables(): array
    {
        $tables = $this->getConnection()->fetchFirstColumn('SHOW TABLES;');

        return $this->getConnection()->fetchAllAssociative("REPAIR TABLE " . implode(',', $tables) . ";");
    }
}
