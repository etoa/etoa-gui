<?PHP

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

		static public $facilities = array(
		"Sonstiges",
		"Gebäude",
		"Forschungen",
		"Schiffe",
		"Verteidigungsanlagen",
		);

		static function add($facility,$severity,$msg,$userId,$allianceId,$entityId,$objectId=0,$status=0,$level=0)
		{
			if (!is_numeric($facility) || $facility < 0 || $facility > 4)
			{
				$facility = self::F_OTHER;
			}
			if (!is_numeric($severity) || $severity < 0 || $severity > 4)
			{
				$severity = self::INFO;
			}
			if ($severity > self::DEBUG || Config::getInstance()->debug->v==1)
			{
				//Speichert Log
				dbquery("
				INSERT INTO
				logs_game
				(
					facility,
					severity,
					timestamp,
					message,
					ip,
					user_id,
					alliance_id,
					entity_id,
					object_id,
					status,
					level
				)
				VALUES
				(
					".$facility.",
					".$severity.",
					'".time()."',
					'".addslashes($msg)."',
					'".$_SERVER['REMOTE_ADDR']."',
					'".intval($userId)."',
					'".intval($allianceId)."',
					'".intval($entityId)."',
					'".intval($objectId)."',
					'".intval($status)."',
					'".intval($level)."'
				);");


			}
		}
		
// todo: add cleanup
		
		
			
	}

?>