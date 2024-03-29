<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;

class UserSessionManager
{
    private UserSessionRepository $repository;
    private ConfigurationService $config;
    private UserRepository $userRepository;
    private LogRepository $logRepository;

    public function __construct(
        UserSessionRepository $repository,
        ConfigurationService $config,
        UserRepository $userRepository,
        LogRepository $logRepository
    ) {
        $this->repository = $repository;
        $this->config = $config;
        $this->userRepository = $userRepository;
        $this->logRepository = $logRepository;
    }

    public function unregisterSession(string $sessionId = null, bool $logoutPressed = true): void
    {
        if ($sessionId == null) {
            $sessionId = session_id();
        }

        $userSession = $this->repository->find($sessionId);
        if ($userSession != null) {
            $this->repository->addSessionLog($userSession, $logoutPressed ? time() : 0);
            $this->repository->remove($sessionId);
            $this->userRepository->setLogoutTime($userSession->userId);
        }
        if ($logoutPressed) {
            session_regenerate_id(true);
            session_destroy();
        }
    }

    public function cleanup(): void
    {
        $sessions = $this->repository->findByTimeout($this->config->getInt('user_timeout'));
        foreach ($sessions as $session) {
            $this->unregisterSession($session->id, false);
        }
    }

    public function cleanupLogs(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->param1Int('sessionlog_store_days'));

        $count = $this->repository->removeSessionLogs($timestamp);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$count Usersession-Logs die älter als " . date("d.m.Y, H:i", $timestamp) . " sind wurden gelöscht.");

        return $count;
    }

    public function kick(string $sessionId): void
    {
        $this->unregisterSession($sessionId, false);
    }
}
