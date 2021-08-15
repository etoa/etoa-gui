<?PHP

use EtoA\Log\LogSeverity;

class GameLog extends BaseLog
{
    protected static $table = "logs_game";

    static function add($facility, $severity, $msg, $userId, $allianceId, $entityId, $objectId = 0, $status = 0, $level = 0)
    {
        if (!is_numeric($facility) || $facility < 0 || $facility > 5) {
            $facility = \EtoA\Log\GameLogFacility::OTHER;
        }
        if (!is_numeric($severity) || $severity < 0 || $severity > 4) {
            $severity = LogSeverity::INFO;
        }
        if ($severity > LogSeverity::DEBUG || isDebugEnabled()) {
            //Speichert Log
            dbquery("
			INSERT DELAYED INTO
				" . self::$table . "
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
				" . $facility . ",
				" . $severity . ",
				'" . time() . "',
				'" . addslashes($msg) . "',
				'" . $_SERVER['REMOTE_ADDR'] . "',
				'" . intval($userId) . "',
				'" . intval($allianceId) . "',
				'" . intval($entityId) . "',
				'" . intval($objectId) . "',
				'" . intval($status) . "',
				'" . intval($level) . "'
			);");
        }
    }
}
