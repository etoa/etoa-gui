<?php

declare(strict_types=1);

namespace EtoA\Support;

use EtoA\Core\AbstractRepository;

class SchemaMigrationRepository extends AbstractRepository
{
    const SCHEMA_MIGRATIONS_TABLE = "schema_migrations";

    public function hasMigrationTable(): bool
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('information_schema.TABLES')
            ->where('table_schema = :db')
            ->andWhere('table_name = :table')
            ->setParameters([
                'db' => $this->getConnection()->getDatabase(),
                'table' => self::SCHEMA_MIGRATIONS_TABLE,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false;
    }

    /**
     * @return array<array{version: string, date: string}>
     */
    public function getMigrations(): array
    {
        return $this->createQueryBuilder()
            ->select("version", "date")
            ->from(self::SCHEMA_MIGRATIONS_TABLE)
            ->orderBy('version')
            ->execute()
            ->fetchAllAssociative();
    }

    public function getMigrationDate(string $version): ?string
    {
        $date = $this->createQueryBuilder()
            ->select("date")
            ->from(self::SCHEMA_MIGRATIONS_TABLE)
            ->where('version = :version')
            ->setParameter('version', $version)
            ->execute()
            ->fetchOne();

        return $date !== false ? $date : null;
    }

    public function addMigration(string $version): void
    {
        $this->createQueryBuilder()
            ->insert(self::SCHEMA_MIGRATIONS_TABLE)
            ->values([
                'version' => ':version',
                'date' => 'CURRENT_TIMESTAMP',
            ])
            ->setParameter('version', $version)
            ->execute();
    }
}
