<?php

declare(strict_types=1);

namespace EtoA\Support;

class DatabaseMigrationService
{
    private DatabaseManagerRepository $databaseManagerRepository;

    public function __construct(DatabaseManagerRepository $databaseManagerRepository)
    {
        $this->databaseManagerRepository = $databaseManagerRepository;
    }

    /**
     * @return string[]
     */
    public function getPendingMigrations(): array
    {
        $files = glob(RELATIVE_ROOT . '../db/migrations/*.sql');
        natsort($files);
        $migrations = [];
        foreach ($files as $f) {
            $pi = pathinfo($f, PATHINFO_FILENAME);
            $date = $this->databaseManagerRepository->getMigrationDate($pi);
            if ($date === null) {
                $migrations[] = $pi;
            }
        }

        return $migrations;
    }

    public function migrate(): int
    {
        if (!$this->databaseManagerRepository->hasMigrationTable()) {
            $this->databaseManagerRepository->loadFile(RELATIVE_ROOT . '../db/init_schema_migrations.sql');
        }

        $files = glob(RELATIVE_ROOT . '../db/migrations/*.sql');
        natsort($files);
        $cnt = 0;
        foreach ($files as $f) {
            $pi = pathinfo($f, PATHINFO_FILENAME);
            $date = $this->databaseManagerRepository->getMigrationDate($pi);
            if ($date === null) {
                echo $pi . "\n";
                $this->databaseManagerRepository->loadFile($f);
                $this->databaseManagerRepository->addMigration($pi);
                $cnt++;
            }
        }

        return $cnt;
    }
}
