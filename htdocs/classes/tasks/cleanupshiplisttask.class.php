<?PHP
	/**
	* Remove old ship build list records
	*/
	class CleanupShiplistTask implements IPeriodicTask
	{
		function run()
		{
			$nr = ShipList::cleanUp();
			return "$nr alte Schiffseinträge gelöscht";
		}

		function getDescription() {
			return "Alte Schiffbaudatensätze löschen";
		}
	}
?>