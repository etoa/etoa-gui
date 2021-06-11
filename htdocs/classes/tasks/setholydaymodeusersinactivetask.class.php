<?PHP
	/**
	* Set users in holyday mode to inactive after threshold has been passed
	*/
	class SetHolydayModeUsersInactiveTask implements IPeriodicTask
	{
		function run()
		{
			if (Config::getInstance()->p2('hmode_days'))
			{
				$nr = Users::setUmodToInactive();
				return "$nr User aus Urlaubsmodus in Inaktivität gesetzt";
			}
			return null;
		}

		function getDescription() {
			return "Benutzer aus Urlaub inaktiv setzen";
		}
	}
?>