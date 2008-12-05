<?PHP

	class Log
	{
		/**
		* Alte Logs löschen
		*/
		static function removeOld($threshold=0)
		{
			$cfg = Config::getInstance();
			if ($threshold>0)
				$tstamp = time() - $threshold;
			else
				$tstamp = time() - (24*3600*$cfg->get('log_threshold_days'));
			dbquery("
				DELETE FROM
					logs
				WHERE
					log_timestamp<'".$tstamp."'
			");
			$nr = mysql_affected_rows();
			dbquery("
				DELETE FROM
					logs_battle 
				WHERE
					logs_battle_time<'".$tstamp."'
			");			
			$nr += mysql_affected_rows();
			dbquery("
				DELETE FROM
					logs_game 
				WHERE
					logs_game_timestamp<'".$tstamp."'
			");			
			$nr += mysql_affected_rows();
			
			
			
			add_log("4","$nr Logs die älter als ".date("d.m.Y H:i",$tstamp)." sind wurden gelöscht!",time());
			return $nr;
		} 
		
		
			
	}

?>