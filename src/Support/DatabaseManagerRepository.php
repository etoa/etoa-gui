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

    public function truncateTables(array $tables)
    {
        $this->getConnection()
            ->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tables as $t) {
            $this->getConnection()
                ->executeStatement('TRUNCATE :table;',
            [
                'table' => $t,
            ]);
        }

        $this->getConnection()
            ->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
