<?PHP

use EtoA\Core\Configuration\ConfigurationService;

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
    static function removeOld($threshold=0)
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app['etoa.config.service'];

        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $config->getInt('log_threshold_days'));

        $nr = Log::cleanup($timestamp);
        $nr+= GameLog::cleanup($timestamp);
        $nr+= FleetLog::cleanup($timestamp);
        $nr+= BattleLog::cleanup($timestamp);

        Log::add(Log::F_SYSTEM, Log::INFO, "$nr Logs die älter als ".date("d.m.Y H:i", $timestamp)." sind wurden gelöscht!");
        return $nr;
    }
}
?>
