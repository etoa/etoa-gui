<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\Configuration\ConfigurationService;

class LogCleanup
{
    public function __construct(
        private readonly ConfigurationService $config,
        private readonly LogRepository        $logRepository,
        private readonly GameLogRepository    $gameLogRepository,
        private readonly FleetLogRepository   $fleetLogRepository,
        private readonly BattleLogRepository  $battleLogRepository,
    ) {
    }

    public function cleanup(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $nr = $this->logRepository->cleanup($timestamp);
        $nr += $this->gameLogRepository->cleanup($timestamp);
        $nr += $this->fleetLogRepository->cleanup($timestamp);
        $nr += $this->battleLogRepository->cleanup($timestamp);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $nr;
    }
}
