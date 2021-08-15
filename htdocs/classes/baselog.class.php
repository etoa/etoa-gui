<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\BattleLogRepository;
use EtoA\Log\FleetLogRepository;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;

abstract class BaseLog
{
    protected static $table;
    protected static $queueTable;

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

        $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr Logs die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");
        return $nr;
    }
}
