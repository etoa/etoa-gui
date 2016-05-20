<?PHP
class GameLog extends BaseLog
{
	protected static $table = "logs_game";
	protected static $queueTable = "logs_game_queue";

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

	static function add($facility, $severity, $msg,$userId, $allianceId, $entityId, $objectId=0, $status=0, $level=0)
	{
		if (!is_numeric($facility) || $facility < 0 || $facility > 4)
		{
			$facility = self::F_OTHER;
		}
		if (!is_numeric($severity) || $severity < 0 || $severity > 4)
		{
			$severity = self::INFO;
		}
		if ($severity > self::DEBUG || isDebugEnabled())
		{
			//Speichert Log
			dbquery("
			INSERT DELAYED INTO
				".self::$queueTable."
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
	
	/**
	* Processes the log queue and stores 
	* all items in the persistend log table
	*/
	static function processQueue()	{
		dbquery("
		INSERT INTO
			".self::$table."
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
		SELECT 
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
		FROM
			".self::$queueTable."
		;");
		$numRecords = mysql_affected_rows();
		if ($numRecords > 0)	{
			dbquery("
			DELETE FROM
				".self::$queueTable."				
			LIMIT
				".$numRecords.";");
		}
		return $numRecords;
	}
	
	/**
	* Removes up old logs from the persistend log table
	*
	* @param $timestamp string All items older than this time threshold will be deleted
	*/
	static function cleanup($threshold)
	{
		dbquery("
			DELETE FROM
				".self::$table."
			WHERE
				timestamp<'".$threshold."'
		");
		return mysql_affected_rows();
	}	
}
?>