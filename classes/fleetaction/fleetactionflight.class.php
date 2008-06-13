<?PHP

	class fleetActionFlight extends fleetAction
	{

		function fleetActionFlight()
		{
			$this->code = "flight";
			$this->name = "Flug";
			$this->desc = "Fliegt zum Ziel, kehrt dort sofort um und fliegt wieder zurück.";
			
			$this->attitude = 0;
			
			$this->targetPlayerEntities = true;
			$this->targetOwnEntities = true;
			$this->targetNpcEntities = true;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>