<?php
class RuntimeDataStore
{
	public static function get($key)
	{
		$res = dbQuerySave('
		SELECT
			data_value
		FROM
			runtime_data
		WHERE
			data_key=?
		;', [
			$key
		]);
		if ($arr = mysql_fetch_row($res))
		{
			return $arr[0];
		}
		return null;
	}
	
	public static function set($key, $value)
	{
		dbQuerySave('
		REPLACE INTO
			runtime_data
		(
			data_key,
			data_value
		)
		VALUES
		(
			?,
			?
		)
		;', [
			$key,
			$value
		]);
	}
}
?>