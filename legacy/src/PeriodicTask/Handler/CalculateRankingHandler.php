<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CalculateRankingTask;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CalculateRankingHandler implements MessageHandlerInterface
{
    private RankingService $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function __invoke(CalculateRankingTask $task): SuccessResult
    {
        $result = $this->rankingService->calc();

        return SuccessResult::create("Die Punkte von " . $result->numberOfUsers . " Spielern wurden aktualisiert; ein Spieler hat durchschnittlich " . StringUtils::formatNumber($result->getAveragePoints()) . " Punkte");
    }
}
