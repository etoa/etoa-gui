<?PHP

	class FleetActionBombard extends FleetAction
	{

		function FleetActionBombard()
		{
			$this->code = "bombard";
			$this->name = "Bombardieren";
			$this->desc = "Bombardiert das Ziel um ein Gebäude zu beschädigen.";
			
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