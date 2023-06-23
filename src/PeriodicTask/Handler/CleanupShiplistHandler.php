<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CleanupShiplistTask;
use EtoA\Ship\ShipRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CleanupShiplistHandler implements MessageHandlerInterface
{
    private ShipRepository $shipRepository;
    private LogRepository $logRepository;

    public function __construct(ShipRepository $shipRepository, LogRepository $logRepository)
    {
        $this->shipRepository = $shipRepository;
        $this->logRepository = $logRepository;
    }

    public function __invoke(CleanupShiplistTask $task): SuccessResult
    {
        $nr = $this->shipRepository->cleanUp();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Schiffsdatensätze wurden gelöscht!");

        return SuccessResult::create("$nr alte Schiffseinträge gelöscht");
    }
}
