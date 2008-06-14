<?PHP

	class FleetActionSpy extends FleetAction
	{

		function FleetActionSpy()
		{
			$this->code = "spy";
			$this->name = "Ausspionieren";
			$this->desc = "Sammelt Informationen über das Ziel";
			
			$this->attitude = 2;
			
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