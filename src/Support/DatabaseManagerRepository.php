<?php

declare(strict_types=1);

namespace EtoA\Support;

use EtoA\Core\AbstractRepository;

class DatabaseManagerRepository extends AbstractRepository
{
	public function getDatabaseSize(): int {
        $database = $this->getConnection()->getDatabase();
        return (int) $this->getConnection()
            ->executeQuery(
                "SELECT round(sum( data_length + index_length ) / 1024 / 1024,2)
                FROM information_schema.TABLES
                WHERE table_schema=?
                GROUP BY table_schema",
                [$database]
            )
            ->fetchOne();
	}

    public function getDatabasePlatform(): string
    {
        return $this->getConnection()->getDatabasePlatform()->getName();
    }
}
