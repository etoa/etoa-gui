<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CleanupShiplistTask;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CleanupShiplistHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly ShipListRepository $shipListRepository,
        private readonly LogRepository $logRepository)
    {}

    public function __invoke(CleanupShiplistTask $task): SuccessResult
    {
        $nr = $this->shipListRepository->cleanUp();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Schiffsdatensätze wurden gelöscht!");

        return SuccessResult::create("$nr alte Schiffseinträge gelöscht");
    }
}
