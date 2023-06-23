<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\BattleLogRepository;
use EtoA\Log\FleetLogRepository;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldLogsTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldLogsHandler implements MessageHandlerInterface
{
    private ConfigurationService $config;
    private LogRepository $logRepository;
    private GameLogRepository $gameLogRepository;
    private FleetLogRepository $fleetLogRepository;
    private BattleLogRepository $battleLogRepository;

    public function __construct(ConfigurationService $config, LogRepository $logRepository, GameLogRepository $gameLogRepository, FleetLogRepository $fleetLogRepository, BattleLogRepository $battleLogRepository)
    {
        $this->config = $config;
        $this->logRepository = $logRepository;
        $this->gameLogRepository = $gameLogRepository;
        $this->fleetLogRepository = $fleetLogRepository;
        $this->battleLogRepository = $battleLogRepository;
    }

    public function __invoke(RemoveOldLogsTask $task): SuccessResult
    {
        $time = time();
        $timestamp = $task->threshold > 0
            ? $time - $task->threshold
            : $time - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $nr = $this->logRepository->cleanup($timestamp);
        $nr += $this->gameLogRepository->cleanup($timestamp);
        $nr += $this->fleetLogRepository->cleanup($timestamp);
        $nr += $this->battleLogRepository->cleanup($timestamp);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return SuccessResult::create("$nr alte Logs gelöscht");
    }
}
