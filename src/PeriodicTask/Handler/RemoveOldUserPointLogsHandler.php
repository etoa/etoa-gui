<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldUserPointLogsTask;
use EtoA\Ranking\PointsService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldUserPointLogsHandler implements MessageHandlerInterface
{
    private PointsService $pointsService;

    public function __construct(PointsService $pointsService)
    {
        $this->pointsService = $pointsService;
    }

    public function __invoke(RemoveOldUserPointLogsTask $task): SuccessResult
    {
        $nr = $this->pointsService->cleanupUserPoints();

        return SuccessResult::create("$nr alte Userpunkte-Logs gelöscht");
    }
}
