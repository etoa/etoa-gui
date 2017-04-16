<?PHP
	/**
	* Generate and store game statistics
	*/
	class GenerateGameStatsTask implements IPeriodicTask 
	{		
		function run()
		{
			GameStats::generateAndSave(GAMESTATS_FILE);
			return "Spielstatistiken erstellt";
		}
		
		function getDescription() {
			return "Spielstatistiken generieren und speichern";
		}
	}
?>