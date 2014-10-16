<?PHP
	/**
	* Checks and handles missile actions
	*/
	class CheckMissilesTask implements IPeriodicTask 
	{		
		function run()
		{
			$res = dbquery("
			SELECT
				flight_id
			FROM
				missile_flights
			WHERE
				flight_landtime < ".time()."
			ORDER BY
				flight_landtime ASC
			;");
			$cnt = 0;
			while ($arr = mysql_fetch_assoc($res))
			{
				MissileBattleHandler::battle($arr['flight_id']);
				$cnt++;
			}
			return "$cnt Raketen-Aktionen berechnet";
		}
		
		function getDescription() {
			return "Raketen-Aktionen berechnen";
		}
	}
?>