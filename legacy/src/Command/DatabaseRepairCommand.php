<?php declare(strict_types=1);

namespace EtoA\Command;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseManagerRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseRepairCommand extends Command
{
    protected static $defaultName = 'database:repair';
    protected static $defaultDescription = 'Repair all database tables';

    private LogRepository $logRepository;
    private DatabaseManagerRepository $databaseManagerRepository;

    public function __construct(LogRepository $logRepository, DatabaseManagerRepository $databaseManagerRepository)
    {
        $this->logRepository = $logRepository;
        $this->databaseManagerRepository = $databaseManagerRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->databaseManagerRepository->repairTables();
        $io->table(['Table', 'Action', 'Status', 'Message'], $result);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, count($result) . " Tabellen wurden manuell repariert!");

        return Command::SUCCESS;
    }
}
