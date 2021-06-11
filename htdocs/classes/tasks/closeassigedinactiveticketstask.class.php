<?PHP
	/**
	* Close open tickets that are answered by an admin and are inactive
	*/
	class CloseAssigedInactiveTicketsTask implements IPeriodicTask
	{
		function run()
		{
			Ticket::closeAssigedInactive();
			return "Inaktive Tickes geschlossen";
		}

		function getDescription() {
			return "Inaktive Tickets schliessen welche von einem Admin beantwortet wurden";
		}
	}
?>