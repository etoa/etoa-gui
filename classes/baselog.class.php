<?PHP

	abstract class BaseLog
	{
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

		static public $severities = array("Debug","Information","Warnung","Fehler","Kritisch");

		/**
		* Alte Logs löschen
		*/
		static function removeOld($threshold=0)
		{
			$cfg = Config::getInstance();
			if ($threshold>0)
				$tstamp = time() - $threshold;
			else
				$tstamp = time() - (24*3600*$cfg->get('log_threshold_days'));
			dbquery("
				DELETE FROM
					logs
				WHERE
					timestamp<'".$tstamp."'
			");
			$nr = mysql_affected_rows();
			dbquery("
				DELETE FROM
					logs_battle
				WHERE
					logs_battle_time<'".$tstamp."'
			");
			$nr += mysql_affected_rows();
			dbquery("
				DELETE FROM
					logs_game
				WHERE
					timestamp<'".$tstamp."'
			");
			$nr += mysql_affected_rows();
			dbquery("
				DELETE FROM
					logs_fleet
				WHERE
					timestamp<'".$tstamp."'
			");
			$nr += mysql_affected_rows();



			add_log("4","$nr Logs die älter als ".date("d.m.Y H:i",$tstamp)." sind wurden gelöscht!",time());
			return $nr;
		} 
		
	}

?>