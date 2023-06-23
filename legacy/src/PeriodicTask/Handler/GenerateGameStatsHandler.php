<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\GenerateGameStatsTask;
use EtoA\Ranking\GameStatsGenerator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GenerateGameStatsHandler implements MessageHandlerInterface
{
    private GameStatsGenerator $gameStatsGenerator;

    public function __construct(GameStatsGenerator $gameStatsGenerator)
    {
        $this->gameStatsGenerator = $gameStatsGenerator;
    }

    public function __invoke(GenerateGameStatsTask $task): SuccessResult
    {
        $this->gameStatsGenerator->generateAndSave();

        return SuccessResult::create("Spielstatistiken erstellt");
    }
}
