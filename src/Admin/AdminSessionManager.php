<?php

declare(strict_types=1);

namespace EtoA\Admin;

use EtoA\Core\Configuration\ConfigurationService;

class AdminSessionManager
{
    private AdminSessionRepository $repository;
    private ConfigurationService $config;

    public function __construct(
        AdminSessionRepository $repository,
        ConfigurationService $config
    ) {
        $this->repository = $repository;
        $this->config = $config;
    }

    /**
     * Removes old session logs from the database
     *
     * @param int $threshold Time difference in seconds
     */
    public function cleanupLogs(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->param2Int('sessionlog_store_days'));

        $count = $this->repository->removeSessionLogs($timestamp);

        \Log::add(\Log::F_SYSTEM, \Log::INFO, "$count Admin-Session-Logs die älter als " . date("d.m.Y, H:i", $timestamp) . " sind wurden gelöscht.");

        return $count;
    }

    /**
     * Kicks the user with the given session id
     *
     * @param string $sid Session id
     */
    public function kick(string $sid): void
    {
        $this->unregisterSession($sid, false);
    }

    /**
     * Unregisters a session and save session to session-log
     *
     * @param string $sid Session-ID.
     * @param bool $logoutPressed True if it was manual logout
     */
    public function unregisterSession(string $sid, bool $logoutPressed = true): void
    {
        $adminSession = $this->repository->find($sid);
        if ($adminSession != null) {
            $this->repository->addSessionLog($adminSession, $logoutPressed ? time() : 0);
            $this->repository->remove($sid);
        }
        if ($logoutPressed) {
            session_regenerate_id(true);
            session_destroy();
        }
    }

    /**
     * Cleans up sessions with have a timeout. Should be called at login or by cronjob regularly
     */
    public function cleanup(): void
    {
        $sessions = $this->repository->findByTimeout($this->config->getInt('admin_timeout'));
        foreach ($sessions as $session) {
            $this->unregisterSession($session['id'], false);
        }
    }
}
