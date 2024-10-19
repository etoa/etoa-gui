<?php

declare(strict_types=1);

namespace EtoA\Support\DB;

use EtoA\Core\AbstractRepository;

class SchemaMigrationRepository extends AbstractRepository
{
    const SCHEMA_MIGRATIONS_TABLE = "schema_migrations";

    public function hasMigrationTable(): bool
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('information_schema.TABLES')
            ->where('table_schema = :db')
            ->andWhere('table_name = :table')
            ->setParameters([
                'db' => $this->getConnection()->getDatabase(),
                'table' => self::SCHEMA_MIGRATIONS_TABLE,
            ])
            ->fetchAssociative();

        return $data !== false;
    }

    /**
     * @return array<array{version: string, date: string}>
     */
    public function getMigrations(): array
    {
        return $this->createQueryBuilder('q')
            ->select("version", "date")
            ->from(self::SCHEMA_MIGRATIONS_TABLE)
            ->orderBy('version')
            ->fetchAllAssociative();
    }

    public function getMigrationDate(string $version): ?string
    {
        $date = $this->createQueryBuilder('q')
            ->select("date")
            ->from(self::SCHEMA_MIGRATIONS_TABLE)
            ->where('version = :version')
            ->setParameter('version', $version)
            ->fetchOne();

        return $date !== false ? $date : null;
    }

    public function addMigration(string $version): void
    {
        $this->createQueryBuilder('q')
            ->insert(self::SCHEMA_MIGRATIONS_TABLE)
            ->values([
                'version' => ':version',
                'date' => 'CURRENT_TIMESTAMP',
            ])
            ->setParameter('version', $version)
            ->executeQuery();
    }
}
