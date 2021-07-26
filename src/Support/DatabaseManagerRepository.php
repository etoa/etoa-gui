<?php

declare(strict_types=1);

namespace EtoA\Support;

use EtoA\Core\AbstractRepository;
use Exception;

class DatabaseManagerRepository extends AbstractRepository
{
    const SCHEMA_MIGRATIONS_TABLE = "schema_migrations";

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
     * @return string[]
     */
    public function getTables(): array
    {
        return $this->getConnection()->fetchFirstColumn('SHOW TABLES;');
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function analyzeTables(): array
    {
        $tables = $this->getTables();

        return $this->getConnection()->fetchAllAssociative("ANALYZE TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function checkTables(): array
    {
        $tables = $this->getTables();

        return $this->getConnection()->fetchAllAssociative("CHECK TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function optimizeTables(): array
    {
        $tables = $this->getTables();

        return $this->getConnection()->fetchAllAssociative("OPTIMIZE TABLE " . implode(',', $tables) . ";");
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function repairTables(): array
    {
        $tables = $this->getTables();

        return $this->getConnection()->fetchAllAssociative("REPAIR TABLE " . implode(',', $tables) . ";");
    }

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

    public function loadFile(string $file): void
    {
        $mysql = isWindowsOS() ? WINDOWS_MYSQL_PATH : "mysql";
        $mysqldump = isWindowsOS() ? WINDOWS_MYSQLDUMP_PATH : "mysqldump";
        if (file_exists($file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($ext == "gz") {
                if (!isUnixOS()) {
                    throw new Exception("Das Laden von GZIP SQL Dateien wird nur auf UNIX Systemen unterstützt!");
                }
                $cmd = "gunzip < " . $file . ".gz | " . $mysql . " -u" . $this->getConnection()->getParams()['user'] . " -p" . $this->getConnection()->getParams()['password'] . " -h" . $this->getConnection()->getParams()['host'] . " --default-character-set=utf8 " . $this->getConnection()->getDatabase();
            } else {
                $cmd = $mysql . " -u" . $this->getConnection()->getParams()['user'] . " -p" . $this->getConnection()->getParams()['password'] . " -h" . $this->getConnection()->getParams()['host'] . " --default-character-set=utf8 " . $this->getConnection()->getDatabase() . " < " . $file;
            }

            if (isWindowsOS() && !file_exists($mysql) || isUnixOS() && !unix_command_exists($mysqldump)) {
                $this->importFromFile($file);
            } else {
                $result = shell_exec($cmd);
                if (is_string($result) && strlen($result) > 0) {
                    throw new Exception("Error while loading file with MySQL: " . $result);
                }
            }
        } else {
            throw new Exception("File $file not found!");
        }
    }

    /**
     * Import SQL file using PHP functionality only
     */
    private function importFromFile(string $file, string $delimiter = ';'): void
    {
        set_time_limit(0);
        if (is_file($file) === true) {
            $file = fopen($file, 'r');
            if (is_resource($file) === true) {
                $query = array();
                while (feof($file) === false) {
                    $query[] = fgets($file);
                    if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1) {
                        $query = trim(implode('', $query));
                        $this->getConnection()->executeQuery($query);
                    }
                    if (is_string($query) === true) {
                        $query = array();
                    }
                }
                fclose($file);
            }
        }
    }

    public function restoreDB(string $backupDir, string $restorePoint): string
    {
        if (is_dir($backupDir)) {
            $file = $backupDir . "/" . $this->getConnection()->getDatabase() . "-" . $restorePoint . ".sql";
            if (file_exists($file . ".gz")) {
                $file = $file . ".gz";
            }
            $this->restoreDBFromFile($file);

            return "Die Datenbank wurde vom Backup [b]" . $restorePoint . "[/b] aus dem Verzeichnis [b]" . $backupDir . "[/b] wiederhergestellt.";
        } else {
            throw new Exception("Backup directory $backupDir does not exist!");
        }
    }

    private function restoreDBFromFile(string $file): string
    {
        if (file_exists($file)) {
            try {
                $this->loadFile($file);

                return "Die Datenbank wurde aus der Datei [b]" . $file . "[/b] wiederhergestellt.";
            } catch (Exception $e) {
                throw new Exception("Error while restoring backup: " . $e->getMessage());
            }
        } else {
            throw new Exception("Backup file $file not found!");
        }
    }


    public function backupDB(string $backupDir, bool $gzip): string
    {
        $mysqldump = isWindowsOS() ? WINDOWS_MYSQLDUMP_PATH : "mysqldump";

        if (is_dir($backupDir)) {
            $file = $backupDir . "/" . $this->getConnection()->getDatabase() . "-" . date("Y-m-d-H-i") . ".sql";
            if ($gzip) {
                if (!isUnixOS()) {
                    throw new Exception("Das Erstellen von GZIP Backups wird nur auf UNIX Systemen unterstützt!");
                }
                $file .= ".gz";
                $cmd = $mysqldump . " -u" . $this->getConnection()->getParams()['user'] . " -p" . $this->getConnection()->getParams()['password'] . " -h" . $this->getConnection()->getParams()['host'] . " --default-character-set=utf8 " . $this->getConnection()->getDatabase() . " | gzip > " . $file;
            } else {
                $cmd = $mysqldump . " -u" . $this->getConnection()->getParams()['user'] . " -p" . $this->getConnection()->getParams()['password'] . " -h" . $this->getConnection()->getParams()['host'] . " --default-character-set=utf8 " . $this->getConnection()->getDatabase() . " -r " . $file;
            }

            if (isWindowsOS() && !file_exists($mysqldump) || isUnixOS() && !unix_command_exists($mysqldump)) {
                $this->dumpIntoFile($file);
            } else {
                $result = shell_exec($cmd);
                if (is_string($result) && strlen($result) > 0) {
                    throw new Exception("Fehler beim Erstellen der Backup-Datei " . $file . ": " . $result);
                }
            }
            if (!file_exists($file)) {
                throw new Exception("Fehler beim Erstellen der Backup-Datei " . $file . ". Es wurde keine Datei erstellt.");
            }

            return "Backup " . $file . " erstellt, Dateigrösse: " . byte_format(filesize($file));
        } else {
            throw new Exception("Das Backup Verzeichnis " . $backupDir . " existiert nicht!");
        }
    }

    /**
     * Import database or tables to SQL file using PHP functionality only
     *
     * @param string[] $tables
     */
    private function dumpIntoFile(string $file, array $tables = []): void
    {
        if (count($tables) == 0) {
            $tables = $this->getTables();
        }

        $str = '';
        foreach ($tables as $table) {
            $result = $this->getConnection()->fetchAllAssociative('SELECT * FROM ' . $table);
            $fields = count($result) > 0 ? array_keys($result[0]) : [];
            $num_fields = count($fields);

            $str .= 'DROP TABLE ' . $table . ';';

            $row2 = $this->getConnection()->fetchNumeric('SHOW CREATE TABLE ' . $table);
            $str .= "\n\n" . $row2[1] . ";\n\n";

            if ($num_fields > 0) {
                $values = [];
                foreach ($result as $arr) {
                    $values[] = '("' . implode('", "', $arr) . '")';
                }
                $str .= 'INSERT INTO `' . $table . '` (`' . implode('`, `', $fields) . '`) VALUES ' . implode(',', $values) . ';';
            }

            $str .= "\n\n\n";
        }

        file_put_contents($file, $str);
    }

    /**
     * @return string[]
     */
    public function getBackupImages(string $dir, bool $strip = true): array
    {
        $backupFiles = array();
        if ($dir != null && is_dir($dir)) {
            if ($d = opendir($dir)) {
                while ($f = readdir($d)) {
                    if (is_file($dir . "/" . $f) && stristr($f, ".sql") && preg_match('/^' . $this->getConnection()->getDatabase() . '/i', $f) == 1) {
                        if (!$strip) {
                            array_push($backupFiles, $f);
                        } else {
                            array_push($backupFiles, preg_replace(array('/\.sql(.gz)?$/', '/^' . $this->getConnection()->getDatabase() . '-/'), array('', ''), $f));
                        }
                    }
                }
                rsort($backupFiles);
            }
        }

        return $backupFiles;
    }
}
