<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\AnalyzeTablesTask;
use EtoA\Support\DB\DatabaseManagerRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AnalyzeTablesHandler implements MessageHandlerInterface
{
    private LogRepository $logRepository;
    private DatabaseManagerRepository $databaseManager;

    public function __construct(LogRepository $logRepository, DatabaseManagerRepository $databaseManager)
    {
        $this->logRepository = $logRepository;
        $this->databaseManager = $databaseManager;
    }

    public function __invoke(AnalyzeTablesTask $task): SuccessResult
    {
        $result = $this->databaseManager->analyzeTables();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, count($result) . " Tabellen wurden analysiert!");

        return SuccessResult::create("Tabellen analysiert");
    }
}
