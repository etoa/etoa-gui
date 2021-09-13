<?php

declare(strict_types=1);

namespace EtoA\Support\DB;

class DatabaseMigrationService
{
    private SchemaMigrationRepository $schemaMigrationRepository;
    private DatabaseBackupService $databaseBackupService;

    public function __construct(
        SchemaMigrationRepository $schemaMigrationRepository,
        DatabaseBackupService $databaseBackupService
    ) {
        $this->schemaMigrationRepository = $schemaMigrationRepository;
        $this->databaseBackupService = $databaseBackupService;
    }

    /**
     * @return string[]
     */
    public function getPendingMigrations(): array
    {
        $files = glob($this->getDatabaseDirectory() . 'migrations/*.sql');
        natsort($files);
        $migrations = [];
        foreach ($files as $f) {
            $pi = pathinfo($f, PATHINFO_FILENAME);
            $date = $this->schemaMigrationRepository->getMigrationDate($pi);
            if ($date === null) {
                $migrations[] = $pi;
            }
        }

        return $migrations;
    }

    public function migrate(): int
    {
        if (!$this->schemaMigrationRepository->hasMigrationTable()) {
            $this->databaseBackupService->loadFile($this->getDatabaseDirectory() . 'db/init_schema_migrations.sql');
        }

        $files = glob($this->getDatabaseDirectory() .'migrations/*.sql');
        natsort($files);
        $cnt = 0;
        foreach ($files as $f) {
            $pi = pathinfo($f, PATHINFO_FILENAME);
            $date = $this->schemaMigrationRepository->getMigrationDate($pi);
            if ($date === null) {
                echo $pi . "\n";
                $this->databaseBackupService->loadFile($f);
                $this->schemaMigrationRepository->addMigration($pi);
                $cnt++;
            }
        }

        return $cnt;
    }

    private function getDatabaseDirectory(): string
    {
        return __DIR__ . '/../../../db/';
    }
}
