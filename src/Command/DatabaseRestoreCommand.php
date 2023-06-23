<?php declare(strict_types=1);

namespace EtoA\Command;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseBackupService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;

class DatabaseRestoreCommand extends Command
{
    protected static $defaultName = 'database:restore';
    protected static $defaultDescription = 'Restore database from backup';

    private LogRepository $logRepository;
    private DatabaseBackupService $databaseBackupService;
    private LockFactory $lockFactory;

    public function __construct(LogRepository $logRepository, DatabaseBackupService $databaseBackupService, LockFactory $lockFactory)
    {
        $this->logRepository = $logRepository;
        $this->databaseBackupService = $databaseBackupService;
        $this->lockFactory = $lockFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('restore_point', InputArgument::OPTIONAL, 'Reset the database first');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dir = $this->databaseBackupService->getBackupDir();

        if (!(bool) $input->getArgument('restore_point')) {
            $io->writeln("Available restore points:");
            $dates = $this->databaseBackupService->getBackupImages($dir);
            foreach ($dates as $f) {
                $io->writeln($f);
            }

            return Command::SUCCESS;
        }

        $restorePoint = $input->getArgument('restore_point');

        $lock = $this->lockFactory->createLock('db');
        $lock->acquire(true);

        try {
            $log = $this->databaseBackupService->restoreDB($dir, $restorePoint);

            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "[b]Datenbank-Restore Skript[/b]\n".$log);

            if ($io->isVerbose()) {
                $io->writeln($log);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Restore Skript[/b]\nDie Datenbank konnte nicht vom Backup [b]".$restorePoint."[/b] aus dem Verzeichnis [b]".$dir."[/b] wiederhergestellt werden: ".$e->getMessage());

            throw $e;
        } finally {
            $lock->release();
        }
    }
}
