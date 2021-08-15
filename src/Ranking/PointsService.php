<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\User\UserRepository;

class PointsService
{
    private ConfigurationService $config;
    private UserRepository $userRepo;
    private AllianceRepository $allianceRepo;
    private LogRepository $logRepository;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepo,
        AllianceRepository $allianceRepo,
        LogRepository $logRepository
    ) {
        $this->config = $config;
        $this->userRepo = $userRepo;
        $this->allianceRepo = $allianceRepo;
        $this->logRepository = $logRepository;
    }

    public function cleanupUserPoints(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $affected = $this->userRepo->removePointsByTimestamp($timestamp);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$affected Userpunkte-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $affected;
    }

    public function cleanupAlliancePoints(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $affected = $this->allianceRepo->removePointsByTimestamp($timestamp);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$affected Allianzpunkte-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $affected;
    }
}
