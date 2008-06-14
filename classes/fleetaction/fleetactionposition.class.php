<?PHP

	class FleetActionPosition extends FleetAction
	{

		function FleetActionPosition()
		{
			$this->code = "position";
			$this->name = "Stationieren";
			$this->desc = "Fliegt zum Ziel und stationiert sich dort.";
			
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