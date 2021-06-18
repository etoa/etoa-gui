<?php
/**
 * Description of net
 *
 * @author Nicolas
 */
class Net {

	private static array $hostCache = [];
	private static array $ipCache = [];

	/**
	* Returns the hostname of the given ip address
	* Lookup results are cached in a static array and used
	* if this function is used multiple times with the same ip. Additionally
	* the values are stored in a memory database table for faster lookups. This records
	* expire after one day.
	*/
	static function getHost($ip)
	{
		if (isset(self::$hostCache[$ip]))
			return self::$hostCache[$ip];

		$t = time();
		$res = dbquery("
		SELECT
			host
		FROM
			hostname_cache
		WHERE
			addr='".$ip."'
			AND timestamp>".($t-86400)."
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			$host = $arr[0];
			self::$hostCache[$ip] = $host;
			return $host;
		}

		$host = @gethostbyaddr($ip);
		self::$hostCache[$ip] = $host;
		dbquery("
		REPLACE INTO
			hostname_cache
		(
		  addr,
			host,
			timestamp
		)
		VALUES
		(
			'".$ip."',
			'".$host."',
			".$t."
		);
		");
		return $host;
	}

	/**
	*
	*/
	static function getAddr($host)
	{
		if (isset(self::$ipCache[$host]))
			return self::$ipCache[$host];

		$t = time();
		$res = dbquery("
		SELECT
			addr
		FROM
			hostname_cache
		WHERE
			host='".$host."'
			AND timestamp>".($t-86400)."
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			$ip = $arr[0];
			self::$ipCache[$host] = $ip;
			return $ip;
		}

		$ip = @gethostbyname($host);
		self::$ipCache[$host] = $ip;
		dbquery("
		REPLACE INTO
			hostname_cache
		(
		  addr,
			host,
			timestamp
		)
		VALUES
		(
			'".$ip."',
			'".$host."',
			".$t."
		);
		");
		return $ip;
	}

	static function clearCache()
	{
		dbquery("
		DELETE FROM
			hostname_cache
		WHERE
			timestamp<".(time()-86400)."
		");
	}

}
?>
