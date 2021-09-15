<?php declare(strict_types=1);

namespace EtoA\Command;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseBackupService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;

class DatabaseBackupCommand extends Command
{
    protected static $defaultName = 'database:backup';
    protected static $defaultDescription = 'Backup database';

    private LogRepository $logRepository;
    private LockFactory $lockFactory;
    private ConfigurationService $config;
    private DatabaseBackupService $databaseBackupService;

    public function __construct(LogRepository $logRepository, LockFactory $lockFactory, ConfigurationService $config, DatabaseBackupService $databaseBackupService)
    {
        $this->logRepository = $logRepository;
        $this->lockFactory = $lockFactory;
        $this->config = $config;
        $this->databaseBackupService = $databaseBackupService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dir = $this->databaseBackupService->getBackupDir();
        $gzip = $this->config->getBoolean('backup_use_gzip');

        $lock = $this->lockFactory->createLock('db');

        $lock->acquire(true);

        try {
            $log = $this->databaseBackupService->backupDB($dir, $gzip);

            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "[b]Datenbank-Backup Skript[/b]\n".$log);

            if ($io->isVerbose()) {
                $io->writeln($log);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Backup Skript[/b]\nDie Datenbank konnte nicht in das Verzeichnis [b]".$dir."[/b] gesichert werden: ".$e->getMessage());

            throw $e;
        } finally {
            $lock->release();
        }
    }
}
