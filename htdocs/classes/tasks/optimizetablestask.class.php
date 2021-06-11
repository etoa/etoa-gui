<?PHP
	/**
	* Optimize tables
	*/
	class OptimizeTablesTask implements IPeriodicTask
	{
		function run()
		{
			DBManager::getInstance()->optimizeTables();
			return "Tabellen optimiert";
		}

		function getDescription() {
			return "Tabellen optimieren";
		}
	}
?>