<?php declare(strict_types=1);

namespace EtoA\Command;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Support\DB\DatabaseMigrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;

class DatabaseMigrateCommand extends Command
{
    protected static $defaultName = 'database:migrate';
    protected static $defaultDescription = 'Apply all database migrations';

    private DatabaseMigrationService $databaseMigrationService;
    private DatabaseManagerRepository $databaseManagerRepository;
    private LockFactory $lockFactory;
    private ConfigurationService $config;

    public function __construct(DatabaseMigrationService $databaseMigrationService, DatabaseManagerRepository $databaseManagerRepository, LockFactory $lockFactory, ConfigurationService $config)
    {
        $this->databaseMigrationService = $databaseMigrationService;
        $this->databaseManagerRepository = $databaseManagerRepository;
        $this->lockFactory = $lockFactory;
        $this->config = $config;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('reset', null, InputOption::VALUE_NONE, 'Reset the database first');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $lock = $this->lockFactory->createLock('db');

        try {
            $lock->acquire(true);

            if ((bool) $input->getOption('reset')) {
                $io->writeln("Dropping all tables:");
                $this->databaseManagerRepository->dropAllTables();
            }

            $io->writeln("Migrate database:");
            $cnt = $this->databaseMigrationService->migrate();
            if ($cnt === 0) {
                $io->writeln("Database is up-to-date");
            }

            // Load config defaults
            if ((bool) $input->getOption('reset')) {
                $this->config->restoreDefaults();
                $this->config->reload();
            }

            return Command::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}
