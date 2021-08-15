<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\BattleLogRepository;
use EtoA\Log\FleetLogRepository;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogRepository;

abstract class BaseLog
{
    protected static $table;
    protected static $queueTable;

    // Severities

    /**
     * Debug message
     */
    const DEBUG = 0;
    /**
     * Information
     */
    const INFO = 1;
    /**
     * Warning
     */
    const WARNING = 2;
    /**
     * Error
     */
    const ERROR = 3;
    /**
     * Critical error
     */
    const CRIT = 4;

    static public $severities = array("Debug", "Information", "Warnung", "Fehler", "Kritisch");

    /**
     * Alle alten Logs löschen
     */
    static function removeOld($threshold = 0)
    {
        // TODO
        global $app;

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];
        /** @var LogRepository $logRepository */
        $logRepository = $app[LogRepository::class];
        /** @var GameLogRepository $gameLogRepository */
        $gameLogRepository = $app[GameLogRepository::class];
        /** @var FleetLogRepository $fleetLogRepository */
        $fleetLogRepository = $app[FleetLogRepository::class];
        /** @var BattleLogRepository $battleLogRepository */
        $battleLogRepository = $app[BattleLogRepository::class];

        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $config->getInt('log_threshold_days'));

        $nr = $logRepository->cleanup($timestamp);
        $nr += $gameLogRepository->cleanup($timestamp);
        $nr += $fleetLogRepository->cleanup($timestamp);
        $nr += $battleLogRepository->cleanup($timestamp);

        Log::add(Log::F_SYSTEM, Log::INFO, "$nr Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");
        return $nr;
    }
}
