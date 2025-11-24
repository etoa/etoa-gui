<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\User;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;

class UserSessionManager
{
    public function __construct(
        private readonly UserSessionRepository $repository,
        private readonly ConfigurationService $config,
        private readonly UserRepository $userRepository,
        private readonly LogRepository $logRepository,
        private readonly UserSessionLogRepository $userSessionLogRepository
    ) {}

    public function unregisterSession(User $user, bool $logoutPressed = true): void
    {
        if ($user->getSession() != null) {
            $this->userSessionLogRepository->addSessionLog($user->getSession(), $logoutPressed ? time() : 0);
            $user->setSession(null);
            $this->userRepository->save();
        }
    }

    public function cleanup(): void
    {
        $sessions = $this->repository->findByTimeout($this->config->getInt('user_timeout'));
        foreach ($sessions as $session) {
            $this->unregisterSession($session->getId(), false);
        }
    }

    public function cleanupLogs(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->param1Int('sessionlog_store_days'));

        $count = $this->userSessionLogRepository->removeSessionLogs($timestamp);

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$count Usersession-Logs die älter als " . date("d.m.Y, H:i", $timestamp) . " sind wurden gelöscht.");

        return $count;
    }

    public function kick(string $sessionId): void
    {
        $this->unregisterSession($sessionId, false);
    }
}
