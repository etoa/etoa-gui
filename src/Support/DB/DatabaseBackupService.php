<?php

declare(strict_types=1);

namespace EtoA\Support\DB;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use Exception;

class DatabaseBackupService
{
    private DatabaseManagerRepository $databaseManagerRepository;
    private ConfigurationService $config;

    public function __construct(
        DatabaseManagerRepository $databaseManagerRepository,
        ConfigurationService $config
    ) {
        $this->databaseManagerRepository = $databaseManagerRepository;
        $this->config = $config;
    }

    public function getBackupDir(): ?string
    {
        $backupDir = $this->config->get('backup_dir');
        if ($backupDir) {
            if (is_dir($backupDir)) {
                return $backupDir;
            }
        } else {
            $backupDir = __DIR__ . '/../../../backup';
            if (is_dir($backupDir)) {
                return $backupDir;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getBackupImages(string $dir, bool $strip = true): array
    {
        $backupFiles = [];
        if ($dir != null && is_dir($dir)) {
            if ($d = opendir($dir)) {
                while ($f = readdir($d)) {
                    if (is_file($dir . "/" . $f) && stristr($f, ".sql") && preg_match('/^' . $this->databaseManagerRepository->getDatabaseName() . '/i', $f) == 1) {
                        if (!$strip) {
                            array_push($backupFiles, $f);
                        } else {
                            array_push($backupFiles, preg_replace(['/\.sql(.gz)?$/', '/^' . $this->databaseManagerRepository->getDatabaseName() . '-/'], ['', ''], $f));
                        }
                    }
                }
                rsort($backupFiles);
            }
        }

        return $backupFiles;
    }

    public function loadFile(string $file): void
    {
        $mysql = isWindowsOS() ? "c:\\xampp\\mysql\\bin\\mysql.exe" : "mysql";
        $mysqldump = isWindowsOS() ? "c:\\xampp\\mysql\\bin\\mysqldump.exe" : "mysqldump";
        if (file_exists($file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($ext == "gz") {
                if (!isUnixOS()) {
                    throw new Exception("Das Laden von GZIP SQL Dateien wird nur auf UNIX Systemen unterstützt!");
                }
                $cmd = "gunzip < " . $file . ".gz | " . $mysql .
                    " -u" . $this->databaseManagerRepository->getUser() .
                    " -p" . $this->databaseManagerRepository->getPassword() .
                    " -h" . $this->databaseManagerRepository->getHost() .
                    " -P" . $this->databaseManagerRepository->getPort() .
                    " --default-character-set=utf8
                    " . $this->databaseManagerRepository->getDatabaseName();
            } else {
                $cmd = $mysql .
                    " -u" . $this->databaseManagerRepository->getUser() .
                    " -p" . $this->databaseManagerRepository->getPassword() .
                    " -h" . $this->databaseManagerRepository->getHost() .
                    " -P" . $this->databaseManagerRepository->getPort() .
                    " --default-character-set=utf8 " . $this->databaseManagerRepository->getDatabaseName() .
                    " < " . $file;
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
                $query = [];
                while (feof($file) === false) {
                    $query[] = fgets($file);
                    if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1) {
                        $query = trim(implode('', $query));
                        $this->databaseManagerRepository->executeQuery($query);
                    }
                    if (is_string($query) === true) {
                        $query = [];
                    }
                }
                fclose($file);
            }
        }
    }

    public function restoreDB(string $backupDir, string $restorePoint): string
    {
        if (is_dir($backupDir)) {
            $file = $backupDir . "/" . $this->databaseManagerRepository->getDatabaseName() . "-" . $restorePoint . ".sql";
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
        $mysqldump = isWindowsOS() ? "c:\\xampp\\mysql\\bin\\mysqldump.exe" : "mysqldump";

        if (is_dir($backupDir)) {
            $file = $backupDir . "/" . $this->databaseManagerRepository->getDatabaseName() . "-" . date("Y-m-d-H-i") . ".sql";
            if ($gzip) {
                if (!isUnixOS()) {
                    throw new Exception("Das Erstellen von GZIP Backups wird nur auf UNIX Systemen unterstützt!");
                }
                $file .= ".gz";
                $cmd = $mysqldump .
                    " -u" . $this->databaseManagerRepository->getUser() .
                    " -p" . $this->databaseManagerRepository->getPassword() .
                    " -h" . $this->databaseManagerRepository->getHost() .
                    " -P" . $this->databaseManagerRepository->getPort() .
                    " -y " .
                    " --default-character-set=utf8 " . $this->databaseManagerRepository->getDatabaseName() .
                    " | gzip > " . $file;
            } else {
                $cmd = $mysqldump .
                    " -u" . $this->databaseManagerRepository->getUser() .
                    " -p" . $this->databaseManagerRepository->getPassword() .
                    " -h" . $this->databaseManagerRepository->getHost() .
                    " -P" . $this->databaseManagerRepository->getPort() .
                    " -y " .
                    " --default-character-set=utf8 " . $this->databaseManagerRepository->getDatabaseName() .
                    " -r " . $file;
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

            return "Backup " . $file . " erstellt, Dateigrösse: " . StringUtils::formatBytes(filesize($file));
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
            $tables = $this->databaseManagerRepository->getTables();
        }

        $str = '';
        foreach ($tables as $table) {
            $result = $this->databaseManagerRepository->selectAllFromTable($table);
            $fields = count($result) > 0 ? array_keys($result[0]) : [];
            $num_fields = count($fields);

            $str .= 'DROP TABLE ' . $table . ';';

            $str .= "\n\n" . $this->databaseManagerRepository->getCreateTableStatement($table) . ";\n\n";

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
     * Removes old backup files
     * @return int The number of removed files
     */
    public function removeOldBackups(string $dir, int $days): int
    {
        $deleted = 0;
        $time = time();
        $files = array_merge(glob($dir . "/*.sql"), glob($dir . "/*.sql.gz"));
        foreach ($files as $f) {
            if (is_file($f) && $time - filemtime($f) >= 86400 * $days) {
                unlink($f);
                $deleted++;
            }
        }

        return $deleted;
    }
}
