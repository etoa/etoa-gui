<?PHP
	/**
	* Update market resource rates
	*/
	class MarketrateUpdateTask implements IPeriodicTask 
	{
		function run()
		{
			MarketHandler::updateRates();
			return "Markt-Raten aktualisiert";
		}
		
		function getDescription() {
			return "Markt-Ressourcen Verhältnisse aktualisieren";
		}
	}
?>