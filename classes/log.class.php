<?PHP

	class Log
	{
		const DEBUG = 0;
		const INFO = 1;
		const WARNING = 2;
		const ERROR = 3;
		const CRIT = 4;

		const F_OTHER = 0;
		const F_BATTLE = 1;
		const F_INSULT = 2;
		const F_USER = 3;
		const F_SYSTEM = 4;
		const F_ALLIANCE = 5;
		const F_GALAXY = 6;
		const F_MARKET = 7;
		const F_ADMIN = 8;
		const F_MULTICHEAT = 9;
		const F_MULTITRADE = 10;
		const F_SHIPTRADE = 11;
		const F_RECYCLING = 12;
		const F_FLEETACTION = 13;
		const F_ECONOMY = 14;
		const F_UPDATES = 15;
		const F_SHIPS = 16;
		const F_RANKING = 17;

		static public $severities = array("Debug","Information","Warnung","Fehler","Kritisch");
		static public $facilities = array(
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
		);

		static function add($facility,$severity,$msg)
		{
			if (!is_numeric($facility) || $facility < 0 || $facility > 17)
			{
				$facility = self::F_OTHER;
			}
			if (!is_numeric($severity) || $severity < 0 || $severity > 17)
			{
				$severity = self::INFO;
			}
			echo "$severity > ".self::DEBUG." || ".ETOA_DEBUG."==1";
			if ($severity > self::DEBUG || ETOA_DEBUG==1)
			{
				dbquery("
				INSERT INTO
					logs
				(
					facility,
					severity,
					timestamp,
					ip,
					message
				)
				VALUES
				(
					'".$facility."',
					'".$severity."',
					'".time()."',
					'".$_SERVER['REMOTE_ADDR']."',
					'".addslashes($msg)."'
				);");
			}
		}
		
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
					log_timestamp<'".$tstamp."'
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
					logs_game_timestamp<'".$tstamp."'
			");			
			$nr += mysql_affected_rows();
			
			
			
			add_log("4","$nr Logs die älter als ".date("d.m.Y H:i",$tstamp)." sind wurden gelöscht!",time());
			return $nr;
		} 
		
		
			
	}

?>