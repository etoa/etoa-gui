<?PHP

	class FleetActionFetch extends FleetAction
	{

		function FleetActionFetch()
		{
			$this->code = "fetch";
			$this->name = "Waren abholen";
			$this->desc = "Fliegt zum Ziel und holt dort Waren ab.";
			
			$this->attitude = 1;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = true;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>