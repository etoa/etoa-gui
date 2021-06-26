<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;
use Log;

class PointsService
{
    private ConfigurationService $config;
    private UserRepository $userRepo;
    private AllianceRepository $allianceRepo;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepo,
        AllianceRepository $allianceRepo
    ) {
        $this->config = $config;
        $this->userRepo = $userRepo;
        $this->allianceRepo = $allianceRepo;
    }

    public function cleanupUserPoints(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $affected = $this->userRepo->removePointsByTimestamp($timestamp);

        Log::add(Log::F_SYSTEM, Log::INFO, "$affected Userpunkte-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $affected;
    }

    public function cleanupAlliancePoints(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $affected = $this->allianceRepo->removePointsByTimestamp($timestamp);

        Log::add(Log::F_SYSTEM, Log::INFO, "$affected Allianzpunkte-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $affected;
    }
}
