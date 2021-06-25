<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

use EtoA\Core\Configuration\ConfigurationService;

class GameLog extends BaseLog
{
    // Facilities

    /**
     * Others
     */
    const F_OTHER = 0;

    /**
     * Buildings 1
     */
    const F_BUILD = 1;
    const F_TECH = 2;
    const F_SHIP = 3;
    const F_DEF = 4;
    const F_QUESTS = 5;

    static public $facilities = array(
        "Sonstiges",
        "Gebäude",
        "Forschungen",
        "Schiffe",
        "Verteidigungsanlagen",
        "Quests",
    );

    private GameLogRepository $repository;
    private ConfigurationService $config;
    private Log $log;

    public function __construct(
        GameLogRepository $repository,
        ConfigurationService $config,
        Log $log
    ) {
        $this->repository = $repository;
        $this->config = $config;
        $this->log = $log;
    }

    public function add(
        $facility,
        $severity,
        $msg,
        $userId,
        $allianceId,
        $entityId,
        $objectId = 0,
        $status = 0,
        $level = 0
    ): void {
        if (!is_numeric($facility) || $facility < 0 || $facility > 5) {
            $facility = self::F_OTHER;
        }
        if (!is_numeric($severity) || $severity < 0 || $severity > 4) {
            $severity = self::INFO;
        }
        if ($severity > self::DEBUG || isDebugEnabled()) {
            //Speichert Log
            $this->repository->addToQueue(
                $facility,
                $severity,
                $msg,
                $_SERVER['REMOTE_ADDR'],
                intval($userId),
                intval($allianceId),
                intval($entityId),
                intval($objectId),
                intval($status),
                intval($level)
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

        $this->log->add(
            Log::F_SYSTEM,
            Log::INFO,
            "$nr Game-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!"
        );
        return $nr;
    }
}
