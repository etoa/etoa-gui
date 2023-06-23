<?php declare(strict_types=1);

namespace EtoA\Command;

use EtoA\Support\DB\DatabaseManagerRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseCheckCommand extends Command
{
    protected static $defaultName = 'database:check';
    protected static $defaultDescription = 'Check all database tables';

    private DatabaseManagerRepository $databaseManagerRepository;

    public function __construct(DatabaseManagerRepository $databaseManagerRepository)
    {
        $this->databaseManagerRepository = $databaseManagerRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->databaseManagerRepository->checkTables();
        $io->table(['Table', 'Action', 'Status', 'Message'], $result);

        return Command::SUCCESS;
    }
}
