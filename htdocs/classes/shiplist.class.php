<?PHP

	class ShipList
	{
		/**
		* Remove empty data
		*/
		static function cleanUp()
		{
			dbquery("DELETE FROM
						`shiplist`
					WHERE
						`shiplist_count`='0'
						AND `shiplist_bunkered`='0'
						AND `shiplist_special_ship`='0'
						;");
			$nr = mysql_affected_rows();
			Log::add("4", Log::INFO, "$nr leere Schiffsdatensätze wurden gelöscht!");
			return $nr;
		}


	}


?>
