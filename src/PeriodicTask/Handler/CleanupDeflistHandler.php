<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Defense\DefenseRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CleanupDeflistTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CleanupDeflistHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly DefenseRepository $defenseRepository,
        private readonly LogRepository $logRepository)
    {}

    public function __invoke(CleanupDeflistTask $task): SuccessResult
    {
        $nr = $this->defenseRepository->cleanUp();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Verteidigungsdatensätze wurden gelöscht!");

        return SuccessResult::create("$nr alte Verteidigungseinträge gelöscht");
    }
}
