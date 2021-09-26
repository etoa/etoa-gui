<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\OptimizeTablesTask;
use EtoA\Support\DB\DatabaseManagerRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OptimizeTablesHandler implements MessageHandlerInterface
{
    private DatabaseManagerRepository $databaseManager;
    private LogRepository $logRepository;

    public function __construct(DatabaseManagerRepository $databaseManager, LogRepository $logRepository)
    {
        $this->databaseManager = $databaseManager;
        $this->logRepository = $logRepository;
    }

    public function __invoke(OptimizeTablesTask $task): SuccessResult
    {
        $result = $this->databaseManager->optimizeTables();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, count($result) . " Tabellen wurden optimiert!");

        return SuccessResult::create("Tabellen optimiert");
    }
}
