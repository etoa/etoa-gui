<?PHP

use EtoA\Log\LogFacility;
use EtoA\Log\LogSeverity;

class Log extends BaseLog
{
    protected static $table = "logs";

    /**
     * Adds a log message to the log queue
     *
     * @param int|string $facility Event facility
     * @param int|string $severity Event severity
     * @param string $msg  Log Message
     */
    static function add($facility, $severity, $msg)
    {
        if (!is_numeric($facility) || $facility < 0 || $facility > 18) {
            $facility = LogFacility::OTHER;
        }
        if (!is_numeric($severity) || $severity < 0 || $severity > 4) {
            $severity = LogSeverity::INFO;
        }
        if ($severity > LogSeverity::DEBUG || isDebugEnabled()) {
            dbquery("
			INSERT DELAYED INTO
				" . self::$table . "
			(
				facility,
				severity,
				timestamp,
				ip,
				message
			)
			VALUES
			(
				'" . $facility . "',
				'" . $severity . "',
				'" . time() . "',
				'" . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') . "',
				'" . mysql_real_escape_string($msg) . "'
			);");
        }
    }
}
