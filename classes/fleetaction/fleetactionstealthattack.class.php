<?PHP

	class FleetActionStealthattack extends FleetAction
	{

		function FleetActionStealthattack()
		{
			$this->code = "stealthattack";
			$this->name = "Tarnangriff";
			$this->desc = "Greift das Ziel getarnt an und klaut Rohstoffe";
			
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