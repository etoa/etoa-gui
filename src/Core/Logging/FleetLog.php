<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

use EtoA\Core\Configuration\ConfigurationService;

class FleetLog extends BaseLog
{
    /**
     * Others
     */
    const F_OTHER = 0;
    /**
     * Launch
     */
    const F_LAUNCH = 1;
    /**
     * Cancel
     */
    const F_CANCEL = 2;
    /**
     * Action
     */
    const F_ACTION = 3;
    /**
     * Return
     */
    const F_RETURN = 4;

    static public $facilities = [
        "Sonstige",
        "Start",
        "Abbruch",
        "Aktion",
        "Rückkehr"
    ];

    private FleetLogRepository $repository;
    private ConfigurationService $config;
    private Log $log;

    public function __construct(
        FleetLogRepository $repository,
        ConfigurationService $config,
        Log $log
    ) {
        $this->repository = $repository;
        $this->config = $config;
        $this->log = $log;
    }

    function __destruct()
    {
        $entry = new FleetLogEntry();
        if ($entry->launched) {
            $entry->text = "Treibstoff: " . $entry->fuel . " Nahrung: " . $entry->food . " Piloten: " . $entry->pilots;
            $this->repository->addToQueue($entry);
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
            Log::F_FLEETACTION,
            Log::INFO,
            "$nr Fleet-Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!"
        );
        return $nr;
    }
}
