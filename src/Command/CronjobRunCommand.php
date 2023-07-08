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

    public function __construct(
        private readonly PeriodicTaskCollection $collection,
        private readonly ConfigurationService   $config,
        private readonly LockFactory            $lockFactory,
        private readonly MessageBusInterface    $messageBus,
        private readonly LogRepository          $logRepository,
    )
    {
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
                $takLog = str_replace('default/', '', $taskStopEvent->__toString()) . ': ' . $result->getMessage();
                $log .= $takLog . "\n";

                if ($io->isVerbose()) {
                    $io->writeln($takLog);
                }
            }

            $stopEvent = $stopwatch->stop('run');
            $log .= str_replace('default/', '', $stopEvent->__toString());

            $severity = $stopEvent->getDuration() > 30000 ? LogSeverity::WARNING : LogSeverity::DEBUG;

            $text = "Periodische Tasks (" . date("d.m.Y H:i:s", $now) . "):\n\n" . $log;
            $this->logRepository->add(LogFacility::UPDATES, $severity, $text);

            return Command::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}
