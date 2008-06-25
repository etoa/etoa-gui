<?PHP

	class FleetActionInvade extends FleetAction
	{

		function FleetActionInvade()
		{
			$this->code = "invade";
			$this->name = "Invasion";
			$this->desc = "Greift das Ziel an und versucht es zu übernehmen.";
			
			$this->attitude = 3;
			
			$this->allowPlayerEntities = true;
			$this->allowOwnEntities = false;
			$this->allowNpcEntities = false;
			$this->allowSourceEntity = false;
		}

		function startAction() {} 
		function cancelAction() {}		
		function targetAction() {} 
		function returningAction() {}		
		
	}

?>