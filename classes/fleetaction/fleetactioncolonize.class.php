<?PHP

	class FleetActionColonize extends FleetAction
	{

		function FleetActionColonize()
		{
			$this->code = "colonize";
			$this->name = "Kolonialisieren";
			$this->desc = "Eine Basis auf dem Ziel errichten";
			
			$this->attitude = 1;
			
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