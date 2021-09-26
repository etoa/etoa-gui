<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldAlliancePointLogsTask;
use EtoA\Ranking\PointsService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldAlliancePointLogsHandler implements MessageHandlerInterface
{
    private PointsService $pointsService;

    public function __construct(PointsService $pointsService)
    {
        $this->pointsService = $pointsService;
    }

    public function __invoke(RemoveOldAlliancePointLogsTask $task): SuccessResult
    {
        $nr = $this->pointsService->cleanupAlliancePoints();

        return SuccessResult::create("$nr alte Allianzpunkte-Logs gelöscht");
    }
}
