<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

use EtoA\Core\Configuration\ConfigurationService;

class Log extends BaseLog
{
    // Facilities

    /**
     * Others
     */
    const F_OTHER = 0;
    /**
     * Battle Logs 1
     * Todo: deprecated
     */
    const F_BATTLE = 1;
    /**
     * Insulting messages 2
     * Todo: Check use
     */
    const F_INSULT = 2;
    /**
     * User actions 3
     */
    const F_USER = 3;
    /**
     * System 4
     */
    const F_SYSTEM = 4;
    /**
     * Alliacen 5
     */
    const F_ALLIANCE = 5;
    /**
     * Galaxy 6
     */
    const F_GALAXY = 6;
    /**
     * Market 7
     */
    const F_MARKET = 7;
    /**
     * Admin 8
     */
    const F_ADMIN = 8;
    /**
     * Multi cheat 9
     */
    const F_MULTICHEAT = 9;
    /**
     * Multitrade 10
     */
    const F_MULTITRADE = 10;
    /**
     * Shiptrade 11
     */
    const F_SHIPTRADE = 11;
    /**
     * Recycling 12
     */
    const F_RECYCLING = 12;
    /**
     * Fleetaction 13
     */
    const F_FLEETACTION = 13;
    /**
     * Economy 14
     */
    const F_ECONOMY = 14;
    /**
     * Updates 15
     */
    const F_UPDATES = 15;
    /**
     * Ships 16
     */
    const F_SHIPS = 16;
    /**
     * Ranking 17
     */
    const F_RANKING = 17;
    /**
     * Illegal user action (bots, wrong referrers etc)
     */
    const F_ILLEGALACTION = 18;

    public static $facilities = [
        "Sonstiges",
        "Kampfberichte",
        "Beleidigungen",
        "User",
        "System",
        "Allianzen",
        "Galaxie",
        "Markt",
        "Administration",
        "Multi-Verstoss",
        "Multi-Handel",
        "Schiffshandel",
        "Recycling",
        "Flottenaktionen",
        "Wirtschaft",
        "Updates",
        "Schiffe",
        "Ranglisten",
        "Illegale Useraktion",
    ];

    private LogRepository $repository;
    private ConfigurationService $config;

    public function __construct(
        LogRepository $repository,
        ConfigurationService $config
    ) {
        $this->repository = $repository;
        $this->config = $config;
    }

    public function add(int $facility, int $severity, string $msg): void
    {
        if ($facility < 0 || $facility > 18) {
            $facility = self::F_OTHER;
        }
        if ($severity < 0 || $severity > 4) {
            $severity = self::INFO;
        }
        if ($severity > self::DEBUG || isDebugEnabled()) {
            $this->repository->addToQueue(
                $facility,
                $severity,
                $msg,
                isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''
            );
        }
    }

    /**
     * Processes the log queue and stores
     * all items in the persistend log table
     */
    public function processQueue(): int
    {
        return $this->repository->addLogsFromQueue();
    }

    /**
     * Removes up old logs from the persistend log table
     *
     * @param int $threshold All items older than this time threshold will be deleted
     */
    public function cleanup(int $threshold): int
    {
        return $this->repository->removeByTimestamp($threshold);
    }

    /**
     * Alle alten Logs löschen
     */
    public function removeOld(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->getInt('log_threshold_days'));

        $nr = $this->cleanup($timestamp);

        $this->add(
            Log::F_SYSTEM,
            Log::INFO,
            "$nr Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!"
        );

        return $nr;
    }
}
