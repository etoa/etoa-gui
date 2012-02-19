<?PHP
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

	abstract static function processQueue();
	abstract static function cleanup($threshold);
	
	/**
	* Alle alten Logs löschen
	*/
	static function removeOld($threshold=0)
	{
		$cfg = Config::getInstance();
		if ($threshold>0)
			$tstamp = time() - $threshold;
		else
			$tstamp = time() - (24*3600*$cfg->get('log_threshold_days'));

		$nr = Log::cleanup($tstamp);
		$nr+= GameLog::cleanup($tstamp);
		$nr+= FleetLog::cleanup($tstamp);
		$nr+= BattleLog::cleanup($tstamp);

		Log::add(Log::F_SYSTEM, Log::INFO, "$nr Logs die älter als ".date("d.m.Y H:i", $tstamp)." sind wurden gelöscht!");
		return $nr;
	}		
}
?>