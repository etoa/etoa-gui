<?php

declare(strict_types=1);

namespace EtoA\Admin;

class AdminSessionManager
{
	private AdminSessionRepository $repository;

	function __construct(AdminSessionRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Removes old session logs from the database
     *
	 * @param int $threshold Time difference in seconds
	 */
	function cleanupLogs(int $threshold = 0): int
	{
		$cfg = \Config::getInstance();

		$timestamp = $threshold > 0
			? time() - $threshold
			: time() - (24 * 3600 * $cfg->sessionlog_store_days->p2);

		$count = $this->repository->removeSessionLogs($timestamp);

        \Log::add(\Log::F_SYSTEM, \Log::INFO, "$count Admin-Session-Logs die älter als " . date("d.m.Y, H:i", $timestamp) . " sind wurden gelöscht.");

        return $count;
	}

	/**
	 * Kicks the user with the given session id
     *
	 * @param string $sid Session id
	 */
	function kick(string $sid): void
	{
		$this->unregisterSession($sid, false);
	}

    /**
	 * Unregisters a session and save session to session-log
	 *
	 * @param string $sid Session-ID.
	 * @param bool $logoutPressed True if it was manual logout
	 */
	function unregisterSession(string $sid, bool $logoutPressed = true)
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
	function cleanup()
	{
		$cfg = \Config::getInstance();

		$sessions = $this->repository->findByTimeout((int)$cfg->admin_timeout->v);
		foreach ($sessions as $sessions) {
			$this->unregisterSession($sessions['id'], false);
		}
	}
}
