<?PHP
	/**
	* Process log messages
	*/
	class ProcessLogMessagesTask implements IPeriodicTask 
	{		
		function run()
		{
			$nr = Log::processQueue();
			$nr+= GameLog::processQueue();
			$nr+= BattleLog::processQueue();
			$nr+= FleetLog::processQueue();
			return "$nr Log Nachrichten verarbeitet";
		}
		
		function getDescription() {
			return "Log-Nachrichten verarbeiten";
		}
	}
?>