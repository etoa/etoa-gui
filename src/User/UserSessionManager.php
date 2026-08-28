<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\User;
use EtoA\Entity\UserSession;
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

    /**
     * Moves the session row of a user to a new session id. Symfony migrates the session
     * id whenever another firewall authenticates in the same browser, and the row is
     * keyed by that id.
     */
    public function migrateSession(User $user, string $newSessionId): void
    {
        $current = $user->getSession();
        if ($current === null || $current->getId() === $newSessionId) {
            return;
        }

        $session = new UserSession();
        $session
            ->setId($newSessionId)
            ->setUser($user)
            ->setIpAddr($current->getIpAddr())
            ->setUserAgent($current->getUserAgent())
            ->setTimeLogin($current->getTimeLogin())
            ->setTimeAction(time());

        $user->setSession($session);
        $this->userRepository->save();
    }

    public function cleanup(): void
    {
        $sessions = $this->repository->findByTimeout($this->config->getInt('user_timeout'));
        foreach ($sessions as $session) {
            if ($session->getUser() !== null) {
                $this->unregisterSession($session->getUser(), false);
            }
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
        $session = $this->repository->find($sessionId);
        if ($session !== null && $session->getUser() !== null) {
            $this->unregisterSession($session->getUser(), false);
        }
    }
}
