<?PHP

	class FleetActionSpyattack extends FleetAction
	{

		function FleetActionSpyattack()
		{
			$this->code = "spyattack";
			$this->name = "Spionageangriff";
			$this->desc = "Stiehlt eine Technologie vom Ziel.";
			
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