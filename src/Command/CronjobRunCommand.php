<?php declare(strict_types=1);

namespace EtoA\Command;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\PeriodicTask\EnvelopResultExtractor;
use EtoA\PeriodicTask\PeriodicTaskCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class CronjobRunCommand extends Command
{
    protected static $defaultName = 'cronjob:run';
    protected static $defaultDescription = 'Run tasks based on cronjob schedule';

    private PeriodicTaskCollection $collection;
    private ConfigurationService $config;
    private LockFactory $lockFactory;
    private MessageBusInterface $messageBus;
    private LogRepository $logRepository;

    public function __construct(PeriodicTaskCollection $collection, ConfigurationService $config, LockFactory $lockFactory, MessageBusInterface $messageBus, LogRepository $logRepository)
    {
        $this->collection = $collection;
        $this->config = $config;
        $this->lockFactory = $lockFactory;
        $this->messageBus = $messageBus;
        $this->logRepository = $logRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch(true);

        $io = new SymfonyStyle($input, $output);

        if (!$this->config->getBoolean('update_enabled')) {
            $io->writeln('Cronjob is currently not enabled');

            return Command::FAILURE;
        }

        $lock = $this->lockFactory->createLock('task-runner');
        $lock->acquire(true);

        $now = time();

        try {
            $stopwatch->start('run');

            $log = '';

            foreach ($this->collection->getScheduledTasks($now) as $task) {
                $taskName = (new \ReflectionClass($task))->getShortName();
                $stopwatch->start($taskName);
                $envelope = $this->messageBus->dispatch($task);
                $taskStopEvent = $stopwatch->stop($taskName);
                $result = EnvelopResultExtractor::extract($envelope);
                $takLog = $taskName . ': ' . $result->getMessage() . ' - ' . $taskStopEvent->__toString();
                $log .= $takLog . "\n";

                if ($io->isVerbose()) {
                    $io->writeln($takLog);
                }
            }

            $stopEvent = $stopwatch->stop('run');
            $log .= $stopEvent->__toString();

            $severity = $stopEvent->getDuration() > 30000 ? LogSeverity::WARNING : LogSeverity::DEBUG;

            $text = "Periodische Tasks (".date("d.m.Y H:i:s", $now)."):\n\n".$log;
            $this->logRepository->add(LogFacility::UPDATES, $severity, $text);

            return Command::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}
