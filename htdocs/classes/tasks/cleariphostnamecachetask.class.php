<?PHP
	/**
	* Remove old ip-hostname combos from cache
	*/
	class ClearIPHostnameCacheTask implements IPeriodicTask
	{
		function run()
		{
			Net::clearCache();
			return "IP/Hostname Cache gelöscht";
		}

		function getDescription() {
			return "Alte IP/Hostnamen Mappings aus Cache löschen";
		}
	}
?>