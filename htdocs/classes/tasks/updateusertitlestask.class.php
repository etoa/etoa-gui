<?PHP
	/**
	* Update user titles
	*/
	class UpdateUserTitlesTask implements IPeriodicTask
	{
		function run()
		{
			if (ENABLE_USERTITLES==1) {
				Ranking::calcTitles();
				return "User Titel aktualisiert";
			}
			return "User Titel nicht aktualisiert (deaktiviert)";
		}

		function getDescription() {
			return "Titel aktualisieren";
		}
	}
?>