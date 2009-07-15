<?PHP

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
			if (!is_numeric($severity) || $severity < 0 || $severity > 4)
			{
				$severity = self::INFO;
			}
			if ($severity > self::DEBUG || Config::getInstance()->debug->v==1)
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
					'".(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'')."',
					'".addslashes($msg)."'
				);");
			}
		}
		
			
	}

?>