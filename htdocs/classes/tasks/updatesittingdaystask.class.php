<?PHP
	/**
	* Update user sitting days
	*/
	class UpdateSittingDaysTask implements IPeriodicTask 
	{		
		function run()
		{
			Users::addSittingDays();
			return "Sittertage aller User wurden aktualisiert";
		}
		
		function getDescription() {
			return "Sitter-Tage aktualisieren";
		}
	}
?>