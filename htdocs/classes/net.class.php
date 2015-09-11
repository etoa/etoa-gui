<?php
/**
 * Description of net
 *
 * @author Nicolas
 */
class Net {

	/**
	* Returns the hostname of the given ip address
	* Lookup results are cached in a static array and used
	* if this function is used multiple times with the same ip. Additionally
	* the values are stored in a memory database table for faster lookups. This records
	* expire after one day.
	*/
	static function getHost($ip)
	{
		if (!isset($hostcache))
			static $hostcache = array();

		if (isset($hostcache[$ip]))
			return $hostcache[$ip];

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
			$hostcache[$ip] = $host;
			return $host;
		}

		$host = @gethostbyaddr($ip);
		$hostcache[$ip] = $host;
		$res = dbquery("
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
		if (!isset($ipcache))
			static $ipcache = array();

		if (isset($ipcache[$host]))
			return $ipcache[$host];

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
			$ipcache[$host] = $ip;
			return $ip;
		}

		$ip = @gethostbyname($host);
		$ipcache[$host] = $ip;
		$res = dbquery("
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
		$res = dbquery("
		DELETE FROM
			hostname_cache
		WHERE
			timestamp<".(time()-86400)."
		");
	}

}
?>
