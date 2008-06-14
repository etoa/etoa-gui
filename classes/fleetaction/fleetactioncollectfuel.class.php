<?PHP

	class FleetActionCollectFuel extends FleetAction
	{

		function FleetActionCollectFuel()
		{
			$this->code = "collectfuel";
			$this->name = RES_FUEL."gas sammeln";
			$this->desc = RES_FUEL." aus der Atmospähre von Gasplaneten saugen";
			
			$this->attitude = 0;
			
			$this->allowPlayerEntities = false;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = true;
			$this->allowSourceEntity = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>