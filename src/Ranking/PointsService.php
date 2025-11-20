<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AlliancePointsRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\User\UserPointsRepository;

class PointsService
{
    public function __construct(
        private readonly ConfigurationService $config,
        private readonly LogRepository $logRepository,
        private readonly AlliancePointsRepository $alliancePointsRepository,
        private readonly UserPointsRepository $userPointsRepository
    ) {}

    public function cleanupUserPoints(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $affected = $this->userPointsRepository->removePointsByTimestamp($timestamp);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$affected Userpunkte-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $affected;
    }

    public function cleanupAlliancePoints(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $affected = $this->alliancePointsRepository->removePointsByTimestamp($timestamp);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$affected Allianzpunkte-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $affected;
    }
}
